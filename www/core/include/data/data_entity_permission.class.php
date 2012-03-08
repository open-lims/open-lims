<?php
/**
 * @package data
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
require_once("interfaces/data_entity_permission.interface.php");

/**
 * Data Entity Permission Management Class
 * @package data
 */
class DataEntityPermission implements DataEntityPermissionInterface
{
	private $permission;
	private $automatic;
	private $owner_id;
	private $owner_group_id;
	
	// private $folder_flag;
	private $read_permission = false;
	private $write_permission = false;
	
	/**
	 * @see DataEntityPermissionInterface::__construct()
	 * @param integer $permission
	 * @param bool $automatic
	 * @param integer $owner_id
	 * @param integer $owner_group_id
	 */
	function __construct($permission, $automatic, $owner_id, $owner_group_id)
	{
		if (isset($automatic) and is_numeric($owner_id))
		{
			$this->permission = $permission;
			$this->automatic = $automatic;
			$this->owner_id = $owner_id;
			$this->owner_group_id = $owner_group_id;
		}
		else
		{
			$this->permission = null;
			$this->automatic = null;
			$this->owner_id = null;
			$this->owner_group_id = null;
		}
	}
	
	function __destruct()
	{
		unset($this->permission);
		unset($this->automatic);
		unset($this->owner_id);
		unset($this->owner_group_id);		
		unset($this->folder_flag);
	}
	
	/**
	 * @see DataEntityPermissionInterface::set_write_permission()
	 */
	public function set_read_permission()
	{
		$this->read_permission = true;
	}
	
	/**
	 * @see DataEntityPermissionInterface::set_write_permission()
	 */
	public function set_write_permission()
	{
		$this->write_permission = true;
	}
	
	/**
	 * @see DataEntityPermissionInterface::is_access()
	 * @param integer $intention
	 * @return bool
	 */	
	public function is_access($intention)
	{
		global $user;

		if ($user->is_admin())
		{	
			return true;	
		}
		else
		{
			if ($this->automatic == true)
			{	
				if ($this->read_permission == true and $intention == 1)
				{
					return true;
				}
				elseif ($this->write_permission == true and $intention == 2)
				{
					return true;
				}
				else
				{
					if ($this->owner_id == $user->get_user_id())
					{
						return true;
					}
					else
					{
						$group = new Group($this->owner_group_id);
						if ($group->is_user_in_group($user->get_user_id()) == true and $intention == 1)
						{
							return true;	
						}
						else
						{
							return false;
						}
					}
				}	
			}
			else
			{	
				$permission_bin = decbin($this->permission);
				
				$permission_bin = str_pad($permission_bin, 16, "0", STR_PAD_LEFT);
				$permission_bin = strrev($permission_bin);	

				// Owner		
				if ($this->owner_id == $user->get_user_id())
				{
					switch($intention):
						case 1:
							return true;
						break;
						
						case 2:
							if ($permission_bin{1} == "1")
							{
								return true;
							}
						break;
						
						case 3:
							if ($permission_bin{2} == "1")
							{
								return true;
							}
						break;
						
						default:
							if ($permission_bin{3} == "1")
							{
								return true;
							}
						break;							
					endswitch;	
				}
				
				// Group
				if ($this->owner_group_id != 0)
				{
					$group = new Group($this->owner_group_id);
					
					if ($group->is_user_in_group($user->get_user_id()))
					{
						switch($intention):
							case 1:
								if ($permission_bin{4} == "1")
								{
									return true;
								}
							break;
							
							case 2:
								if ($permission_bin{5} == "1")
								{
									return true;
								}
							break;
							
							case 3:
								if ($permission_bin{6} == "1")
								{
									return true;
								}
							break;
							
							default:
								if ($permission_bin{7} == "1")
								{
									return true;
								}
							break;							
						endswitch;	
					}
				}
				
				// Public				
				switch($intention):
					case 1:
						if ($permission_bin{12} == "1")
						{
							return true;
						}
					break;
					
					case 2:
						if ($permission_bin{13} == "1")
						{
							return true;
						}
					break;
					
					case 3:
						if ($permission_bin{14} == "1")
						{
							return true;
						}
					break;
					default:
						if ($permission_bin{15} == "1")
						{
							return true;
						}
					break;							
				endswitch;	
				
				return false;	
			}	
		}
	}
	
	/**
	 * @see DataEntityPermissionInterface::get_permission_string()
	 * @return string
	 */	
	public function get_permission_string()
	{
		global $db;
				
		if ($this->automatic == true)
		{	
			return "automatic";	
		}
		else
		{
			$dec_string = decbin($this->permission);
			$dec_string = str_pad($dec_string, 16 ,'0', STR_PAD_LEFT);	
					
			$counter = 1;
						
			for ($i=0;$i<=15;$i++)
			{			
				if ($dec_string{$i} == '1')
				{
					switch ($counter):
						case 1:
						$returnstring = "c".$returnstring;
						break;
						
						case 2:
						$returnstring = "d".$returnstring;
						break;
						
						case 3:
						$returnstring = "w".$returnstring;
						break;
						
						default:
						$returnstring = "r".$returnstring;
						break;
					endswitch;
				}
				else
				{
					$returnstring = "-".$returnstring;
				}

				if ($counter >= 4)
				{
					$counter = 1;
				}
				else
				{
					$counter++;
				}
			}
			return $returnstring;	
		}	
	}	
	
}

?>
