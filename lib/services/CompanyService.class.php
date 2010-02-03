<?php
class ao_CompanyService extends f_persistentdocument_DocumentService
{
	const FOLDER_TAG = 'default_ao_company_folder';
	const FRONTEND_GROUP_TAG = 'default_ao_frontend_group';

	/**
	 * @var ao_CompanyService
	 */
	private static $instance;

	/**
	 * @return ao_CompanyService
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
	 * @return ao_persistentdocument_company
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_ao/company');
	}

	/**
	 * Create a query based on 'modules_ao/company' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_ao/company');
	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		$document->setLabel($document->getContactName().' / '.$document->getCompanyName());
	}

	/**
	 * Creates a new company from an associative array supplied by the frontoffice form.
	 * @param Array<String, String> $data
	 * @return ao_persistentdocument_company
	 */
	public function createFromSubmittedData($data)
	{
		// Fix some value types.
		$themes = $data['theme'];
		if (is_string($themes))
		{
			$themes = explode(" ", $themes);
		}
		unset($data['theme']);

		$tm = $this->getTransactionManager();
		try 
		{
			$tm->beginTransaction();
			
			// Create user.
			$frontendUser = users_FrontenduserService::getInstance()->getNewDocumentInstance();
			$this->setFrontendUserPropertiesFromSubmittedData($frontendUser, $data);
			$frontendUser->addGroups(TagService::getInstance()->getDocumentByExclusiveTag(ao_CompanyService::FRONTEND_GROUP_TAG));
			$frontendUser->save();
			$frontendUser->activate();
	
			// Create company.
			if (!is_null($company = $this->getByFrontendUser($frontendUser)))
			{
				throw new Exception("This company already has an account.");
			}
			$company = ao_CompanyService::getInstance()->getNewDocumentInstance();
			$this->setCompanyPropertiesFromSubmittedData($company, $data);
			$company->setFrontendUser($frontendUser);
			foreach ($themes as $themeId)
			{
				$company->addTheme(DocumentHelper::getDocumentInstance($themeId));
			}
			$company->setParentFolder(TagService::getInstance()->getDocumentByExclusiveTag(self::FOLDER_TAG));
			$company->save();
			
			$tm->commit();
		}
		catch (Exception $e)
		{
			try 
			{
				$tm->rollBack($e);
			}
			catch (Exception $e)
			{
			}
			// Only form_FormValidationException are catched properly in forms...
			throw new form_FormValidationException($e->getMessage(), array($e->getMessage()));
		}
		return $company;
	}

	/**
	 * @param ao_persistentdocument_company $frontendUser
	 * @param Array<String=>String> $data
	 */
	private function setCompanyPropertiesFromSubmittedData($company, $data)
	{
		DocumentHelper::setPropertiesTo($data, $company);
	}

	/**
	 * @param users_persistentdocument_frontenduser $frontendUser
	 * @param Array<String=>String> $data
	 */
	private function setFrontendUserPropertiesFromSubmittedData($frontendUser, $data)
	{
		$frontendUser->setFirstname($data['contactName']);
		$frontendUser->setLastname($data['companyName']);
		$frontendUser->setLogin($data['emailAddress']);
		$frontendUser->setEmail($data['emailAddress']);
		$frontendUser->setPassword($data['password']);
	}

	/**
	 * @param users_persistentdocument_frontenduser $frontendUser
	 * @return ao_persistentdocument_company
	 */
	public function getByFrontendUser($frontendUser)
	{
		return $this->createQuery()
			->add(Restrictions::eq('frontendUser.id', $frontendUser->getId()))
			->findUnique();
	}

	/**
	 * Moves the company into the right folder.
	 *
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}


	/**
	 * @return ao_persistentdocument_company
	 */
	public function getLoggedCompany()
	{
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if ($user !== null)
		{
			return f_util_ArrayUtils::firstElement($user->getCompanyArrayInverse(0, 1));
		}
		return null;
	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
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
	 * @param ao_persistentdocument_company $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param ao_persistentdocument_company $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param ao_persistentdocument_company $toDocument
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
	 * @param ao_persistentdocument_company $document
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