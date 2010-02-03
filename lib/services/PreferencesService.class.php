<?php
/**
 * @date Wed, 14 May 2008 15:42:12 +0200
 * @author intbonjf
 */
class ao_PreferencesService extends f_persistentdocument_DocumentService
{
	/**
	 * @var ao_PreferencesService
	 */
	private static $instance;

	/**
	 * @return ao_PreferencesService
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
	 * @return ao_persistentdocument_preferences
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_ao/preferences');
	}

	/**
	 * Create a query based on 'modules_ao/preferences' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_ao/preferences');
	}

	/**
	 * @param ao_persistentdocument_preferences $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		$document->setLabel('&modules.ao.bo.general.Module-name;');
	}
}