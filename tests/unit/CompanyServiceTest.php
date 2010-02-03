<?php
class modules_ao_tests_CompanyServiceTest extends ao_tests_AbstractBaseUnitTest
{
	public function prepareTestCase()
	{

	}

	public function testSave()
	{
		// Check company creation.
		$tf = ao_TestFactory::getInstance();
		$company = $tf->getNewCompany($newFolder);
		$this->assertType('ao_persistentdocument_company', $company);
	}
}
