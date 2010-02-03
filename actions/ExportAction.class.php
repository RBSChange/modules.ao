<?php
class ao_ExportAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$companiesFolder = $this->getDocumentInstanceFromRequest($request);
		$request->setAttribute('companiesFolder', $companiesFolder);
		return 'Csv';
	}
}