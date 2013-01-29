<?php
/**
 * @package extension
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
require_once("interfaces/extension.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/extension.access.php");
}

/**
 * Extension Class
 * @package extension
 */
class Extension implements ExtensionInterface
{
	private $extension_id;
	private $extension;
	
	/**
	 * @see ExtensionInterface::__construct()
	 * @param integer $extension_id
	 */
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
	
	/**
	 * @see ExtensionInterface::get_folder()
	 * @return string
	 */
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
	
	/**
	 * @see ExtensionInterface::get_class()
	 * @return string
	 */
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
	
	/**
	 * @see ExtensionInterface::get_main_file()
	 * @return string
	 */
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
	
	/**
	 * @see ExtensionInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->extension_id)
		{
			return $this->extension->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ExtensionInterface::get_run_status()
	 * @param integer $run_id
	 * @return integer
	 */
	public function get_run_status($run_id)
	{
		if ($this->extension_id and is_numeric($run_id))
		{
			$main_file = constant("EXTENSION_DIR")."/".$this->extension->get_folder()."/".$this->extension->get_main_file();
			$main_class = $this->extension->get_class();
			
			if (file_exists($main_file))
			{
				require_once($main_file);
				return $main_class::get_data_status($run_id);
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @see ExtensionInterface::get_id_by_identifier()
	 * @param string $identifier
	 * @return integer
	 */
	public static function get_id_by_identifier($identifier)
	{
		return Extension_Access::get_id_by_identifier($identifier);
	}
}
?>