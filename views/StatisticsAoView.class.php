<?php
class ao_StatisticsAoView extends f_view_BaseView
{
    /**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
        $this->setTemplateName('Ao-Statistics-Ao', K::HTML);

        $ao = $request->getAttribute('ao');
        $this->setAttribute('ao', $ao);

        $stataoArray = ao_StataoService::getInstance()->createQuery()
        	->add(Restrictions::eq('ao.id', $ao->getId()))
        	->addOrder(Order::desc('document_creationdate'))
        	->find();

        $items = array();
        foreach ($stataoArray as $statao)
        {
        	$items[] = array(
        		'company' => $statao->getCompany(),
        		'date' => $statao->getCreationdate()
        		);
        }
        $this->setAttribute('items', $items);
    }
}