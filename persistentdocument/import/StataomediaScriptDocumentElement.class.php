<?php
class ao_StataomediaScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_stataomedia
     */
    protected function initPersistentDocument()
    {
    	return ao_StataomediaService::getInstance()->getNewDocumentInstance();
    }
}