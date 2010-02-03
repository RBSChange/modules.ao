<?php
class modules_ao_tests_AoServiceTest extends ao_tests_AbstractBaseUnitTest
{
	public function testGetOrdered()
	{
		$topic1 = website_TopicService::getInstance()->getNewDocumentInstance();
		$topic1->setLabel("Appels d'offre régionnaux");
		$topic1->save(website_WebsiteModuleService::getInstance()->getDefaultWebsite()->getId());

		$topic2 = website_TopicService::getInstance()->getNewDocumentInstance();
		$topic2->setLabel("Appels d'offre nationnaux");
		$topic2->save(website_WebsiteModuleService::getInstance()->getDefaultWebsite()->getId());

		$tf = ao_TestFactory::getInstance();

		// Create themes.

		$theme1 = $tf->getNewTheme(null, array('label' => "Informatique"));
		$theme2 = $tf->getNewTheme(null, array('label' => "Nature"));
		$theme3 = $tf->getNewTheme(null, array('label' => "Sports"));

		// Create AOs.

		$ao1 = $tf->getNewAo($topic1, array(
			'theme' => $theme1,
			'emissionDate' => '2008-05-03 10:00:00'
			));
		$ao1->activate();
		$ao2 = $tf->getNewAo($topic1, array(
			'theme' => $theme2,
			'emissionDate' => '2008-05-01 10:00:00'
			));
		$ao2->activate();
		$ao3 = $tf->getNewAo($topic1, array(
			'theme' => $theme1,
			'emissionDate' => '2008-05-01 10:00:00'
			));
		$ao3->activate();

		// Assertions.

		$aoArray = ao_AoService::getInstance()->getOrdered($theme2->getId(), $topic1);
		$this->assertArrayEqualsIgnoreOrder(array($ao2), $aoArray);

		$aoArray = ao_AoService::getInstance()->getOrdered($theme1->getId(), $topic1);
		$this->assertArrayEqualsIgnoreOrder(array($ao1, $ao3), $aoArray);

		$aoArray = ao_AoService::getInstance()->getOrdered($theme3->getId(), $topic1);
		$this->assertEmpty($aoArray);

		$aoArray = ao_AoService::getInstance()->getOrdered($theme1->getId(), $topic2);
		$this->assertEmpty($aoArray);

		$aoArray = ao_AoService::getInstance()->getOrdered($theme1->getId(), $topic1, Order::asc('emissionDate'));
		$this->assertArrayEquals(array($ao3, $ao1), $aoArray);
	}
}
