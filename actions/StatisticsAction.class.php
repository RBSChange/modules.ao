<?php
class ao_StatisticsAction extends f_action_BaseAction
{
	const VIEW_MODE_AO       = 'ao';
	const VIEW_MODE_AO_MEDIA = 'media';

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
    	$mode = $request->getParameter('mode', self::VIEW_MODE_AO);
    	$request->setAttribute('ao', $this->getDocumentInstanceFromRequest($request));
	   	return ucfirst($mode);
    }

    public function isSecure()
    {
    	return true;
    }

    public function getRequestMethods ()
    {
        return Request::GET;
    }
}
