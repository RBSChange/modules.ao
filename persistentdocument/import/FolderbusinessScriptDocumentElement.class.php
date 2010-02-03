<?php
class ao_FolderbusinessScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_folderbusiness
     */
    protected function initPersistentDocument()
    {
    	return ao_FolderbusinessService::getInstance()->getNewDocumentInstance();
    }
}