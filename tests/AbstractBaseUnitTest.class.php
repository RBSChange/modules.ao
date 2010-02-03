<?php
abstract class ao_tests_AbstractBaseUnitTest extends ao_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->resetDatabase();
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param String $tag
	 * @param String $message
	 */
	protected function assertHasTag($document, $tag, $message = '')
	{
		$this->assertTrue(TagService::getInstance()->hasTag($document, $tag), $message);
	}
}

class ao_AoServicePublic extends ao_AoService
{
	/**
	 * @var ao_AoServicePublic
	 */
	private static $instance;

	/**
	 * @return ao_AoServicePublic
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @param ao_persistentdocument_ao $ao
	 * @param ao_persistentdocument_company $company
	 */
	public function updateStatsOnAOView($ao, $company)
	{
		parent::updateStatsOnAOView($ao, $company);
	}
}