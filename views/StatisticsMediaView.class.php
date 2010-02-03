<?php
class ao_StatisticsMediaView extends f_view_BaseView
{
    /**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
        $this->setTemplateName('Ao-Statistics-Media', K::HTML);

        $ao = $request->getAttribute('ao');
        $this->setAttribute('ao', $ao);

        $stataoArray = ao_StataomediaService::getInstance()->createQuery()
        	->add(Restrictions::eq('ao.id', $ao->getId()))
        	->addOrder(Order::desc('document_creationdate'))
        	->find();

        $items = array();
        foreach ($stataoArray as $statao)
        {
        	$media = $statao->getMedia();
        	$mediaLabel = $media->getLabel();
        	if ( ! isset($items[$mediaLabel]) )
        	{
        		$items[$mediaLabel] = array('document' => $media, 'items' => array());
        	}
        	$items[$mediaLabel]['items'][] = array(
        		'company' => $statao->getCompany(),
        		'date' => $statao->getCreationdate()
        		);
        }
        ksort($items);
        $this->setAttribute('items', $items);
    }
}