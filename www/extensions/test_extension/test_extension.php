<?php
/**
 * @package extension
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
 * @license GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

/**
 * test extension
 * @package extension
 */
class TestExtension implements ConreteExtensionInterface
{
	/**
	 * @return string
	 */
	public static function get_icon()
	{
		return null;
	}
	
	/**
	 * @return string
	 */
	public static function get_description()
	{
		return "Lorum ipsum dolor sit amet";
	}
	
	/**
	 * @param array $data
	 * @return integer
	 */
	public static function push_data($data)
	{
		require_once("io/test_extension.io.php");
		TestExtensionIO::set_template_path("extensions/test_extension/template");
		TestExtensionIO::start($data);
	}
	
	/**
	 * @param integer $status_id
	 * @return integer
	 */
	public static function get_data_status($status_id)
	{
		require_once("classes/test.class.php");
		$test = new Test($status_id);
		return $test->get_status();
	}
	
	public static function main()
	{
		require_once("io/test_extension.io.php");
		TestExtensionIO::set_template_path("extensions/test_extension/template");
		TestExtensionIO::home();
	}
}
?>