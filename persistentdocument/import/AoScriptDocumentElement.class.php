<?php
class ao_AoScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return ao_persistentdocument_ao
     */
    protected function initPersistentDocument()
    {
    	return ao_AoService::getInstance()->getNewDocumentInstance();
    }
}