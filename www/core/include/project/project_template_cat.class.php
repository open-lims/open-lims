<?php
/**
 * @package project
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
require_once("interfaces/project_template_cat.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_template_cat.access.php");
}

/**
 * Project Template Category Management Class
 * @package project
 */
class ProjectTemplateCat implements ProjectTemplateCatInterface
{
	private $project_template_cat_id;
	private $project_template_cat;

    /**
     * @see ProjectTemplateCatInterface::__construct()
	 * @param integer $project_template_cat_id
	 * @throws ProjectTemplateCategoryNotFoundException
	 */
	function __construct($project_template_cat_id)
	{
		if (is_numeric($project_template_cat_id))
		{
			if (ProjectTemplateCat_Access::exist_id($project_template_cat_id) == true)
			{
				$this->project_template_cat_id = $project_template_cat_id;
				$this->project_template_cat = new ProjectTemplateCat_Access($project_template_cat_id);
			}
			else
			{
				throw new ProjectTemplateCategoryNotFoundException();
			}
		}
		else
		{
			$this->project_template_cat_id = null;
			$this->project_template_cat = new ProjectTemplateCat_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->project_template_cat_id);
		unset($this->project_template_cat);
	}
	
	/**
	 * @see ProjectTemplateCatInterface::create()
	 * @param string $name
	 * @return integer
	 * @throws ProjectTemplateCategoryCreateException
	 */
	public function create($name)
	{
		if ($this->project_template_cat and $name)
		{
			if (($return_value = $this->project_template_cat->create($name)) != null)
			{
				return $return_value;
			}
			else
			{
				throw new ProjectTemplateCategoryCreateException();
			}
		}
		else
		{
			throw new ProjectTemplateCategoryCreateException();
		}
	}
	
	/**
	 * @see ProjectTemplateCatInterface::delete()
	 * @return bool
	 * @throws ProjectTemplateCategoryDeleteException
	 */
	public function delete()
	{
		if ($this->project_template_cat and $this->project_template_cat_id)
		{
			$project_template_array = ProjectTemplate::list_entries_by_cat_id($this->project_template_cat_id);
			if (is_array($project_template_array))
			{
				if (count($project_template_array) == 0)
				{
					if ($this->project_template_cat->delete() == true)
					{
						return true;
					}
					else
					{
						throw new ProjectTemplateCategoryDeleteException();
					}
				}
				else
				{
					throw new ProjectTemplateCategoryDeleteException();
				}
			}
			else
			{
				if ($this->project_template_cat->delete() == true)
				{
					return true;
				}
				else
				{
					throw new ProjectTemplateCategoryDeleteException();
				}
			}
		}
		else
		{
			throw new ProjectTemplateCategoryDeleteException();
		}
	}
	
	/**
	 * @see ProjectTemplateCatInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->project_template_cat and $this->project_template_cat_id)
		{
			return $this->project_template_cat->get_name();
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @see ProjectTemplateCatInterface::set_name()
	 * @param string $name
	 * @return integer
	 */
	public function set_name($name)
	{
		if ($this->project_template_cat and $this->project_template_cat_id and $name)
		{
			return $this->project_template_cat->set_name($name);
		}
		else
		{
			return false;
		}	
	}
	
	
	/**
	 * @see ProjectTemplateCatInterface::exist_name()
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		return ProjectTemplateCat_Access::exist_name($name);
	}
	
	/**
	 * @see ProjectTemplateCatInterface::exist_id()
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return ProjectTemplateCat_Access::exist_id($id);
	}
	
	/**
	 * @see ProjectTemplateCatInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return ProjectTemplateCat_Access::list_entries();
	}
	
}
?>