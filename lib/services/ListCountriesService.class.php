<?php
class ao_ListCountriesService implements list_ListItemsService
{
    /**
     * @var ao_ListCountriesService
     */
	private static $instance = null;

	/**
	 * @return ao_ListCountriesService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new ao_ListCountriesService();
		}
		return self::$instance;
	}

	/**
	 * @return Array<list_Item>
	 */
	public function getItems()
	{
		$itemArray = array();
	    foreach (zone_CountryService::getInstance()->createQuery()->addOrder(Order::asc('document_label'))->find() as $country)
	    {
	    	$itemArray[] = new list_Item($country->getLabel(), $country->getId());
	    }
		return $itemArray;
	}
}