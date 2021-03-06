<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JLanguageStemmerPorteren.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Language
 */
class JLanguageStemmerPorterenTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var JLanguageStemmerPorteren
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->object = new JLanguageStemmerPorteren;
	}

	/**
	 * Data provider for testStem()
	 *
	 * @return array
	 */
	public function dataWords()
	{
		return array(
			array('Car', 'Car', 'en'),
			array('Cars', 'Car', 'en'),
			array('fishing', 'fish', 'en'),
			array('fished', 'fish', 'en'),
			array('fish', 'fish', 'en'),
			array('powerful', 'power', 'en'),
			array('Reflect', 'Reflect', 'en'),
			array('Reflects', 'Reflect', 'en'),
			array('Reflected', 'Reflect', 'en'),
			array('stemming', 'stem', 'en'),
			array('stemmed', 'stem', 'en'),
			array('walk', 'walk', 'en'),
			array('walking', 'walk', 'en'),
			array('walked', 'walk', 'en'),
			array('walks', 'walk', 'en'),
			array('us', 'us', 'en'),
			array('I', 'I', 'en'),
			array('Standardabweichung', 'Standardabweichung', 'de')
		);
	}

	/**
	 * @param   string $token
	 * @param   string $result
	 * @param   string $lang
	 *
	 * @dataProvider dataWords
	 */
	public function testStem($token, $result, $lang)
	{
		$this->assertEquals(
			$result,
			$this->object->stem($token, $lang)
		);
	}
}
