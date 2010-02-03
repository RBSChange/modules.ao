<?php
class ao_PreferencesScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_preferences
     */
    protected function initPersistentDocument()
    {
    	$document = ModuleService::getInstance()->getPreferencesDocument('ao');
    	return ($document !== null) ? $document : ao_PreferencesService::getInstance()->getNewDocumentInstance();
    }
}