<?php
/**
 * @package base
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
require_once("interfaces/module_navigation.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_module_navigation.access.php");
}

/**
 * Module Navigation Class
 * @package base
 */
class ModuleNavigation implements ModuleNavigationInterface, EventListenerInterface
{	
	private $navigation_id;
	private $navigaiton;
	
	/**
	 * @param integer $navigation_id
	 */
	function __construct($navigation_id)
	{
		if (is_numeric($navigation_id))
		{
			$this->navigation_id = $navigation_id;
			$this->navigation = new BaseModuleNavigation_Access($navigation_id);
		}
		else
		{
			$this->navigation_id = null;
			$this->navigation = null;
		}
	}
	
	function __destruct()
	{
		unset($this->navigation_id);
		unset($this->navigation);
	}	

	/**
	 * @see ModuleNavigationInterface::upwards()
	 * @return bool
	 */
	public function upwards()
	{
		global $transaction;
		
		if ($this->navigation and $this->navigation_id)
		{
			if ($this->navigation->get_position() != 1)
			{
				$upper_position = $this->navigation->get_position()-1;
				$current_position = $this->navigation->get_position();
				$id = BaseModuleNavigation_Access::get_id_by_position($upper_position);
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_navigation = new BaseModuleNavigation_Access($id);
					if ($change_navigation->set_position(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->navigation->set_position($upper_position) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($change_navigation->set_position($current_position) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return true;
					}	
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ModuleNavigationInterface::downwards()
	 * @return bool
	 */
	public function downwards()
	{
		global $transaction;
		
		if ($this->navigation and $this->navigation_id)
		{
			if ($this->navigation->get_position() != BaseModuleNavigation_Access::get_highest_position())
			{
				$lower_position = $this->navigation->get_position()+1;
				$current_position = $this->navigation->get_position();
				$id = BaseModuleNavigation_Access::get_id_by_position($lower_position);
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_navigation = new BaseModuleNavigation_Access($id);
					if ($change_navigation->set_position(null) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->navigation->set_position($lower_position) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($change_navigation->set_position($current_position) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					else
					{
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						return true;
					}	
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ModuleNavigationInterface::hide()
	 * @return bool
	 */
	public function hide()
	{
		if ($this->navigation->get_hidden() == true)
		{
			return $this->navigation->set_hidden(false);
		}
		else
		{
			return $this->navigation->set_hidden(true);
		}
	}
	
	
	/**
	 * @see ModuleNavigationInterface::get_highest_position()
	 * @return integer
	 */
	public static function get_highest_position()
	{
		return BaseModuleNavigation_Access::get_highest_position();
	}
	
 	/**
 	 * @see ModuleNavigationInterface::list_module_navigations_entries()
	 * @return array
	 */
	public static function list_module_navigations_entries()
	{
		self::clean_up();
		return BaseModuleNavigation_Access::list_entries();
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof ModuleDisableEvent)
    	{
    		$id = BaseModuleNavigation_Access::get_id_by_module_id($event_object->get_module_id());
    		if (is_numeric($id))
    		{
    			$module_navigation = new BaseModuleNavigation_Access($id);
    			if ($module_navigation->get_hidden() == false)
    			{
	    			if ($module_navigation->set_hidden(true) == false)
	    			{
	    				return false;
	    			}
    			}
    		}
    	}
    	
    	if ($event_object instanceof ModuleEnableEvent)
    	{
    		$id = BaseModuleNavigation_Access::get_id_by_module_id($event_object->get_module_id());
    		if (is_numeric($id))
    		{
    			$module_navigation = new BaseModuleNavigation_Access($id);
    			if ($module_navigation->get_hidden() == true)
    			{
	    			if ($module_navigation->set_hidden(false) == false)
	    			{
	    				return false;
	    			}
    			}
    		}
    	}
    	
    	return true;
    }
    
	/**
	 * @todo implementation
	 * Checks the sort-ID of the menu-entries and resorts them
	 * @return bool
	 */
	private static function clean_up()
	{
		if (BaseModuleNavigation_Access::check_position() == false)
		{
			$entry_array = BaseModuleNavigation_Access::list_ids();
			$number_of_entries = BaseModuleNavigation_Access::count_entries();
			
			for($i=1;$i<=$number_of_entries;$i++)
			{
				$base_module_navigation = new BaseModuleNavigation_Access($entry_array[$i-1]);
				$base_module_navigation->set_position($i);
			}
		}
	}
}

?>