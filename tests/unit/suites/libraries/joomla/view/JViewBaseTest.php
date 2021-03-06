<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  View
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

JLoader::register('BaseView', __DIR__ . '/stubs/tbase.php');
JLoader::register('JModelMock', __DIR__ . '/mocks/JModelMock.php');

/**
 * Tests for the JViewBase class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  View
 * @since       3.0.0
 */
class JViewBaseTest extends TestCase
{
	/**
	 * @var    JViewBase
	 * @since  3.0.0
	 */
	private $_instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @covers  JViewBase::__construct
	 * @since   3.0.0
	 */
	public function test__construct()
	{
		$this->assertAttributeInstanceOf('JModel', 'model', $this->_instance);
	}

	/**
	 * Tests the escape method.
	 *
	 * @return  void
	 *
	 * @covers  JViewBase::escape
	 * @since   3.0.0
	 */
	public function testEscape()
	{
		$this->assertEquals('foo', $this->_instance->escape('foo'));
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$model = JModelMock::create($this);

		$this->_instance = new BaseView($model);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		unset($this->_instance);
		parent::tearDown();
	}
}
