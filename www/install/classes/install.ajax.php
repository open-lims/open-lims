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
class InstallAjax
{
	private static $installed_module_array = array();
	
	private static function get_version()
	{
		global $db;
		
		try
		{
			// Version >= 0.3.9.9-5

			$sql = "SELECT name,db_version FROM core_base_includes";
			$res = @$db->db_query($sql);
			while($data = $db->fetch($res))
			{
				if (!$data['db_version'])
				{
					self::$installed_module_array[$data['name']] = "0.3.9.9-4";
				}
				else
				{
					self::$installed_module_array[$data['name']] = $data['db_version'];
				}
			}
		}
		catch(DatabaseQueryFailedException $e)
		{
			try
			{
				$sql = "SELECT name FROM core_base_includes";
				$res = @$db->db_query($sql);
				while($data = $db->fetch($res))
				{
					self::$installed_module_array[$data['name']] = "0.3.9.9-4";
				}
			}
			catch(DatabaseQueryFailedException $e)
			{
				// Error
			}
		}
	}
	
	public static function get_modules()
	{
		global $db;

		self::get_version();
		
		$order = array("base", "organisation_unit", "item", "parser", "template", "data", "location", "manufacturer", "project", "sample", "equipment");
		$return_array = array();
		
		$counter = 0;
		
		foreach ($order as $key => $value)
		{
			if (file_exists("information/".$value.".php"))
			{
				include("information/".$value.".php");
				
				if (file_exists("install/postgres/structure/".$value.".php"))
				{
					include("install/postgres/structure/".$value.".php");
					
					try
					{
						$sql = $check_statement;
						$res = @$db->db_query($sql);
											
						if (self::$installed_module_array[$value] != $version)
						{
							$return_array[$counter][0] = $value;
							$return_array[$counter][1] = "update";
							$counter++;
						}
						else
						{
							$return_array[$counter][0] = $value;
							$return_array[$counter][1] = "ok";
							$counter++;
						}
					}
					catch(DatabaseQueryFailedException $e)
					{
						$return_array[$counter][0] = $value;
						$return_array[$counter][1] = "install";
						$counter++;
					}
				}
				unset($version);
			}
		}
		
		return json_encode($return_array);
	}
	
	public static function install($module)
	{
		global $db, $db_check, $transaction;

		$return = "-1";
		
		$transaction_id = $transaction->begin();
		
		if (file_exists("install/postgres/structure/".$module.".php"))
		{
			include("install/postgres/structure/".$module.".php");
			
			try
			{
				$sql = $check_statement;
				$res = @$db_check->db_query($sql);
				
				$return = "0";
			}
			catch(DatabaseQueryFailedException $e)
			{
				if (is_array($statement) and count($statement) >= 1)
				{
					foreach ($statement as $statement_key => $statement_value)
					{
						$db->db_query($statement_value);
					}
				}
				
				$return = "1";
			}
		}
		
		if (file_exists("install/postgres/data/".$module.".php") and $return != "0")
		{
			include("install/postgres/data/".$module.".php");
			
			if (is_array($statement) and count($statement) >= 1)
			{
				foreach ($statement as $statement_key => $statement_value)
				{
					$db->db_query($statement_value);
				}
			}
		}
		
		$transaction->commit($transaction_id);
		
		return $return;
	}
	
	public static function update($module)
	{
		global $db, $transaction;

		self::get_version();
		
		require_once("update/update.php");
		
		if (file_exists("information/".$module.".php"))
		{
			include("information/".$module.".php");
		}
		else
		{
			return "-1";
		}
		
		if (is_array($update) and count($update) >= 1)
		{
			foreach($update as $key => $value)
			{
				if (self::$installed_module_array[$module] == $value['from'])
				{
					$start = $key;
				}
				if ($version == $value['to'])
				{
					$end = $key;
				}
			}
		}
		
		if (is_numeric($start) and is_numeric($end) and $start <= $end)
		{
			$transaction_id = $transaction->begin();
			
			for($i=$start;$i<=$end;$i++)
			{
				if (file_exists("update/".$i."/postgres/structure/".$module.".php"))
				{
					include("update/".$i."/postgres/structure/".$module.".php");
					
					if (is_array($statement) and count($statement) >= 1)
					{
						foreach ($statement as $statement_key => $statement_value)
						{
							$db->db_query($statement_value);
						}
					}
				}
				
				if (file_exists("update/".$i."/postgres/data/".$module.".php"))
				{
					include("update/".$i."/postgres/data/".$module.".php");
			
					if (is_array($statement) and count($statement) >= 1)
					{
						foreach ($statement as $statement_key => $statement_value)
						{
							$db->db_query($statement_value);
						}
					}
				}
			}
			
			$transaction->commit($transaction_id);
			
			return "1";
		}
		else
		{
			return "0";
		}
	}
	
	public static function get_table_row($module)
	{
		global $db;
		
		self::get_version();
		
		$template = new HTMLTemplate("table_row.html", "install/template");
		
		if (file_exists("update/update.php"))
		{
			include("update/update.php");
		}
		
		if (file_exists("information/".$module.".php"))
		{
			include("information/".$module.".php");
		}
		else
		{
			$version = "not available";
		}

		if (file_exists("install/postgres/structure/".$module.".php"))
		{
			
			include("install/postgres/structure/".$module.".php");
			
			try
			{
				$sql = $check_statement;
				$res = @$db->db_query($sql);

				$iv = self::$installed_module_array[$module];
				
				// Überprüfen ob größer
				// Überprüfen ob Update-Routine vorhanden
				if (self::$installed_module_array[$module] == $version)
				{
					$status = "up to date";
					$status_image = "<img src='images/ok.png' alt='' />";
				}
				else
				{
					$version_found = false;
					foreach($update as $key => $value)
					{
						if ($value['from'] == self::$installed_module_array[$module])
						{
							$version_found = true;
							break;
						}
					}
					
					if ($version_found == true)
					{
						$status = "update required";
						$status_image = "";
					}
					else
					{
						$status = "update not available";
						$status_image = "";
					}
				}
			}
			catch(DatabaseQueryFailedException $e)
			{
				$iv = "none";
				$status = "not installed";
				$install = true;
				$status_image = "";
			}
			
			$av = $version;
		}
		else
		{
			$av = "none";
			$iv = "none";
			$status = "OK";
			$status_image = "";
		}

		$template->set_var("av", $av);
		$template->set_var("iv", $iv);
		$template->set_var("fv", constant("PRODUCT_VERSION"));
		$template->set_var("status", $status);
		$template->set_var("status_image", $status_image);
		$template->set_var("name", $module);
		
		return $template->get_string();
	}
}
?>