<?php
class ao_ThemeScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_theme
     */
    protected function initPersistentDocument()
    {
    	return ao_ThemeService::getInstance()->getNewDocumentInstance();
    }
}