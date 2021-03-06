<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Inspector for JHtmlBootstrap
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       3.1
 */
abstract class JHtmlBehaviorInspector extends JHtmlBehavior
{
	/**
	 * Resets the JHtmlBehavior::$loaded array
	 *
	 * @return  mixed  void
	 *
	 * @since   3.1
	 */
	public static function resetLoaded()
	{
		static::$loaded = array();
	}

	/**
	 * Inspects the JHtmlBehavior::$loaded array
	 *
	 * @return  mixed  The value of the class variable.
	 *
	 * @since   3.1
	 */
	public static function getLoaded()
	{
		return static::$loaded;
	}
}
