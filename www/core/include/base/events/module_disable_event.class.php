<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Module Disable Event
 * @package base
 */
class ModuleDisableEvent extends Event
{    
	private $module_id;
	
	function __construct($module_id)
    {
    	if (is_numeric($module_id))
    	{
    		parent::__construct();
    		$this->module_id = $module_id;
    	}
    	else
    	{
    		$this->module_id = null;
    	}
    }
    
    public function get_module_id()
    {
    	if ($this->module_id)
    	{
    		return $this->module_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>