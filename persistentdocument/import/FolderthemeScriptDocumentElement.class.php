<?php
class ao_FolderthemeScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_foldertheme
     */
    protected function initPersistentDocument()
    {
    	return ao_FolderthemeService::getInstance()->getNewDocumentInstance();
    }
}