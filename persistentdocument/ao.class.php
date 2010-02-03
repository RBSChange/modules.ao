<?php
class ao_persistentdocument_ao extends ao_persistentdocument_aobase implements indexer_IndexableDocument
{
	/**
	 * Get the indexable document
	 *
	 * @return indexer_IndexedDocument
	 */
	public function getIndexedDocument()
	{
		$indexedDoc = new indexer_IndexedDocument();
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel('modules_ao/ao');
		$indexedDoc->setLabel($this->getLabel());
		$indexedDoc->setLang(RequestContext::getInstance()->getLang());
		$indexedDoc->setText($this->getMainPieces() . $this->getParticularPieces() . $this->getDescription());
		return $indexedDoc;
	}

	/**
	 * @return String
	 */
	public function getLabelAndReference()
	{
		$label = $this->getLabel();
		$ref = trim($this->getReference());
		if (strlen($ref) > 0)
		{
			$label .= " (ref. : $ref)";
		}
		return $label;
	}
}