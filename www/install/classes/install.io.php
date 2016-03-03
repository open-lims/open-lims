<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * @package install
 */
class InstallIO
{
	public static function install()
	{
		global $db;
		
		$module_array = array();
		$module_array[] = "base";
		$module_array[] = "data";
		$module_array[] = "equipment";
		$module_array[] = "item";
		$module_array[] = "location";
		$module_array[] = "manufacturer";
		$module_array[] = "organisation_unit";
		$module_array[] = "parser";
		$module_array[] = "project";
		$module_array[] = "sample";
		$module_array[] = "template";
		
		$template = new HTMLTemplate("table.html", "install/template");
		
		if (is_array($module_array) and count($module_array) >= 1)
		{
			try
			{
				// Version >= 0.3.9.9-5
				
				$installed_module_array = array();
				
				$sql = "SELECT name,db_version FROM core_base_includes";
				$res = @$db->db_query($sql);
				while($data = $db->db_fetch_assoc($res))
				{
					$installed_module_array[$data['name']] = $data['db_version'];
				}
			}
			catch(DatabaseQueryFailedException $e)
			{
				// Version < 0.3.9.9-5
			}
			
			$install = false;
			$update = false;
			
			foreach ($module_array as $key => $value)
			{
				if (file_exists("information/".$value.".php"))
				{
					include("information/".$value.".php");
				}
				else
				{
					$version = "not available";
				}

				if (file_exists("install/postgres/structure/".$value.".php"))
				{
					include("install/postgres/structure/".$value.".php");
					
					try
					{
						$sql = $check_statement;
						$res = @$db->db_query($sql);
						
						if ($installed_module_array[$value])
						{
							if ($installed_module_array[$value] != $version)
							{
								$update = true;
							}
						}
						else
						{
							$update = true;
						}
					}
					catch(DatabaseQueryFailedException $e)
					{
						$install = true;
					}
					
					$module_display_array[$counter]['av'] = $version;
				}
			}
			
			if ($install == true and $update == true)
			{
				$template->set_var("template_disabled", "disabled='disabled'");
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Update/Installation");
			}
			elseif ($install == true and $update == false)
			{
				$template->set_var("template_disabled", "disabled='disabled'");
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Installation");
			}
			elseif ($install == false and $update == true)
			{
				$template->set_var("template_disabled", "disabled='disabled'");
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Update");
			}
			else
			{
				$template->set_var("template_disabled", "disabled='disabled'");
				$template->set_var("button_disabled", "disabled='disabled'");
				$template->set_var("button", "Everything is up to date");
			}
			
			$template->set_var("version", constant("PRODUCT_VERSION"));
		}
		
		$template->output();
	}
	
	public static function update()
	{
		if ($_GET['session_id'])
		{
			$session = new Session($_GET['session_id'], true);
		}
		else
		{
			$session = new Session(null, true);
		}
		
		$session_valid_array = $session->is_valid();
		if ($session_valid_array[0] === true)
		{
			self::install();
		}
		else
		{
			self::login();
		}
	}
	
	public static function login()
	{
		$template = new HTMLTemplate("login.html", "install/template");
		$template->output();
	}
}
?>