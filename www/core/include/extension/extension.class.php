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
 * 
 */
// require_once("interfaces/extension.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/extension.access.php");
}

/**
 * Extension Class
 * @package extension
 */
class Extension
{
	private $extension_id;
	private $extension;
	
	function __construct($extension_id)
	{
		if (is_numeric($extension_id))
		{
			$this->extension_id = $extension_id;
			$this->extension = new Extension_Access($extension_id);
		}
		else
		{
			$this->extension_id = null;
			$this->extension = new Extension_Access(null);
		}
	}
	
	public function get_folder()
	{
		if ($this->extension_id)
		{
			return $this->extension->get_folder();
		}
		else
		{
			return null;
		}
	}
	
	public function get_class()
	{
		if ($this->extension_id)
		{
			return $this->extension->get_class();
		}
		else
		{
			return null;
		}
	}
	
	public function get_main_file()
	{
		if ($this->extension_id)
		{
			return $this->extension->get_main_file();
		}
		else
		{
			return null;
		}
	}
	
	
	public static function get_id_by_identifer($identifer) 
	{
		return Extension_Access::get_id_by_identifer($identifer);
	}
}
?>