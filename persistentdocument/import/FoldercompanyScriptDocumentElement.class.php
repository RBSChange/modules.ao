<?php
class ao_FoldercompanyScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_foldercompany
     */
    protected function initPersistentDocument()
    {
    	return ao_FoldercompanyService::getInstance()->getNewDocumentInstance();
    }
}