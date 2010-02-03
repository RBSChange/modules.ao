<?php
class ao_ListThemesService implements list_ListItemsService
{
    /**
     * @var ao_ListThemesService
     */
	private static $instance = null;

	/**
	 * @return ao_ListThemesService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new ao_ListThemesService();
		}
		return self::$instance;
	}

	/**
	 * @return Array<list_Item>
	 */
	public function getItems()
	{
		$itemArray = array();
	    foreach (ao_ThemeService::getInstance()->createQuery()->addOrder(Order::asc('document_label'))->find() as $theme)
	    {
	    	$itemArray[] = new list_Item($theme->getLabel(), $theme->getId());
	    }
		return $itemArray;
	}
}