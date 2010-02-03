<?php
class modules_ao_tests_BusinessServiceTest extends ao_tests_AbstractBaseUnitTest
{
	public function prepareTestCase()
	{

	}

	public function testSave()
	{
		// Check business creation.
		$tf = ao_TestFactory::getInstance();
		$business = $tf->getNewBusiness(null, array('label' => "Secteur d'activité"));
		$this->assertType('ao_persistentdocument_business', $business);
	}
}
