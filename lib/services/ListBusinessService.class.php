<?php
class ao_ListBusinessService implements list_ListItemsService
{
    /**
     * @var ao_ListBusinessService
     */
	private static $instance = null;

	/**
	 * @return ao_ListBusinessService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new ao_ListBusinessService();
		}
		return self::$instance;
	}

	/**
	 * @return Array<list_Item>
	 */
	public function getItems()
	{
		$itemArray = array();
	    foreach (ao_BusinessService::getInstance()->createQuery()->addOrder(Order::asc('document_label'))->find() as $business)
	    {
	    	$itemArray[] = new list_Item($business->getLabel(), $business->getId());
	    }
		return $itemArray;
	}
}