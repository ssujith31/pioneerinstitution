<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JLanguageHelper.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Language
 * @since       1.7.0
 */
class JLanguageHelperTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test...
	 *
	 * @return void
	 */
	public function testCreateLanguageList()
	{
		$option = array(
			'text'     => 'English (United Kingdom)',
			'value'    => 'en-GB',
			'selected' => 'selected="selected"'
		);
		$listCompareEqual = array(
			0 => $option,
		);

		$list = JLanguageHelper::createLanguageList('en-GB', __DIR__ . '/data', false);
		$this->assertEquals(
			$listCompareEqual,
			$list
		);
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	public function testDetectLanguage()
	{
		$lang = JLanguageHelper::detectLanguage();

		// Since we're running in a CLI context we can only check the default value
		$this->assertNull(
			$lang
		);
	}
}
