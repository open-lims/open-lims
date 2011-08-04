<?php
/**
 * @package base
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
 * Assistant Bar Class
 * Manages the bars of assistants
 * @package base
 */
class AssistantBarIO {
	
	private $active = "_blue.png";
	private $visited = "_dgrey.png";
	private $unvisited = "_lgrey.png";
	
	private $screen_array = array();
	
	/**
	 * Adds a screen to the assistant
	 * @param integer $id
	 * @param string $name
	 * @param string $link
	 */
	public function add_screen($id, $name, $link)
	{
		if ($name and is_numeric($id))
		{
			if ($link)
			{
				$this->screen_array[$id][link] = $link;
			}
			
			$this->screen_array[$id][name] = $name;
			$this->screen_array[$id][status] = 0;
		}
	}
	
	/**
	 * Sets a screen as active
	 * @param integer $id
	 * @return bool
	 */
	public function set_active($id)
	{
		if (is_numeric($id) and is_array($this->screen_array[$id]))
		{
			$this->screen_array[$id][status] = 1;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Sets a screen as visited
	 * @param integer $id
	 * @return bool
	 */
	public function set_visited($id)
	{
		if (is_numeric($id) and is_array($this->screen_array[$id]))
		{
			$this->screen_array[$id][status] = 2;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_content()
	{
		if (is_array($this->screen_array) and count($this->screen_array) >= 1)
		{
			$return = "";
			
			$template = new Template("template/assistants/header.html");
			$return .= $template->get_string();
			
			$element_width = floor(730/count($this->screen_array));
			
			foreach($this->screen_array as $key => $value)
			{
				switch($value[status]):
				
					case(0):
						$template = new Template("template/assistants/element.html");
						$template->set_var("width", $element_width);
						$template->set_var("image", $key."".$this->unvisited);
						$template->set_var("text", $value[name]);
						$template->set_var("href", "");
						$return .= $template->get_string();
					break;
					
					case(1):
						$template = new Template("template/assistants/element.html");
						$template->set_var("width", $element_width);
						$template->set_var("image", $key."".$this->active);
						$template->set_var("text", $value[name]);
						if ($value[link])
						{
							$paramquery = $value[link];
							$paramquery[tpage] = $key;
							$params = http_build_query($paramquery, '', '&#38;');
							$template->set_var("href", "onclick='document.forms[1].submit();' href='index.php?".$params."'");
						}
						else
						{
							$template->set_var("href", "");
						}
						$return .= $template->get_string();
					break;
					
					case(2):
						$template = new Template("template/assistants/element.html");
						$template->set_var("width", $element_width);
						$template->set_var("image", $key."".$this->visited);
						$template->set_var("text", $value[name]);
						if ($value[link])
						{
							$paramquery = $value[link];
							$paramquery[tpage] = $key;
							$params = http_build_query($paramquery, '', '&#38;');
							$template->set_var("href", " onclick='document.forms[1].submit();' href='index.php?".$params."'");
						}
						else
						{
							$template->set_var("href", "");
						}
						$return .= $template->get_string();
					break;
				
				endswitch;
					
			}
			
			$template = new Template("template/assistants/footer.html");
			$return .= $template->get_string();
			
			return $return;
		}
		else
		{
			return null;
		}
	}
	
}

?>
