<?php
/**
 * @package modules.ao.lib.blocks
 */
class ao_BlockAoAction extends block_BlockAction
{
	/**
	 * @param website_Page $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
	public function executeBackoffice($context, $request)
	{
		return block_BlockView::NONE;
	}
	
	/**
	 * @param website_Page $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
	public function execute($context, $request)
	{
		$ao = $this->getDocumentParameter();
		if ($ao === null)
		{
			return block_BlockView::NONE;
		}
		$aoService = ao_AoService::getInstance();
		if ($aoService->canViewAoDetail($ao))
		{
			$aoService->onAoDetailView($ao);
			$this->setParameter('item', $ao);
			return block_BlockView::SUCCESS;
		}
		
		if (users_UserService::getInstance()->getCurrentFrontEndUser() !== null || $context->getGlobalRequest()->getParameter('action') == 'PreviewPage')
		{
			return block_BlockView::ERROR;
		}
		
		$context->getUser()->setAttribute('illegalAccessPage', $_SERVER['REQUEST_URI']);
		HttpController::getInstance()->redirect('website', 'Error401');
	}
}