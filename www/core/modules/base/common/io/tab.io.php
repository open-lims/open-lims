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
 * Tab IO Class
 * Handles Tab-Bars
 * @package base
 */
class Tab_IO
{
	private $max_tabs;
	private $tab_array;
	
	/**
	 * @param integer $max_tabs
	 */
	function __construct($max_tabs = 7)
	{
		$this->max_tabs = $max_tabs;
	}
	
	function __destruct()
	{
		unset($this->max_tabs);
		unset($this->tab_array);
	}
	
	/**
	 * @param string $name
	 * @param string $display_name
	 * @param array $target
	 * @param bool $active
	 * @param bool $disabled
	 * @param string $css_id
	 * @return bool
	 */
	public function add($name, $display_name, $target, $active, $disabled = false, $css_id = null)
	{		
		if ($name and $display_name and ($target or $css_id))
		{
			$this->tab_array[$name]['name'] = $display_name;
			
			if ($target)
			{
				$this->tab_array[$name]['target'] = $target;
			}
	
			if ($active == true)
			{
				$this->tab_array[$name]['active'] = true;
			}
			else
			{
				$this->tab_array[$name]['active'] = false;
			}
			
			if ($disabled == true)
			{
				$this->tab_array[$name]['disabled'] = true;
			}
			else
			{
				$this->tab_array[$name]['disabled'] = false;
			}
			
			if ($css_id == true)
			{
				$this->tab_array[$name]['css_id'] = $css_id;
			}
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
	public function activate($name)
	{
		if ($this->tab_array[$name]['name'])
		{
			foreach ($this->tab_array as $key => $value)
			{
				$this->tab_array[$key]['active'] = false;
			}
			$this->tab_array[$name]['active'] = true;
			return true;
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
	public function disable($name)
	{
		if ($this->tab_array[$name]['name'])
		{
			$this->tab_array[$name]['disabled'] = true;
			return true;
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
	public function enable($name)
	{
		if ($this->tab_array[$name]['name'])
		{
			$this->tab_array[$name]['disabled'] = false;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function output()
	{
		echo $this->get_string();
	}
	
	/**
	 * @return string
	 */
	public function get_string()
	{
		$return = "";
		
		$template = new HTMLTemplate("base/tabs/small_tab_header.html");
		$return .= $template->get_string();
		
		if (is_array($this->tab_array) and count($this->tab_array) >= 1)
		{
			foreach ($this->tab_array as $key => $value)
			{
				if ($value['disabled'])
				{
					$template = new HTMLTemplate("base/tabs/generic_inactive.html");
					$template->set_var("title", $value['name']);
					$return .= $template->get_string();
				}
				else
				{
					if ($value['active'] == true)
					{
						$template = new HTMLTemplate("base/tabs/generic_active.html");
						$template->set_var("title", $value['name']);
						$template->set_var("params", $value['target']);
						$return .= $template->get_string();
					}
					else
					{
						$template = new HTMLTemplate("base/tabs/generic.html");
						$template->set_var("title", $value['name']);
						$template->set_var("params", $value['target']);
						$return .= $template->get_string();
					}
				}
			}
		}
		
		$template = new HTMLTemplate("base/tabs/small_tab_footer.html");
		$return .= $template->get_string();
		
		return $return;
	}
}
?>