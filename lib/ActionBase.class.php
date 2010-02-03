<?php
class ao_ActionBase extends f_action_BaseAction
{
	
	/**
	 * Returns the ao_PreferencesService to handle documents of type "modules_ao/preferences".
	 *
	 * @return ao_PreferencesService
	 */
	public function getPreferencesService()
	{
		return ao_PreferencesService::getInstance();
	}
	
	/**
	 * Returns the ao_ThemeService to handle documents of type "modules_ao/theme".
	 *
	 * @return ao_ThemeService
	 */
	public function getThemeService()
	{
		return ao_ThemeService::getInstance();
	}
	
	/**
	 * Returns the ao_CompanyService to handle documents of type "modules_ao/company".
	 *
	 * @return ao_CompanyService
	 */
	public function getCompanyService()
	{
		return ao_CompanyService::getInstance();
	}
	
	/**
	 * Returns the ao_BusinessService to handle documents of type "modules_ao/business".
	 *
	 * @return ao_BusinessService
	 */
	public function getBusinessService()
	{
		return ao_BusinessService::getInstance();
	}
	
	/**
	 * Returns the ao_AoService to handle documents of type "modules_ao/ao".
	 *
	 * @return ao_AoService
	 */
	public function getAoService()
	{
		return ao_AoService::getInstance();
	}
	
	/**
	 * Returns the ao_FolderbusinessService to handle documents of type "modules_ao/folderbusiness".
	 *
	 * @return ao_FolderbusinessService
	 */
	public function getFolderbusinessService()
	{
		return ao_FolderbusinessService::getInstance();
	}
	
	/**
	 * Returns the ao_FoldercompanyService to handle documents of type "modules_ao/foldercompany".
	 *
	 * @return ao_FoldercompanyService
	 */
	public function getFoldercompanyService()
	{
		return ao_FoldercompanyService::getInstance();
	}
	
	/**
	 * Returns the ao_FolderthemeService to handle documents of type "modules_ao/foldertheme".
	 *
	 * @return ao_FolderthemeService
	 */
	public function getFolderthemeService()
	{
		return ao_FolderthemeService::getInstance();
	}
	
	/**
	 * Returns the ao_StataoService to handle documents of type "modules_ao/statao".
	 *
	 * @return ao_StataoService
	 */
	public function getStataoService()
	{
		return ao_StataoService::getInstance();
	}
	
	/**
	 * Returns the ao_StataomediaService to handle documents of type "modules_ao/stataomedia".
	 *
	 * @return ao_StataomediaService
	 */
	public function getStataomediaService()
	{
		return ao_StataomediaService::getInstance();
	}
	
}