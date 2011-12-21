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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Value AJAX IO Class
 * @package data
 */
class ValueAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}

	
	private function get_data_browser_link_html_and_button_handler($action) 
	{
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery[run]);
		switch($action):
			case "value_delete":
				$template = new HTMLTemplate("data/value_delete_window.html");
				$button_handler = "
					$.ajax({
						type : \"GET\",
						url : \"../../../../core/modules/data/ajax/value.ajax.php\",
						data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&value_id=".$_GET['value_id']."&run=delete_value\",
						success : function(data) {
							close_ui_window_and_reload();
						}
					});
				";
				$button_handler_caption = "Delete";
				$html_caption = "Delete Value";
				$html = $template->get_string();
			break;
			case "permission":
				require_once("data.ajax.php");
				if(isset($_GET[permissions]))
				{
					$success = DataAjax::change_permission(json_decode($_GET[permissions]), "Value");
					return $success;
				}
				else
				{
					$permission = DataAjax::permission_window();
					$button_handler = "
						var json = '{';
						$('#DataBrowserLoadedAjaxContent').find('input').each(function(){
							if($(this).attr('type') != 'hidden') 
							{
								if($(this).is(':checkbox:checked'))
								{
									json += '\"'+$(this).attr('name')+'\":\"'+$(this).attr('value')+'\",';
								}
								else
								{
									json += '\"'+$(this).attr('name')+'\":\"0\",';
								}
							}
						});
						json = json.substr(0,json.length-1); //cut last ,
						json += '}';
						$.ajax({
							type : \"GET\",
							url : \"../../../../core/modules/data/ajax/value.ajax.php\",
							data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&value_id=".$_GET['value_id']."&nav=data&run=get_data_browser_link_html_and_button_handler&action=permission&permissions=\"+json,
							success : function(data) {
								close_ui_window_and_reload();
							}
						});
					";
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;	
				}
			break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
	private function add_value($folder_id, $type_id, $value_array)
	{
		if($type_id == null) //first call
		{
			$paramquery = $_GET;
			$params = http_build_query($paramquery);
			$template = new HTMLTemplate("data/value_add_window.html");
			$template->set_var("params", $params);
			require_once("../../../include/data/value/access/value_type.access.php");
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
			$template->set_var("option",$options);
			$html = $template->get_string();			
			$html_caption = "Add Value";
			$button_handler = "
				//parse all values and their respective names to a json array
				var value_id = $('#DataBrowserAddValue option:selected').val();
				var json = '{'
				$('#AjaxLoadedContent').find('input').each(function(){
					var name = $(this).attr('name');
					var value = $(this).val();
					json += '\"'+name+'\":\"'+value+'\",'
				});	
				$('#AjaxLoadedContent').find('select').each(function(){
					var name = $(this).attr('name');
					var value = $(this).children('option:selected').val();
					json += '\"'+name+'\":\"'+value+'\",'
				});	
				$('#AjaxLoadedContent').find('textarea').each(function(){
					var name = $(this).attr('name');
					var value = $(this).val();
					json += '\"'+name+'\":\"'+value+'\",'
				});	
				json = json.substr(0,json.length-1);
				json += '}'	
				
				//hand the array to this same function (third call)
				$.ajax({
						type : \"GET\",
						url : \"../../../core/modules/data/ajax/value.ajax.php\",
						data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&folder_id=".$_GET['folder_id']."&run=add_value&value_id=\"+value_id+\"&value_array=\"+json,
						success : function(data){
							close_ui_window_and_reload();
						}
					});
			";
			$additional_script = "
				//load the template corresponding to the selected value id (second call)
				$('#AjaxLoadedContent').jScrollPane();
				load_additional_content();
				var scrollAPI = $('#AjaxLoadedContent').data('jsp');
				$('#DataBrowserAddValue').change(function(){
					load_additional_content();
				});
				function load_additional_content()
				{
					var value_id = $('#DataBrowserAddValue option:selected').val();
					$.ajax({
						type : \"GET\",
						url : \"../../../core/modules/data/ajax/value.ajax.php\",
						data : \"username=".$_GET['username']."&session_id=".$_GET['session_id']."&run=add_value&value_id=\"+value_id,
						success : function(data) {
							$('#AjaxLoadedContent').find('.jspVerticalBar').show();
							$('#AjaxLoadedContent').children('.jspContainer').children('.jspPane').html(data);
							scrollAPI.reinitialise();
							var content_height = scrollAPI.getContentHeight();
							if(content_height > 320)
							{	
								$('#AjaxLoadedContent').children('.jspContainer').css('height',320);
							}
							else
							{
								$('#AjaxLoadedContent').children('.jspContainer').css('height',content_height);
							} 
							scrollAPI.reinitialise();
							if($('#AjaxLoadedContent').find('.jspDrag').height() == content_height)
							{
								$('#AjaxLoadedContent').find('.jspVerticalBar').hide();
							} 
							if($('#AjaxLoadedContent').find('.autofield').length > 0)
							{
								auto_field = new autofield(undefined);
								
								function check_if_values_were_added()
								{									
									if($('#DataAutofieldTable').children().length > 0)
									{
										var content_height = scrollAPI.getContentHeight();
										if(content_height > 320)
										{	
											$('#AjaxLoadedContent').children('.jspContainer').css('height',320);
										}
										else
										{
											$('#AjaxLoadedContent').children('.jspContainer').css('height',content_height);
										}
										scrollAPI.reinitialise();
									}
									if($('#DataBrowserLoadedAjaxContent').length != 0)
									{
										setTimeout(check_if_values_were_added, 200);
									}
								}
								check_if_values_were_added();
							}
						}
					});
				}
			";
			$button_handler_caption = "Add";
			$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption, "additional_script"=>$additional_script);
			return json_encode($array);
		}
		else if($folder_id == null) //second call (from additional script; loads template)
		{
			require_once("../../../../core/modules/data/io/value_form.io.php");
			$value_form_io = new ValueFormIO(null, $type_id, $folder_id);
			return $value_form_io->get_content();
		}
		else if($value_array != null)//third call (from add button; creates value)
		{
			$values = json_decode($value_array, true);
			require_once("../../../../core/modules/data/io/value.io.php");
			$new_value = ValueIO::add_value_item_window($type_id, $folder_id, $values);
			return $new_value;
		}
	}
	
	private function delete_value($value_id) {
		$value = Value::get_instance($_GET[value_id]);
		$value->delete();
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "get_data_browser_link_html_and_button_handler":
					echo $this->get_data_browser_link_html_and_button_handler($_GET[action]);
				break;
				
				case "add_value":
					echo $this->add_value($_GET[folder_id],$_GET[value_id], $_GET[value_array]);
				break;
				
				case "delete_value":
					echo $this->delete_value($_GET[value_id]);
				break;
				
			endswitch;
		}
	}
}

$value_ajax = new ValueAjax;
$value_ajax->method_handler();

?>