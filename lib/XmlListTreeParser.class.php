<?php
class ao_XmlListTreeParser extends tree_parser_XmlListTreeParser
{
	/**
     * Returns the document's specific and/or overridden attributes.
     *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param XmlElement $treeNode
	 * @param f_persistentdocument_PersistentDocument $reference
	 * @return array<mixed>
	 */
	protected function getAttributes($document, $treeNode, $reference = null)
	{
		$attributes = parent::getAttributes($document, $treeNode, $reference);

		if ($document instanceof ao_persistentdocument_ao)
		{
			$value = ! is_null($document->getAwardNotice());
			$attributes['awardNoticePresent'] = f_Locale::translate('&modules.uixul.bo.general.'.($value ? 'Yes' : 'No').';');
			$attributes['awardNoticePresentValue'] = $value ? 'true' : 'false';
			$attributes['emissionDate'] = date_DateFormat::format(date_Calendar::getInstance($document->getEmissionDate()), 'd/m/Y');
			$attributes['endDate'] = date_DateFormat::format(date_Calendar::getInstance($document->getEndDate()), 'd/m/Y');
		}

		return $attributes;
	}
}