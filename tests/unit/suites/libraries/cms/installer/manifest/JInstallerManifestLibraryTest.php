<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Installer
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JInstallerManifestLibrary.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Installer
 * @since       3.1
 */
class JInstallerManifestLibraryTest extends TestCase
{
	/**
	 * @var JInstallerManifestLibrary
	 */
	protected $object;

	/**
	 * Test...
	 *
	 * @covers  JInstallerManifestLibrary::__construct
	 * @covers  JInstallerManifest::loadManifestFromXML
	 * @covers  JInstallerManifestLibrary::loadManifestFromData
	 *
	 * @return void
	 */
	public function testLoadManifestFromData()
	{
		$this->object = new JInstallerManifestLibrary(dirname(__DIR__) . '/data/joomla.xml');

		$this->assertEquals(
			'Joomla! Platform',
			$this->object->name
		);

		$this->assertEquals(
			'joomla',
			$this->object->libraryname
		);

		$this->assertEquals(
			'11.4',
			$this->object->version
		);

		$this->assertEquals(
			'LIB_JOOMLA_XML_DESCRIPTION',
			$this->object->description
		);

		$this->assertEquals(
			'2008',
			$this->object->creationdate
		);

		$this->assertEquals(
			'Joomla! Project',
			$this->object->author
		);

		$this->assertEquals(
			'admin@joomla.org',
			$this->object->authoremail
		);

		$this->assertEquals(
			'https://www.joomla.org',
			$this->object->authorurl
		);

		$this->assertEquals(
			'Joomla!',
			$this->object->packager
		);

		$this->assertEquals(
			'https://www.joomla.org',
			$this->object->packagerurl
		);

		$this->assertEquals(
			'https://update.joomla.org/libraries/joomla',
			$this->object->update
		);
	}
}
