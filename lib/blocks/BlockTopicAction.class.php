<?php
class ao_BlockTopicAction extends block_BlockAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
    public function execute($context, $request)
    {   
        $container = $this->getDocumentParameter();
		$this->setParameter('container', $container);

		// Get the list of element for the container
		$items = array();
		$items = $container->getDocumentService()->getChildrenOf($container, 'modules_ao/ao');
		
		// Get the preference of module
		$nbItemPerPage = 10;

		// Set the paginator
		$paginator = new paginator_Paginator('ao', $request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1), $items, $nbItemPerPage);
		$this->setParameter('paginator', $paginator);

		return block_BlockView::SUCCESS;
    }
}