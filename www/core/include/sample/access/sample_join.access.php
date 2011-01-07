<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * Sample Join Access Class
 * @package sample
 */
class SampleJoin_Access
{

	const SAMPLE_TABLE = 'core_samples';
	const SAMPLE_HAS_ORGANISATION_UNIT_TABLE = 'core_sample_has_organisation_units';
	
	/**
	 * @param integer $id
	 * @param string $name
	 * @param array $template_array
	 * @param array $organisation_unit_array
	 * @return array
	 * @todo replace with wrapper-method
	 */
	public static function search_samples($id, $name, $template_array, $organisation_unit_array)
	{
   		global $db;
   		
   		if (($name or $id or (is_array($template_array) and count($template_array) >= 1)) and 
   			(is_array($organisation_unit_array) and count($organisation_unit_array) >= 1))
   		{	
   			$base_sql = "SELECT id FROM ".self::SAMPLE_TABLE." WHERE";
   			
   			if ($id)
   			{
   				$id = str_replace("*","%",$id);
   				$id_string = "";
   				$id_length = strlen($id);
   				for($i=0;$i<=($id_length-1);$i++)
   				{
   					if (is_numeric($id{$i}))
   					{
   						$id_string .= $id{$i};
   					}
   				}
   				
   				$id_string = (int)$id_string;
   				if ($id_string)
   				{
   					$add_sql = " CAST(id AS TEXT) LIKE '".$id_string."'";
   				}
   			}
   			else
   			{
   				$add_sql .= "";
   			}
   			
   			if ($name)
   			{
   				$name = str_replace("*","%",$name);
   				if ($add_sql)
   				{
					$add_sql .= " OR LOWER(name) LIKE '".$name."'";
				}
				else
				{
					$add_sql = " LOWER(name) LIKE '".$name."'";
				}
   			}
   			else
   			{
   				$add_sql .= "";
   			}	
   			
   			if (is_array($template_array) and count($template_array) >= 1)
   			{
   				if ($add_sql)
   				{
					$add_sql .= " AND (";
				}
				else
				{
					$add_sql .= " (";
				}
				
				$template_sql = "";
   				
   				foreach($template_array as $key => $value)
   				{
   					if ($template_sql)
   					{
   						$template_sql .= " OR template_id = '".$value."'";
   					}
   					else
   					{
   						$template_sql .= "template_id = '".$value."'";
   					}
   				}
   				$add_sql .= $template_sql.")";
   			}
   			
			if ($add_sql)
			{
				$add_sql .= " AND (";
			}
			else
			{
				$add_sql .= " (";
			}
			
			$organisation_unit_sql = "";
			
			foreach($organisation_unit_array as $key => $value)
			{
				if ($organisation_unit_sql)
				{
					$organisation_unit_sql .= " OR id IN (SELECT sample_id FROM ".self::SAMPLE_HAS_ORGANISATION_UNIT_TABLE." WHERE organisation_unit_id = ".$value.")";
				}
				else
				{
					$organisation_unit_sql .= "id IN (SELECT sample_id FROM ".self::SAMPLE_HAS_ORGANISATION_UNIT_TABLE." WHERE organisation_unit_id = ".$value.")";
				}
			}
			
			$add_sql .= $organisation_unit_sql.")";
			
   			$sql = $base_sql."".$add_sql;
   			
   			$return_array = array();
   			
   			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
			}
			
			if (is_array($return_array))
			{
				return $return_array;
			}
			else
			{
				return null;
			}	
   		}
   		else
   		{
   			return null;
   		}
   	}
	
}

?>
