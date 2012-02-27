<?php
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
			$module_display_array = array();
			$counter = 0;
			
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
							$module_display_array[$counter][iv] = $installed_module_array[$value];
							
							if ($installed_module_array[$value] == $version)
							{
								$module_display_array[$counter][status] = "up to date";
							}
							else
							{
								$module_display_array[$counter][status] = "update required";
								$update = true;
							}
						}
						else
						{
							$module_display_array[$counter][iv] = "<= 0.3.9.9-4";
							$module_display_array[$counter][status] = "update required";
							$update = true;
						}
					}
					catch(DatabaseQueryFailedException $e)
					{
						$module_display_array[$counter][iv] = "none";
						$module_display_array[$counter][status] = "not installed";
						$install = true;
					}
					
					$module_display_array[$counter][av] = $version;
				}
				else
				{
					$module_display_array[$counter][av] = "none";
					$module_display_array[$counter][iv] = "none";
					$module_display_array[$counter][status] = "OK";
				}

				$module_display_array[$counter][fv] = constant("PRODUCT_VERSION");
				$module_display_array[$counter][name] = $value;
				
				$counter++;
				
				unset($version);
			}
			
			if ($install == true and $update == true)
			{
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Update/Installation");
			}
			elseif ($install == true and $update == false)
			{
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Installation");
			}
			elseif ($install == false and $update == true)
			{
				$template->set_var("button_disabled", "");
				$template->set_var("button", "Start Update");
			}
			else
			{
				$template->set_var("button_disabled", "disabled='disabled'");
				$template->set_var("button", "Everything is up to date");
			}
			
			$template->set_var("version", constant("PRODUCT_VERSION"));
			$template->set_var("modules", $module_display_array);
		}
		
		$template->output();
	}
}
?>