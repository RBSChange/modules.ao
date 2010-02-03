<?php
/**
  * ao_persistentdocument_preferences
 * @package ao
 */
class ao_persistentdocument_preferences extends ao_persistentdocument_preferencesbase 
{
	/**
	 * @see f_persistentdocument_PersistentDocumentImpl::getLabel()
	 *
	 * @return String
	 */
	public function getLabel()
	{
		return f_Locale::translateUI(parent::getLabel());
	}
}