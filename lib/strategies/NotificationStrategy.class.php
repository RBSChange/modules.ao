<?php
abstract class ao_NotificationStrategy extends BaseService
{
	/**
	 * @var ao_NotificationStrategy
	 */
	private static $strategy = null;

	/**
	 * @param ao_NotificationStrategy $strategy
	 */
	public final static function setStrategy($strategy)
	{
		self::$strategy = $strategy;
	}

	/**
	 * @return ao_NotificationStrategy
	 */
	public final static function getStrategy()
	{
		if (is_null(self::$strategy))
		{
			try
			{
				$className = Framework::getConfiguration('modules/ao/notificationStrategyClass');
			}
			catch (ConfigurationException $e)
			{
				// No strategy defined in the project's config file: use default one.
				$className = 'ao_NotificationDefaultStrategy';
				if (Framework::isDebugEnabled())
				{
					Framework::debug("No strategy defined for notifications for this projet: using default one (".$className.").");
				}
			}
			self::$strategy = f_util_ClassUtils::callMethodByName($className . '::getInstance');
		}
		return self::$strategy;
	}

	/**
	 * @param String $codeName
	 * @param Array<ao_persistentdocument_ao> $ao
	 * @return Boolean
	 */
	public abstract function sendNotification($codeName, $ao);
}