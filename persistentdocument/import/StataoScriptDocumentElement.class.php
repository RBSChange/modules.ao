<?php
class ao_StataoScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_statao
     */
    protected function initPersistentDocument()
    {
    	return ao_StataoService::getInstance()->getNewDocumentInstance();
    }
}