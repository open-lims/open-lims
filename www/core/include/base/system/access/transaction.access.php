<?php
/**
 * @package base
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
 * Transaction Access Class
 * @package base
 */
class Transaction_Access
{
	/**
	 * @return bool
	 */
	public static function begin()
	{
		global $db;
		/*
    	$db->query("START TRANSACTION ISOLATION LEVEL SERIALIZABLE");
    	$db->query("BEGIN");
    	$db->query("SET CONSTRAINTS ALL DEFERRED");*/
		return $db->begin_transaction();
		
		//return true;
	}
	
	/**
	 * @return ressource
	 */
	public static function commit()
	{
		global $db;
		return $db->commit();
		//return $db->query("COMMIT");
	}
	
	/**
	 * @return ressource
	 */
	public static function rollback()
	{
		global $db;
		return $db->rollback();
		// return $db->query("ROLLBACK");
	}
	
}

?>
