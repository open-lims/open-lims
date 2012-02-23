<?php
class InstallIO
{
	public static function install()
	{
		$module_array = array();
		$module_array[] = "base";
		$module_array[] = "data";
		$module_array[] = "equipment";
		$module_array[] = "item";
		$module_array[] = "location";
		$module_array[] = "manufacturer";
		$module_array[] = "organisation_unit";
		$module_array[] = "project";
		$module_array[] = "sample";
		$module_array[] = "template";
		
		$template = new HTMLTemplate("table.html", "install/template");
		
		if (is_array($module_array) and count($module_array) >= 1)
		{
			$module_display_array = array();
			$counter = 0;
			
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
				
				$module_display_array[$counter][name] = $value;
				$module_display_array[$counter][iv] = "not installed";
				$module_display_array[$counter][av] = $version;
				$counter++;
				
				unset($version);
			}
			
			$template->set_var("modules", $module_display_array);
		}
		
		$template->output();
	}
}
?>