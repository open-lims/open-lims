<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/folder_is_system_folder.access.php");
}

/**
 * System Folder Class
 * @package data
 */
class SystemFolder extends Folder implements ConcreteFolderCaseInterface
{  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		if (is_numeric($folder_id))
  		{
  			parent::__construct($folder_id);
  			$this->data_entity_permission->set_read_permission();
  			
  			if ($this->data_entity_permission->is_access(1))
			{
				$this->read_access = true;
			}
  		}
  		else
  		{
  			parent::__construct(null);
  		}
  	}
  	
	function __destruct()
	{
		parent::__destruct();
	}

	/**
	 * @return bool
	 */
	protected function get_inherit_permission()
	{
		return false;
	}
		
	public function is_write_access($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see DataEntityInterface::is_delete_access()
	 * @return bool
	 */
	public function is_delete_access($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see FolderInterface::can_command_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_rename_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see ConcreteFolderCaseInterface::delete()
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		return false;
	}
	
	
	/**
	 * @see ConcreteFolderCaseInterface::is_case()
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id)
	{
		if (is_numeric($folder_id))
		{
			$folder_is_system_folder_access = new FolderIsSystemFolder_Access($folder_id);
			if ($folder_is_system_folder_access->get_folder_id())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}