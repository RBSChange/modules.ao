<?php
class ao_TestFactory extends ao_TestFactoryBase
{
	/**
	 * @var ao_TestFactory
	 */
	private static $instance;

	/**
	 * @return ao_TestFactory
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (PROFILE != 'test')
		{
			throw new Exception('This method is only usable in test mode.');
		}
		if (self::$instance === null)
		{
			self::$instance = new ao_TestFactory;
			// register the testFactory in order to be cleared after each test case.
			tests_AbstractBaseTest::registerTestFactory(self::$instance);
		}
		return self::$instance;
	}

	/**
	 * Clear the TestFactory instance.
	 *
	 * @return void
	 * @throws Exception
	 */
	public static function clearInstance()
	{
		if (PROFILE != 'test')
		{
			throw new Exception('This method is only usable in test mode.');
		}
		self::$instance = null;
	}

	/**
	 * Initialize documents default properties
	 * @return void
	 */
	public function init()
	{
		$this->setPreferencesDefaultProperty('label', 'preferences test');
		$this->setThemeDefaultProperty('label', 'Design web');
		$this->setBusinessDefaultProperty('label', 'Informatique');
		$this->setAoDefaultProperty('label', "Appel d'offre de test");

		$this->initCompanyDefaultProperties();
		$this->initAoDefaultProperties();
	}

	private function initAoDefaultProperties()
	{
		$this->setAoDefaultProperty('label', "Appel d'offre");
		$this->setAoDefaultProperty('description', "Description de l'appel d'offre");
		$this->setAoDefaultProperty('emissionDate', date("Y-m-d H:i:s"));
		$this->setAoDefaultProperty('endDate', date("Y-m-d H:i:s"));
		$this->setAoDefaultProperty('place', "Strasbourg");
	}

	private function initCompanyDefaultProperties()
	{
		$contactName = "Frédéric Bonjour";
		$companyName = "RBS";
		$emailAddress = "frederic.bonjour@rbs.fr";
		$this->setCompanyDefaultProperty('label', 'company test');
		$this->setCompanyDefaultProperty('contactName', $contactName);
		$this->setCompanyDefaultProperty('companyName', $companyName);
		$this->setCompanyDefaultProperty('emailAddress', $emailAddress);
		$this->setCompanyDefaultProperty('business', "");
		$this->setCompanyDefaultProperty('address', "11 rue Icare");
		$this->setCompanyDefaultProperty('zipCode', "67000");
		$this->setCompanyDefaultProperty('city', "STRASBOURG");
		$this->setCompanyDefaultProperty('business', $this->getNewBusiness(null));
		$country = zone_CountryService::getInstance()->createQuery()->add(Restrictions::eq('label', 'France'))->findUnique();
		$this->setCompanyDefaultProperty('country', $country);
		$this->setCompanyDefaultProperty('phoneNumber', '03 88 764 764');
		$this->setCompanyDefaultProperty('faxNumber', '03 88 764 765');
		$this->setCompanyDefaultProperty('wantsNotificationOnAOPublication', true);
		$this->setCompanyDefaultProperty('wantsNotificationOnAOUpdate', true);
		$this->setCompanyDefaultProperty(
			'theme',
			array(
				$this->getNewTheme(null, array('label' => 'Informatique'))->getId(),
				$this->getNewTheme(null, array('label' => 'Design'))->getId()
				)
			);

		$frontendUser = users_FrontenduserService::getInstance()->getNewDocumentInstance();
		$frontendUser->setFirstname($contactName);
		$frontendUser->setLastname($companyName);
		$frontendUser->setLogin($emailAddress);
		$frontendUser->setEmail($emailAddress);
		$frontendUser->setPassword('123456');
		$this->setCompanyDefaultProperty('frontendUser', $frontendUser);
	}
}