<?php
class ao_AoService extends f_persistentdocument_DocumentService
{
	/**
	 * ID of the notification sent when a new AO is published.
	 */
	const NOTIFICATION_PUBLISHED_AO = 'modules_ao/notificationPublishedAO';

	/**
	 * ID of the notification sent when an AO has been updated.
	 */
	const NOTIFICATION_UPDATED_AO = 'modules_ao/notificationUpdatedAO';

	/**
	 * Session namespace used to store some history information.
	 */
	const SESSION_NAMESPACE = 'modules_ao';

	/**
	 * @var ao_AoService
	 */
	private static $instance;

	/**
	 * @return ao_AoService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return ao_persistentdocument_ao
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_ao/ao');
	}

	/**
	 * Create a query based on 'modules_ao/ao' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_ao/ao');
	}

	/**
	 * Retourne les AO rattachés au thème $themeId, dans la rubrique $parentTopic,
	 * ordonnés selon $order.
	 *
	 * @param Integer $themeId
	 * @param website_persistentdocument_topic $parentTopic
	 * @param Order $order
	 * @return Array<ao_persistentdocument_ao>
	 */
	public function getOrdered($themeId = null, $parentTopic = null, $order = null)
	{
		$query = $this->createQuery()
			->add(Restrictions::isEmpty('awardNotice'))
			->add(Restrictions::published())
			->add(Restrictions::gt('endDate', date_DateFormat::format(date_Calendar::now(), 'Y-m-d H:i:s')));
		if ( ! is_null($themeId) )
		{
			$query->add(Restrictions::eq('theme.id', $themeId));
		}
		if ( ! is_null($parentTopic) )
		{
			$query->add(Restrictions::childOf($parentTopic->getId()));
		}
		if ( $order instanceof Order )
		{
			$query->addOrder($order);
		}
		elseif ( ! is_null($order) )
		{
			Framework::debug(__METHOD__.": argument \$order must be an instance of class Order (\$order has been ignored).");
		}
		return $query->find();
	}

	/**
	 * @param Integer $themeId
	 * @param website_persistentdocument_topic $parentTopic
	 * @return Array<ao_persistentdocument_ao>
	 */
	public function getAwardNoticeArray($themeId = null, $parentTopic = null)
	{
		$query = $this->createQuery()
			->add(Restrictions::isNotEmpty('awardNotice'))
			->add(Restrictions::published());
		if ( ! is_null($themeId) )
		{
			$query->add(Restrictions::eq('theme.id', $themeId));
		}
		if ( ! is_null($parentTopic) )
		{
			$query->add(Restrictions::childOf($parentTopic->getId()));
		}
		$query->addOrder(Order::desc('emissionDate'));
		return $query->find();
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
	protected function onCorrectionActivated($document, $args)
	{
		// Parse richtext contents to find linked media.
		$this->updateLinkedMedia($document);
		$this->sendNotification(self::NOTIFICATION_UPDATED_AO, $document);
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 */
	public function onAOPublished($ao)
	{
		if ( ! $ao->getPublishedNotificationSent() )
		{
			if ($this->sendNotification(self::NOTIFICATION_PUBLISHED_AO, $ao))
			{
				$ao->setPublishedNotificationSent(true);
				f_persistentdocument_PersistentProvider::getInstance()->updateDocument($ao);
			}
		}
	}

	/**
	 * @param media_persistentdocument_media $media
	 * @param mixed $mediaId (Integer or String)
	 */
	public function onMediaDownloaded($media, $mediaId)
	{
		// Primary checks.
		if (// Is a company logged?
		    is_null($company = ao_CompanyService::getInstance()->getLoggedCompany())
		    // Is the media valid?
		    || is_null($media)
		    // Is the 'ao' parameter present?
		    || is_null($aoId = Context::getInstance()->getRequest()->getParameter('ao', null))
		    ) {
			return;
		}

		// Does the 'ao' parameter correspond to a valid AO?
		$ao = null;
		try
		{
			$ao = DocumentHelper::getDocumentInstance($aoId);
			if ( ! ($ao instanceof ao_persistentdocument_ao) )
			{
				return;
			}
		}
		catch (Exception $e)
		{
			Framework::warn("Bad 'ao' parameter: ".$e->getMessage());
			return;
		}

		$session = Context::getInstance()->getUser();
		$attr = 'viewedAo_'.$aoId.'_media_'.$media->getId();

		// Has the company already downloaded this media in the current session?
		if ( ! $session->hasAttribute($attr, self::SESSION_NAMESPACE) )
		{
			$stat = ao_StataomediaService::getInstance()->getNewDocumentInstance();
			$stat->setAo($ao);
			$stat->setCompany($company);
			$stat->setMedia($media);
			$stat->save();
			$session->setAttribute($attr, '1', self::SESSION_NAMESPACE);
		}
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 */
	public function onAoDetailView($ao)
	{
		// Is the company logged?
		if (is_null($company = ao_CompanyService::getInstance()->getLoggedCompany()))
		{
			return;
		}
		$this->updateStatsOnAOView($ao, $company);
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @param ao_persistentdocument_company $company
	 */
	protected function updateStatsOnAOView($ao, $company)
	{
		$session = Context::getInstance()->getUser();

		$attr = 'viewedAo_'.$ao->getId();

		// Has the company already viewed this AO in the current session?
		if ( ! $session->hasAttribute($attr, self::SESSION_NAMESPACE) )
		{
			$stat = ao_StataoService::getInstance()->getNewDocumentInstance();
			$stat->setAo($ao);
			$stat->setCompany($company);
			$stat->save();
			$session->setAttribute($attr, '1', self::SESSION_NAMESPACE);
		}
	}

	/**
	 * @return Boolean
	 */
	public function canViewAoDetail($ao)
	{
    	$company = ao_CompanyService::getInstance()->getLoggedCompany();
    	// AOs can be viewed:
        return
        	// if preference 'authRequiredToViewAoDetail' is NOT set to true,
        	(! ModuleService::getInstance()->getPreferenceValue('ao', 'authRequiredToViewAoDetail'))
        	// or if a company is logged,
        	|| (! is_null($company))
        	// or if they have an award notice.
        	|| (! is_null($ao->getAwardNotice()));
	}

	/**
	 * @param String $codeName
	 * @param Array<ao_persistentdocument_ao> $ao
	 * @return Boolean
	 */
	private function sendNotification($codeName, $ao)
	{
		return ao_NotificationStrategy::getStrategy()->sendNotification($codeName, $ao);
	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		// Check if award notice publication date is set if the award notice is set.
		if ( ! is_null($document->getAwardNotice()) && is_null($document->getAwardNoticePublicationDate()) )
		{
			throw new Exception("The award notice publication date must be set.");
		}
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 */
	private function updateLinkedMedia($ao)
	{
		$aoId = $ao->getId();
		$mediaIdArray = array();

		// For each property of type XHTMLFragment, parse the HTML to find
		// references to media and add the AO's id to the "download media URLs".
		foreach ($ao->getPersistentModel()->getPropertiesInfos() as $propertyInfo)
		{
			if ($propertyInfo->getType() == f_persistentdocument_PersistentDocument::PROPERTYTYPE_XHTMLFRAGMENT)
			{
				// Get text.
				$text = f_util_ClassUtils::callMethodOn($ao, 'get' . ucfirst($propertyInfo->getName()));
				// Parse it, get media references and fix URLs.
				$this->checkMediaInRichText($text, $mediaIdArray, $aoId);
				// Set text.
				f_util_ClassUtils::callMethodArgsOn($ao, 'set' . ucfirst($propertyInfo->getName()), array($text));
			}
		}

		$ao->removeAllLinkedMedia();
		foreach (array_unique($mediaIdArray) as $mediaId)
		{
			$ao->addLinkedMedia(DocumentHelper::getDocumentInstance($mediaId));
		}
		f_persistentdocument_PersistentProvider::getInstance()->updateDocument($ao);
	}

	/**
	 * This method parses an HTML text ($text) to find links to media. For each
	 * link to a media found, it will complete the URL with the AO id ($aoId)
	 * @param String $text
	 * @param Array<Integer>
	 * @param Integer $aoId
	 */
	private function checkMediaInRichText(&$text, &$mediaIdArray, $aoId)
	{
		$matches = null;
		if (preg_match_all('@(href|src)="(index.php\?module=media&amp;action=Display&amp;cmpref=([0-9]+)[^"]*)"@', $text, $matches))
		{
			$mediaIdArray = array_merge($mediaIdArray, $matches[3]);
			// $matches[2] contains the URLs
			foreach ($matches[2] as $oldUrl)
			{
				$newUrl = $oldUrl;
				// Remove any existing AO reference.
				$newUrl = preg_replace('/&amp;ao=\-?[0-9]+/', '', $oldUrl);
				// Append AO reference to URL.
				$text = str_replace($oldUrl, $newUrl.'&amp;ao='.$aoId, $text);
			}
		}
	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function postSave($document, $parentNodeId = null)
	{
		// Parse richtext contents to find linked media.
		$this->updateLinkedMedia($document);
	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @return boolean true if the document is publishable, false if it is not.
	 */
//	public function isPublishable($document)
//	{
//		$result = parent::isPublishable($document);
//		return $result;
//	}

	/**
	 * Methode à surcharger pour effectuer des post traitement apres le changement de status du document
	 * utiliser $document->getPublicationstatus() pour retrouver le nouveau status du document.
	 * @param ao_persistentdocument_ao $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param ao_persistentdocument_ao $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedTo($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $destId
	 */
//	protected function onMoveToStart($document, $destId)
//	{
//	}

	/**
	 * @param ao_persistentdocument_ao $document
	 * @param Integer $destId
	 * @return void
	 */
//	protected function onDocumentMoved($document, $destId)
//	{
//	}

	/**
	 * this method is call before save the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param f_persistentdocument_PersistentDocument $newDocument
	 * @param f_persistentdocument_PersistentDocument $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
//	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
//	{
//		throw new IllegalOperationException('This document cannot be duplicated.');
//	}
}