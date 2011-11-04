<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * 
 */
require_once("interfaces/project_status.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_status.access.php");
	require_once("access/project_has_project_status.access.php");
}

/**
 * Project Status Management Class
 * @package project
 */
class ProjectStatus implements ProjectStatusInterface
{
    private $status_id;
    private $status;    
    
    /**
     * @see ProjectStatusInterface::__construct()
     * @param integer $status_id
     * @throws ProjectStatusNotFoundException
     */
    function __construct($status_id)
    {
    	if (is_numeric($status_id))
		{
			if (ProjectStatus_Access::exist_id($status_id) == true)
			{
				$this->status_id = $status_id;
    			$this->status = new ProjectStatus_Access($status_id);
			}
			else
			{
				throw new ProjectStatusNotFoundException();
			}
    	}
    	else
    	{
    		$this->status_id = null;
    		$this->status = new ProjectStatus_Access(null);
    	}
    }
    
    function __destruct()
    {
    	unset($this->status_id);
    	unset($this->status);
    }
    
    /**
     * @see ProjectStatusInterface::create()
     * @param string $name
     * @param string $comment
     * @return integer
     * @throws ProjectStatusCreateException
     */
    public function create($name, $comment)
    {
    	if ($this->status)
    	{
    		if (($return_value = $this->status->create($name, $comment)) != null)
    		{
    			return $return_value;
    		}
    		else
    		{
    			throw new ProjectStatusCreateException();
    		}
    	}
    	else
    	{
    		throw new ProjectStatusCreateException();
    	}
    }
    
    /**
     * @see ProjectStatusInterface::delete()
     * @return bool
     * @throws ProjectStatusDeleteException;
     */
    public function delete()
    {
    	if ($this->status and $this->status_id)
    	{
    		$project_relation_array = ProjectHasProjectStatus_Access::list_entries_by_status_id($this->status_id);
    		if (is_array($project_relation_array))
    		{
    			if (count($project_relation_array) == 0)
    			{
    				if ($this->status->delete() == true)
    				{
    					return true;
    				}
    				else
    				{
    					throw new ProjectStatusDeleteException();
    				}
    			}
    			else
    			{
    				throw new ProjectStatusDeleteException();
    			}
    		}
    		else
    		{
    			if ($this->status->delete() == true)
    			{
    				return true;
    			}
    			else
    			{
    				throw new ProjectStatusDeleteException();
    			}
    		}
    	}
    	else
    	{
    		throw new ProjectStatusDeleteException();
    	}
    }
    
    /**
     * @see ProjectStatusInterface::get_name()
     * @return string
     */
    public function get_name()
    {
    	if ($this->status_id and $this->status)
    	{
    		return $this->status->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectStatusInterface::get_blocked()
     * @return bool
     */
    public function get_blocked()
    {
    	if ($this->status_id and $this->status)
    	{
    		return $this->status->get_blocked();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ProjectStatusInterface::set_name()
     * @param string $name
     * @return bool
     */
    public function set_name($name)
    {
    	if ($this->status and $this->status_id and $name)
    	{
    		return $this->status->set_name($name);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    
    /**
     * @see ProjectStatusInterface::exist_id()
     * @param integer $id
     * @return bool
     */
    public static function exist_id($id)
    {
    	return ProjectStatus_Access::exist_id($id);
    }
    
}
?>