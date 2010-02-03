<?php
class ao_BlockCreateCompanyAction extends block_BlockAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String the view name
	 */
	public function execute($context, $request)
	{
    	f_event_EventManager::register($this);

		return block_BlockView::SUCCESS;
	}

 	/**
	 * @param form_FormService $sender
	 * @param array<'form' => form_persistentdocument_form, 'parameters' => &Array<>>
	 */
    public function onformInitData($sender, $params)
	{
		$params['parameters']['country'] = zone_CountryService::getInstance()->getByCode('FR')->getId();
	}

	/**
	 * @param form_persistentdocument_form $form
	 * @param array<'response' => form_persistentdocument_response> $params
	 */
	public function onformSubmitted($form, $params)
	{
		$response = $params['response'];
		$company = ao_CompanyService::getInstance()->createFromSubmittedData($response->getData());

		if (!is_null($company))
		{
			users_UserService::getInstance()->authenticateFrontEndUser($company->getFrontendUser());
		}
	}
	
	/**
	 * If an exception is thrown in the listener, it must be thrown by the event dispatcher.
	 */
	public $onformSubmittedRequired;

	/**
	 * @param form_FormService $sender
	 * @param array<'form' => form_persistentdocument_form, 'request' => block_BlockRequest, 'errors' => validation_Errors) $params
	 */
	public function onformValidate($sender, $params)
	{
		$request = $params['request'];
		$errors = $params['errors'];
		$us = users_UserService::getInstance();

		// Check password.
		if (!$us->checkPassword($request->getParameter('password')))
		{
			$errors->rejectValue('', '&modules.ao.validate.new-password-is-not-valid;');
		}
		else if ($request->getParameter('password') != $request->getParameter('passwordConfirm'))
		{
			$errors->rejectValue('', '&modules.ao.validate.Passwords-do-not-match;');
		}

		// Check login unicity.
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		if ($us->getFrontendUserByLogin($request->getParameter('emailAddress'), $website->getId()) !== null)
		{
			$errors->rejectValue('', '&modules.users.validate.Email-address-already-used-message;');
		}
	}
}