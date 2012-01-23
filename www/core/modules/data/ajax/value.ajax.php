<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
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
 * Value AJAX IO Class
 * @package data
 */
class ValueAjax
{
	public static function get_data_browser_link_html_and_button_handler($action) 
	{
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery[run]);
		switch($action):
			case "value_add":
				if(!isset($_POST['folder_id']) && !isset($_POST['value_array']))
				{ //second call (from additional script; loads template)
					require_once("core/modules/data/io/value_form.io.php");
					$value_form_io = new ValueFormIO(null, $_POST['type_id'], $_POST['folder_id']);
					return $value_form_io->get_content();
				}
				if(isset($_POST['value_array']))
				{//third call (from add button; creates value)
					return self::add_value($_POST['folder_id'], $_POST['type_id'], $_POST['value_array']);
				}

				require_once("core/include/data/value/access/value_type.access.php");
				$types = ValueType_Access::list_entries();
				$options = array();
				$counter = 0;
				foreach($types as $key => $value)
				{	
					if($value == 2)
					{
						continue;
					}
					$value_type = new ValueType($value);
					$options[$counter][value] = $value; 
					$options[$counter][content] = $value_type->get_name();		
					$options[$counter][selected] = "";
					$options[$counter][disabled] = "";
					$counter++;
				}
				
				$template = new HTMLTemplate("data/value_add_window.html");
				$template->set_var("option",$options);
				$html = $template->get_string();			
				$html_caption = "Add Value";
				
				$button_handler_template = new JSTemplate("data/js/value_add_window.js");
				$button_handler_template->set_var("session_id", $_GET['session_id']);
				$button_handler_template->set_var("folder_id", $_POST['folder_id']);
				$button_handler = $button_handler_template->get_string();
				$button_handler_caption = "Add";
				
				$additional_script_template = new JSTemplate("data/js/value_add_window_additional.js");
				$additional_script_template->set_var("session_id", $_GET['session_id']);
				$additional_script = $additional_script_template->get_string();
				$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption, "additional_script"=>$additional_script);
				return json_encode($array);
				
			break;
			case "value_delete":
				if(isset($_POST['sure']))
				{
					self::delete_value($_POST['value_id']);
				}
				else
				{
					$template = new HTMLTemplate("data/value_delete_window.html");
					$button_handler_template = new JSTemplate("data/js/value_delete_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("value_id", $_POST['value_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Delete";
					$html_caption = "Delete Value";
					$html = $template->get_string();
				}
			break;
			case "permission":
				require_once("data.ajax.php");
				if(isset($_POST[permissions]))
				{
					$success = DataAjax::change_permission(json_decode($_POST[permissions]), "Value");
					return $success;
				}
				else
				{
					$permission = DataAjax::permission_window();
					$button_handler_template = new JSTemplate("data/js/value_permission_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("value_id", $_POST['value_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;	
				}
			break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	private static function add_value($folder_id, $type_id, $value_array)
	{
		$values = json_decode($value_array, true);
		require_once("core/modules/data/io/value.io.php");
		$new_value = ValueIO::add_value_item_window($type_id, $folder_id, $values);
		return $new_value;
	}
	
	private static function delete_value($value_id) {
		$value = Value::get_instance($value_id);
		$value->delete();
	}

}

?>