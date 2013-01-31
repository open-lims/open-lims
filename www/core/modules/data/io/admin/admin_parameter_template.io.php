<?php
/**
 * @package data
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
 * Parameter Template Admin IO Class
 * @package data
 */
class AdminParameterTemplateIO
{
	public static function home()
	{
		$list = new List_IO("DataAdminParameterTemplate", "ajax.php?nav=data", "admin_list_parameter_templates", "admin_count_parameter_templates", "0", "DataAdminParameterTemplate");
		
		$list->add_column("", "symbol", false, "20px");
		$list->add_column(Language::get_message("DataGeneralListColumnName", "general"), "name");
		$list->add_column(Language::get_message("DataGeneralListColumnInternalName", "general"), "internal_name");
		$list->add_column(Language::get_message("DataGeneralListColumnCreatedBy", "general"), "created_by");
		$list->add_column(Language::get_message("DataGeneralListColumnDateTime", "general"), "datetime");
		
		$template = new HTMLTemplate("data/admin/parameter_template/list.html");	
	
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
	
		$template->set_var("list", $list->get_list());		
		
		$template->output();			
	}
	
	public static function add()
	{
		$template = new HTMLTemplate("data/admin/parameter_template/add.html");
		
		$result = array();
		$counter = 0;
		
		$measuring_unit_array = MeasuringUnit::get_categorized_list();
		
		if (is_array($measuring_unit_array) and count($measuring_unit_array) >= 1)
		{
			foreach($measuring_unit_array as $key => $value)
			{
				if ($value['headline'] == true)
				{
					$result[$counter]['disabled'] = "disabled='disabled'";
				}
				else
				{
					$result[$counter]['disabled'] = "";
				}
				
				$result[$counter]['value'] = "";
				
				$result[$counter]['selected'] = "";
				$result[$counter]['content'] = $value['name'];
				$counter++;
			}
		}
		
		$template->set_var("measuring_units",$result);
		
		$template->output();
	}
	
	public static function handler()
	{			
		switch($_GET['action']):
			
			case "add":
				self::add();
			break;
			
			case "delete":
				self::delete();
			break;
						
			default:
				self::home();
			break;
		
		endswitch;
	}
}
?>