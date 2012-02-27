<?php
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
			while($data = $db->db_fetch_assoc($res))
			{
				self::$installed_module_array[$data['name']] = $data['db_version'];
			}
		}
		catch(DatabaseQueryFailedException $e)
		{
			try
			{
				$sql = "SELECT name FROM core_base_includes";
				$res = @$db->db_query($sql);
				while($data = $db->db_fetch_assoc($res))
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
		global $db, $transaction;

		$return = "-1";
		
		$transaction_id = $transaction->begin();
		
		if (file_exists("install/postgres/structure/".$module.".php"))
		{
			include("install/postgres/structure/".$module.".php");
			
			try
			{
				$sql = $check_statement;
				$res = @$db->db_query($sql);
				
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
}
?>