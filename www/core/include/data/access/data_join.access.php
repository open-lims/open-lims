<?php
/**
 * @package data
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
 * Data Join Access Class
 * @package data
 */
class DataJoin_Access
{
	const FOLDER_TABLE = 'core_folders';
	const FILE_TABLE = 'core_files';
	const FILE_VERSION_TABLE = 'core_file_versions';
	const VALUE_TABLE = 'core_values';
	const VALUE_VERSION_TABLE = 'core_value_versions';
	const VALUE_TYPE_TABLE = 'core_value_types';
	const OBJECT_TABLE = 'core_objects';

	/**
	 * @param integer $toid
	 * @return array
	 */
	public static function get_images_in_folder($toid)
	{
		global $db;
			
		if (is_numeric($toid))
		{
			$return_array = array();
			
			$sql = "SELECT ".self::FILE_TABLE.".id FROM ".self::OBJECT_TABLE." " .
					"JOIN ".self::FILE_TABLE." ON ".self::OBJECT_TABLE.".file_id = ".self::FILE_TABLE.".id " .
					"JOIN ".self::FILE_VERSION_TABLE." ON ".self::FILE_TABLE.".id = ".self::FILE_VERSION_TABLE.".toid " .
						"WHERE ".self::OBJECT_TABLE.".file_id IS NOT NULL AND " .
								"".self::OBJECT_TABLE.".value_id IS NULL AND " .
								"".self::FILE_VERSION_TABLE.".current = 't' AND " .
								"".self::OBJECT_TABLE.".toid = ".$toid." AND " .
									"(LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'jpg' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'jpeg' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'png' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'bmp' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'tiff' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'tif' OR " .
									"LOWER(".self::FILE_VERSION_TABLE.".file_extension) = 'gif')";				
			
			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
			}
							
			if (count($return_array) >= 1)
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
