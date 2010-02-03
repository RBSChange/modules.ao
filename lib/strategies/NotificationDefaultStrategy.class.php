<?php
class ao_NotificationDefaultStrategy extends ao_NotificationStrategy
{
	/**
	 * Singleton
	 * @var ao_NotificationDefaultStrategy
	 */
	private static $instance = null;

	/**
	 * @return ao_NotificationDefaultStrategy
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @return Array<ao_persistentdocument_company>
	 */
	protected function getCompaniesToNotifyOnAOPublication($ao)
	{
		if ( ! is_null($theme = $ao->getTheme()) )
		{
			$query = ao_CompanyService::getInstance()->createQuery()
				// Companies that asked to be notified on AO publication
				->add(Restrictions::eq('wantsNotificationOnAOPublication', true));
			// Companies that are interested in one theme (at least) affected to the AO.
			$query->createCriteria('theme')->add(Restrictions::eq('id', $theme->getId()));
			return $query->find();
		}
		return array();
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @return Array<ao_persistentdocument_company>
	 */
	protected function getCompaniesToNotifyOnAOUpdate($ao)
	{
		if ( ! is_null($theme = $ao->getTheme()) )
		{
			$query = ao_CompanyService::getInstance()->createQuery()
				// Companies that asked to be notified on AO update
				->add(Restrictions::eq('wantsNotificationOnAOUpdate', true))
				// Companies that has viewed the AO
				//->add(Restrictions::eq('viewedAO.id', $ao->getId()))
				->addOrder(Order::asc('document_label'));
			// Companies that has viewed the AO (via the statao documents)
			$query->createCriteria('statao')->add(Restrictions::eq('ao.id', $ao->getId()));
			// Companies that are interested in one theme (at least) affected to the AO.
			$query->createCriteria('theme')->add(Restrictions::eq('id', $theme->getId()));
			return $query->find();
		}
		return array();
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @return Array<String=>String> $replacements
	 */
	protected function buildNotificationReplacementsArray($ao)
	{
		$replacements = array();
		foreach (DocumentHelper::getPropertiesOf($ao) as $name => $value)
		{
			$replacements['ao-' . $name] = $value;
		}

		$labelAndReference = $ao->getLabel();
		if ( ($reference = $ao->getReference()) )
		{
			$labelAndReference .= ' (ref. : ' . $reference.')';
		}
		$replacements['ao-label-reference'] = $labelAndReference;

		if ( ! is_null($theme = $ao->getTheme()) )
		{
			$replacements['ao-themes'] = $ao->getTheme()->getLabel();
		}
		else
		{
			$replacements['ao-themes'] = '';
		}

		$replacements['ao-url'] = LinkHelper::getUrl($ao);

		return $replacements;
	}

	/**
	 * @param Array<ao_persistentdocument_company> $companies
	 * @return mail_MessageRecipients
	 */
	protected function buildMessageRecipients($companies)
	{
		$recipients = new mail_MessageRecipients();
		$bcc = array();
		foreach ($companies as $company)
		{
			$bcc[] = $company->getEmailAddress();
		}
		$recipients->setBCC($bcc);
		return $recipients;
	}

	/**
	 * @param String $codeName
	 * @param Array<ao_persistentdocument_ao> $ao
	 * @return Boolean
	 */
	public function sendNotification($codeName, $ao)
	{
		switch ($codeName)
		{
			case ao_AoService::NOTIFICATION_UPDATED_AO :
				$companies = $this->getCompaniesToNotifyOnAOUpdate($ao);
				break;
			case ao_AoService::NOTIFICATION_PUBLISHED_AO :
				$companies = $this->getCompaniesToNotifyOnAOPublication($ao);
				break;
			default:
				throw new Exception("Unknown notification \"$codeName\".");
				break;
		}

		$ns = notification_NotificationService::getInstance();
		$recipients = $this->buildMessageRecipients($companies);
		$replacements = $this->buildNotificationReplacementsArray($ao);
		if (Framework::isDebugEnabled())
		{
			Framework::debug(__METHOD__." notification  : ".var_export($codeName, true));
			Framework::debug(__METHOD__." recipients To : ".var_export($recipients->getTo(), true));
			Framework::debug(__METHOD__." recipients CC : ".var_export($recipients->getCC(), true));
			Framework::debug(__METHOD__." recipients BCC: ".var_export($recipients->getBCC(), true));
			Framework::debug(__METHOD__." replacements  : ".var_export($replacements, true));
		}
		$result = $ns->send($ns->getNotificationByCodeName($codeName), $recipients, $replacements, 'ao');
		if (Framework::isDebugEnabled())
		{
			Framework::debug(__METHOD__." send result   : ".var_export($result, true));
		}
		return $result;
	}
}