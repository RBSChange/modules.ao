<?php
class ao_CompanyScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_company
     */
    protected function initPersistentDocument()
    {
    	return ao_CompanyService::getInstance()->getNewDocumentInstance();
    }
}