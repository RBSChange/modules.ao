<?php
class modules_ao_tests_ThemeServiceTest extends ao_tests_AbstractBaseUnitTest
{
	public function prepareTestCase()
	{

	}

	public function testSave()
	{
		// Check theme creation.
		$tf = ao_TestFactory::getInstance();
		$theme = $tf->getNewTheme($newFolder, array('label' => "Thème pour les appels d'offre"));
		$this->assertType('ao_persistentdocument_theme', $theme);
	}
}
