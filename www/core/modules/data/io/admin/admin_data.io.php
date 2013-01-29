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
 * Admin Data IO Class
 * @package data
 */
class AdminDataIO
{
	public static function home_dialog()
	{
		$template = new HTMLTemplate("data/admin/home_dialog.html");
		
		$template->set_var("base_dir", System::get_base_directory());
		$template->set_var("system_space", Convert::convert_byte_1024(System::get_system_space()));
		$template->set_var("user_used_space", Convert::convert_byte_1024(DataUserData::get_used_space()));
		
		$additional_quota_dialog_array = ModuleDialog::list_dialogs_by_type("additional_quota");
		
		if (is_array($additional_quota_dialog_array) and count($additional_quota_dialog_array) >= 1)
		{
			$additional_quota_array = array();
			$additional_quota_counter = 0;
			
			foreach ($additional_quota_dialog_array as $key => $value)
			{
				if (file_exists($value['class_path']))
				{
					require_once($value['class_path']);
					$additional_quota_array[$additional_quota_counter]['title'] = Language::get_message($value['language_address'], "dialog");
					$additional_quota_array[$additional_quota_counter]['value'] = $value['class']::$value['method']();
					$additional_quota_counter++;
				}
			}
			
			$template->set_var("additional_quota_array", $additional_quota_array);
		}
		
		$template->set_var("db_used_space", Convert::convert_byte_1024(System::get_used_database_space()));
		$template->set_var("free_space", Convert::convert_byte_1024(System::get_free_space()));
		
		return $template->get_string();
	}
}
?>