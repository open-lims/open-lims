<?php
/**
 * @package template
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * XML Cacke Element Access Class
 * @package template
 */
class XmlCacheElement_Access
{
	const XML_CACHE_ELEMENT_PK_SEQUENCE = 'core_xml_cache_elements_primary_key_seq';
	
	/**
	 * @param integer $toid
	 * @param mixed $field_0
	 * @param mixed $field_1
	 * @param mixed $field_2
	 * @param mixed $field_3
	 * @return integer
	 */
	public function create($toid, $field_0, $field_1, $field_2, $field_3)
	{
		global $db;
		
		if (is_numeric($toid))
		{
			$field_0 = serialize($field_0);
			$field_1 = serialize($field_1);
			$field_2 = serialize($field_2);
			$field_3 = serialize($field_3);
			
			$sql_write = "INSERT INTO ".constant("XML_CACHE_ELEMENT_TABLE")." (primary_key,toid,field_0,field_1,field_2,field_3) " .
					"VALUES (nextval('".self::XML_CACHE_ELEMENT_PK_SEQUENCE."'::regclass),".$toid.",'".$field_0."','".$field_1."','".$field_2."','".$field_3."')";
					
			$db->db_query($sql_write);	
			
			$sql_read = "SELECT primary_key FROM ".constant("XML_CACHE_ELEMENT_TABLE")." WHERE primary_key = currval('".self::XML_CACHE_ELEMENT_PK_SEQUENCE."'::regclass)";
			$res_read = $db->db_query($sql_read);
			$data_read = $db->fetch($res_read);
			
			return $data_read['id'];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $toid
	 * @return bool
	 */
	public static function delete_all_by_toid($toid)
	{
		global $db;
		
		if (is_numeric($toid))
		{	
			$sql = "DELETE FROM ".constant("XML_CACHE_ELEMENT_TABLE")." WHERE toid = ".$toid."";
			$res = $db->db_query($sql);
						
			if ($db->row_count($res))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $toid
	 * @return array
	 */
	public static function get_all_content_by_toid($toid)
	{
		global $db;
		
		if (is_numeric($toid))
		{	
			$sql = "SELECT * FROM ".constant("XML_CACHE_ELEMENT_TABLE")." WHERE toid = ".$toid." ORDER BY primary_key ASC";
			$res = $db->db_query($sql);
			
			$result_array = array();
			
			while ($data = $db->fetch($res))
			{
				$tmp_array = array();
				$tmp_array[0] = unserialize($data['field_0']);
				$tmp_array[1] = unserialize($data['field_1']);
				$tmp_array[2] = unserialize($data['field_2']);
				$tmp_array[3] = unserialize($data['field_3']);
				array_push($result_array, $tmp_array);
			}
			
			return $result_array;
		}
		else
		{
			return null;
		}
	}
	
}
?>
