<?php
class ao_BlockCreateCompanySuccessView extends block_BlockView
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {
    	$this->setTemplateName('Ao-Block-CreateCompany-Input', K::HTML);

  		$form = form_FormService::getInstance()->getFormByFormId('modules_ao/createCompany');
		$form->setSaveResponse(false);

		$subBlock = $this->getNewBlockInstance()
        	->setPackageName('modules_form')
        	->setType('form')
        	->setParameter(K::COMPONENT_ID_ACCESSOR , array($form->getId()));

   	 	$this->setAttribute('form', $this->forward($subBlock));
    }
}