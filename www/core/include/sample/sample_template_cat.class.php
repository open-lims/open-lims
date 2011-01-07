<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
require_once("interfaces/sample_template_cat.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/sample_template_cat.access.php");
}

/**
 * Sample Template Category Management Class
 * @package sample
 */
class SampleTemplateCat implements SampleTemplateCatInterface
{
	private $sample_template_cat_id;
	private $sample_template_cat;

    /**
	 * @param integer $sample_template_cat_id
	 */
	function __construct($sample_template_cat_id)
	{
		if ($sample_template_cat_id == null)
		{
			$this->sample_template_cat_id = null;
			$this->sample_template_cat = new SampleTemplateCat_Access(null);
		}
		else
		{
			$this->sample_template_cat_id = $sample_template_cat_id;
			$this->sample_template_cat = new SampleTemplateCat_Access($sample_template_cat_id);
		}
	}
	
	function __destruct()
	{
		unset($this->sample_template_cat_id);
		unset($this->sample_template_cat);
	}
	
	/**
	 * Creates a new sample-tempalte-category
	 * @param string $name
	 * @return integer
	 */
	public function create($name)
	{
		if ($this->sample_template_cat and $name)
		{
			return $this->sample_template_cat->create($name);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Deletes a sample-template-category
	 * @return bool
	 */
	public function delete()
	{
		if ($this->sample_template_cat and $this->sample_template_cat_id)
		{
			$sample_template_array = SampleTemplate::list_entries_by_cat_id($this->sample_template_cat_id);
			if (is_array($sample_template_array))
			{
				if (count($sample_template_array) == 0)
				{
					return $this->sample_template_cat->delete();
				}
				else
				{
					return false;
				}
			}
			else
			{
				return $this->sample_template_cat->delete();
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		if ($this->sample_template_cat and $this->sample_template_cat_id)
		{
			return $this->sample_template_cat->get_name();
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param string $name
	 * @return integer
	 */
	public function set_name($name)
	{
		if ($this->sample_template_cat and $this->sample_template_cat_id and $name)
		{
			return $this->sample_template_cat->set_name($name);
		}
		else
		{
			return false;
		}	
	}
	
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		return SampleTemplateCat_Access::exist_name($name);
	}
	
	/**
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return SampleTemplateCat_Access::exist_id($id);
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		return SampleTemplateCat_Access::list_entries();
	}
	
}
?>