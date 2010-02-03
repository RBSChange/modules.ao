<?php
class ao_BusinessScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_business
     */
    protected function initPersistentDocument()
    {
    	return ao_BusinessService::getInstance()->getNewDocumentInstance();
    }
}