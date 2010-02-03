<?php
class modules_ao_tests_NotificationDefaultStrategyTest extends ao_tests_AbstractBaseUnitTest
{
	public function testGetCompaniesToNotifyOnAOPublication()
	{
		$tf = ao_TestFactory::getInstance();
		$strategy = ao_NotificationDefaultPublicStrategy::getInstance();

		// Create a topic to store the AOs.

		$topic = website_TopicService::getInstance()->getNewDocumentInstance();
		$topic->setLabel("Appels d'offre régionnaux");
		$topic->save(website_WebsiteModuleService::getInstance()->getDefaultWebsite()->getId());

		// Create themes.

		$theme1 = $tf->getNewTheme(null, array('label' => "Internet"));
		$theme2 = $tf->getNewTheme(null, array('label' => "Nature"));
		$theme3 = $tf->getNewTheme(null, array('label' => "Sports"));
		$theme4 = $tf->getNewTheme(null, array('label' => "Science"));

		// Create companies.

		// p : notified on AO Publication
		// t12 : theme1 + theme2
		$company_p_t12 = $tf->getNewCompany(null, array(
			'theme' => array($theme1, $theme2),
			'wantsNotificationOnAOPublication' => true,
			'wantsNotificationOnAOUpdate' => false
			));
		// u : notified on AO Update
		// t12 : theme1 + theme2
		$company_u_t12 = $tf->getNewCompany(null, array(
			'theme' => array($theme1, $theme2),
			'wantsNotificationOnAOPublication' => false,
			'wantsNotificationOnAOUpdate' => true
			));
		// pu : notified on AO Publication and Update
		// t3 : theme3
		$company_pu_t3 = $tf->getNewCompany(null, array(
			'theme' => array($theme3),
			'wantsNotificationOnAOPublication' => true,
			'wantsNotificationOnAOUpdate' => true
			));
		// u : notified on AO Update
		// t3 : theme3
		$company_u_t3 = $tf->getNewCompany(null, array(
			'theme' => array($theme3),
			'wantsNotificationOnAOPublication' => false,
			'wantsNotificationOnAOUpdate' => true
			));
		// p : notified on AO Publication
		// t123 : theme1 + theme2 + theme3
		$company_p_t23 = $tf->getNewCompany(null, array(
			'theme' => array($theme2, $theme3),
			'wantsNotificationOnAOPublication' => true,
			'wantsNotificationOnAOUpdate' => true
			));

		// Create AOs.

		// t3 : theme3
		$ao_t3 = $tf->getNewAo($topic, array(
			'theme' => $theme3
			));
		// t1 : theme1
		$ao_t1 = $tf->getNewAo($topic, array(
			'theme' => $theme1
			));
		// t4 : theme4
		$ao_t4 = $tf->getNewAo($topic, array(
			'theme' => $theme4
			));

		// Assertions.

		$companies = $strategy->getCompaniesToNotifyOnAOPublication($ao_t3);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_pu_t3, $company_p_t23),
			$companies
			);

		$companies = $strategy->getCompaniesToNotifyOnAOPublication($ao_t1);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_p_t12),
			$companies
			);

		$companies = $strategy->getCompaniesToNotifyOnAOPublication($ao_t4);
		$this->assertEmpty($companies);
	}

	public function testGetCompaniesToNotifyOnAOUpdate()
	{
		$tf = ao_TestFactory::getInstance();
		$strategy = ao_NotificationDefaultPublicStrategy::getInstance();

		// Create a topic to store the AOs.

		$topic = website_TopicService::getInstance()->getNewDocumentInstance();
		$topic->setLabel("Appels d'offre régionnaux");
		$topic->save(website_WebsiteModuleService::getInstance()->getDefaultWebsite()->getId());

		// Create themes.

		$theme1 = $tf->getNewTheme(null, array('label' => "Internet"));
		$theme2 = $tf->getNewTheme(null, array('label' => "Nature"));
		$theme3 = $tf->getNewTheme(null, array('label' => "Sports"));
		$theme4 = $tf->getNewTheme(null, array('label' => "Science"));

		// Create companies.

		// p : notified on AO Publication
		// t12 : theme1 + theme2
		$company_p_t12 = $tf->getNewCompany(null, array(
			'theme' => array($theme1, $theme2),
			'wantsNotificationOnAOPublication' => true,
			'wantsNotificationOnAOUpdate' => false
			));
		// u : notified on AO Update
		// t12 : theme1 + theme2
		$company_u_t12 = $tf->getNewCompany(null, array(
			'theme' => array($theme1, $theme2),
			'wantsNotificationOnAOPublication' => false,
			'wantsNotificationOnAOUpdate' => true
			));
		// pu : notified on AO Publication and Update
		// t : theme3
		$company_pu_t3 = $tf->getNewCompany(null, array(
			'theme' => array($theme3),
			'wantsNotificationOnAOPublication' => true,
			'wantsNotificationOnAOUpdate' => true
			));
		// u : notified on AO Update
		// t : theme3
		$company_u_t3 = $tf->getNewCompany(null, array(
			'theme' => array($theme3),
			'wantsNotificationOnAOPublication' => false,
			'wantsNotificationOnAOUpdate' => true
			));

		// Create AOs.

		// t3 : theme3
		$ao_t3 = $tf->getNewAo($topic, array(
			'theme' => array($theme3)
			));
		// t123 : theme1 + theme2 + theme3
		$ao_t123 = $tf->getNewAo($topic, array(
			'theme' => array($theme1, $theme2, $theme3)
			));
		// t4 : theme4
		$ao_t4 = $tf->getNewAo($topic, array(
			'theme' => array($theme4)
			));

		// Assertions.

		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t3);
		// Because no company has viewed $ao_t3.
		$this->assertEmpty($companies);

		$aos = ao_AoServicePublic::getInstance();

		// $company_pu_t3 has viewed $ao_t3
		// The only company to notify is $company_pu_t3.
		$aos->updateStatsOnAOView($ao_t3, $company_pu_t3);
		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t3);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_pu_t3),
			$companies
			);

		// $company_u_t3 has viewed $ao_t3
		// 2 companies to notify : $company_pu_t3 (as before) and $company_u_t3.
		$aos->updateStatsOnAOView($ao_t3, $company_u_t3);
		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t3);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_pu_t3, $company_u_t3),
			$companies
			);

		// $company_u_t12 has viewed $ao_t3 (but is not interested in $theme3)
		// Still 2 companies to notify : $company_pu_t3 (as before) and $company_u_t3.
		// Since $company_u_t12 is not interested in $theme3, it don't have to be notified.
		$aos->updateStatsOnAOView($ao_t3, $company_u_t12);
		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t3);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_pu_t3, $company_u_t3),
			$companies
			);

		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t123);
		// Because no company has viewed $ao_t123.
		$this->assertEmpty($companies);

		// $company_pu_t3 has viewed $ao_t123
		// 3 companies to notify : $company_u_t3, $company_pu_t3 and $company_u_t12.
		$aos->updateStatsOnAOView($ao_t123, $company_u_t3);
		$aos->updateStatsOnAOView($ao_t123, $company_pu_t3);
		$aos->updateStatsOnAOView($ao_t123, $company_u_t12);

		$companies = $strategy->getCompaniesToNotifyOnAOUpdate($ao_t123);
		$this->assertArrayEqualsIgnoreOrder(
			array($company_u_t3, $company_pu_t3, $company_u_t12),
			$companies
			);
	}
}

/**
 * This class extends the ao_NotificationDefaultStrategy class to expose some
 * protected methods to ease the testing process.
 */
class ao_NotificationDefaultPublicStrategy extends ao_NotificationDefaultStrategy
{
	/**
	 * Singleton
	 * @var ao_NotificationDefaultPublicStrategy
	 */
	private static $instance = null;

	/**
	 * @return ao_NotificationDefaultPublicStrategy
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @return Array<ao_persistentdocument_company>
	 */
	public function getCompaniesToNotifyOnAOPublication($ao)
	{
		return parent::getCompaniesToNotifyOnAOPublication($ao);
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @return Array<ao_persistentdocument_company>
	 */
	public function getCompaniesToNotifyOnAOUpdate($ao)
	{
		parent::getCompaniesToNotifyOnAOUpdate($ao);
	}
}