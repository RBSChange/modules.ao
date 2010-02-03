<?php
class ao_BlockAoListAction extends block_BlockAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
    public function execute($context, $request)
    {
        $this->setParameter('items', $this->getDocumentsParameter());

        return block_BlockView::SUCCESS;
    }
}