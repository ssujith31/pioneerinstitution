<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  (C) 2012 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Abstract test case class for MySQL database testing.
 *
 * @package  Joomla.Test
 * @since    3.0.0
 */
abstract class TestCaseDatabaseMysql extends TestCaseDatabase
{
	/**
	 * @var    JDatabaseDriverMysql  The active database driver being used for the tests.
	 * @since  3.0.0
	 */
	protected static $driver;

	/**
	 * @var    array  The JDatabaseDriver options for the connection.
	 * @since  3.0.0
	 */
	private static $_options = array('driver' => 'mysql');

	/**
	 * @var    JDatabaseDriverMysql  The saved database driver to be restored after these tests.
	 * @since  3.0.0
	 */
	private static $_stash;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public static function setUpBeforeClass()
	{
		if (PHP_MAJOR_VERSION >= 7)
		{
			static::markTestSkipped('ext/mysql is unsupported on PHP 7.');
		}

		// First let's look to see if we have a DSN defined or in the environment variables.
		if (!defined('JTEST_DATABASE_MYSQL_DSN') && !getenv('JTEST_DATABASE_MYSQL_DSN'))
		{
			static::markTestSkipped('The MySQL driver is not configured.');
		}

		$dsn = defined('JTEST_DATABASE_MYSQL_DSN') ? JTEST_DATABASE_MYSQL_DSN : getenv('JTEST_DATABASE_MYSQL_DSN');

		// First let's trim the mysql: part off the front of the DSN if it exists.
		if (strpos($dsn, 'mysql:') === 0)
		{
			$dsn = substr($dsn, 6);
		}

		// Split the DSN into its parts over semicolons.
		$parts = explode(';', $dsn);

		// Parse each part and populate the options array.
		foreach ($parts as $part)
		{
			list ($k, $v) = explode('=', $part, 2);

			switch ($k)
			{
				case 'host':
					self::$_options['host'] = $v;
					break;
				case 'dbname':
					self::$_options['database'] = $v;
					break;
				case 'user':
					self::$_options['user'] = $v;
					break;
				case 'pass':
					self::$_options['password'] = $v;
					break;
			}
		}

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = JDatabaseDriver::getInstance(self::$_options);
		}
		catch (RuntimeException $e)
		{
			static::$driver = null;
		}

		// If for some reason an exception object was returned set our database object to null.
		if (static::$driver instanceof Exception)
		{
			static::$driver = null;
		}

		// Setup the factory pointer for the driver and stash the old one.
		self::$_stash = JFactory::$database;
		JFactory::$database = static::$driver;
	}

	/**
	 * This method is called after the last test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public static function tearDownAfterClass()
	{
		JFactory::$database = self::$_stash;

		if (static::$driver !== null)
		{
			static::$driver->disconnect();
			static::$driver = null;
		}
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 *
	 * @since   3.0.0
	 */
	protected function getConnection()
	{
		// Compile the connection DSN.
		$dsn = 'mysql:host=' . self::$_options['host'] . ';dbname=' . self::$_options['database'];

		// Create the PDO object from the DSN and options.
		$pdo = new PDO($dsn, self::$_options['user'], self::$_options['password']);

		return $this->createDefaultDBConnection($pdo, self::$_options['database']);
	}
}
