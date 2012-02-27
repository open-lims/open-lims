<?php
class InstallAjax
{
	public static function get_modules()
	{
		$order = array("base", "organisation_unit", "item", "parser", "template", "data", "location", "manufacturer", "project", "sample", "equipment");
		//$order = array("base");
		return json_encode($order);
	}
	
	public static function install($module)
	{
		global $db, $transaction;

		$return = "-1";
		
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
				$transaction_id = $transaction->begin();
				
				if (is_array($statement) and count($statement) >= 1)
				{
					foreach ($statement as $statement_key => $statement_value)
					{
						$db->db_query($statement_value);
					}
				}
				
				$transaction->commit($transaction_id);
				
				$return = "1";
			}
		}
		
		if (file_exists("install/postgres/data/".$module.".php") and $return != "0")
		{
			include("install/postgres/data/".$module.".php");
			
			$transaction_id = $transaction->begin();
			
			if (is_array($statement) and count($statement) >= 1)
			{
				foreach ($statement as $statement_key => $statement_value)
				{
					$db->db_query($statement_value);
				}
			}
			
			$transaction->commit($transaction_id);
		}
		
		return $return;
	}
	
	public static function update($module)
	{
		
	}
}
?>