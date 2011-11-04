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
require_once("../base/ajax.php");

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
				$paramquery[sure] = "true";
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery);
				$template = new Template("../../../template/data/value_delete_window.html");
				$button_handler = "close_ui_window_and_reload();";
				$button_handler_caption = "Delete";
				$html_caption = "Delete Value";
				$html = $template->get_string();
			break;
			case "permission":
				require_once("data.io.php");
				if(isset($_GET[permissions]))
				{
					$success = DataIO::change_permission(json_decode($_GET[permissions]), "Value");
					return $success;
				}
				else
				{
					$permission = DataIO::permission_window();
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
							url : \"../../../core/modules/data/value.ajax.php\",
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
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):	
				case "get_data_browser_link_html_and_button_handler":
					echo $this->get_data_browser_link_html_and_button_handler($_GET[action]);
				break;
			endswitch;
		}
	}
}

$value_ajax = new ValueAjax;
$value_ajax->method_handler();

?>