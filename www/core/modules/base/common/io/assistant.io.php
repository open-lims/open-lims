<?php
/**
 * @package base
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
 * Assistant Class
 * @package base
 */
class AssistantIO
{	
	private $ajax_handler;
	private $form_field_name;
	private $init_page;
	private $screen_array;
	
	/**
	 * @param string $ajax_handler
	 * @param string $form_field_name
	 */
	function __construct($ajax_handler, $form_field_name, $init_page =1)
	{
		$this->screen_array = array();
		
		if ($ajax_handler and $form_field_name)
		{
			$this->ajax_handler = $ajax_handler;
			$this->form_field_name = $form_field_name;
			
			if (is_numeric($init_page) and $init_page >= 0)
			{
				$this->init_page = $init_page;
			}
		}
	}
	
	/**
	 * @param string $title
	 * @return bool
	 */
	public function add_screen($title)
	{
		if ($title)
		{
			if (!in_array($title, $this->screen_array))
			{
				array_push($this->screen_array, $title);
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
	
	/**
	 * @return string
	 */
	public function get_content()
	{
		$return = "";

		$template = new HTMLTemplate("base/assistant/header.html");
		$return .= $template->get_string();
		
		$element_width = floor(730/count($this->screen_array));
		
		foreach($this->screen_array as $key => $value)
		{
			$template = new HTMLTemplate("base/assistant/element.html");
			$template->set_var("id", ($key+1));
			$template->set_var("width", $element_width);
			$template->set_var("image", ($key+1)."_lgrey.png");
			$template->set_var("text", $value);
			$return .= $template->get_string();	
		}
		
		$template = new HTMLTemplate("base/assistant/footer.html");
		$return .= $template->get_string();
		
		$template = new HTMLTemplate("base/assistant/content.html");
		
		$template->set_var("ajax_handler", $this->ajax_handler);
		$template->set_var("ajax_page", $this->init_page);
		$template->set_var("max_page", count($this->screen_array));
		$template->set_var("form_field_name", $this->form_field_name);
		
		$return .= $template->get_string();
		
		return $return;
	}

}
?>