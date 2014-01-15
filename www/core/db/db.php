<?php
/**
 * @package base
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
 * 
 */
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/database_query_failed_exception.class.php");
}

/**
 * DB Class
 * @package base
 */
class Database
{
	private $sql;
	private $connection;
	
	private $query_log;
	private $query_log_status;
		
	function __construct($sql_type)
	{
		$this->query_log = "";
		$this->query_log_status = false;
		
		switch ($sql_type):
		
		case ("postgres"):
			require_once("postgresql.php");
			$this->sql = new Postgresql;	
		break;
		default:
			die("Wrong Database Type");
		break;
		
		endswitch;
	}
	
	public function query_log_start()
	{
		$this->query_log_status = true;
		$this->query_log = "";
	}
	
	public function query_log_end()
	{
		$this->query_log_status = false;
	}
	
	public function get_query_log()
	{
		if (trim($this->query_log))
		{
			return $this->query_log;
		}
		else
		{
			return false;
		}
	}
	
	public function db_connect($server,$port,$db_user,$pass,$database)
	{
		$this->connection = $this->sql->sql_connect($server,$port,$db_user,$pass,$database);
		return $this->connection;
	}
	
	public function db_query($query)
	{
 	 	if ($this->query_log_status == true)
 	 	{
 	 		if (trim($this->query_log))
 	 		{
 	 			$this->query_log = $this->query_log."\n".$query.";";
 	 		}
 	 		else
 	 		{
 	 			$this->query_log = $query.";";
 	 		}
 	 	}
 	 	
 	 	$db_result = $this->sql->sql_query($this->connection, $query);
 	 	
 	 	if (!$db_result)
 	 	{
 	 		return 0;
 	 	}
 	 	else
 	 	{
 	 		return $db_result;
 	 	}
 	}
 	
	public function db_fetch_assoc($resultset)
	{
 		return $this->sql->sql_fetch_assoc($resultset);
 	}
 	
 	public function db_fetch_assoc_wrow($resultset, $row)
 	{
 		return $this->sql->sql_fetch_assoc_wrow($resultset, $row);
 	}
 
 	public function db_fetch_array($resultset)
 	{
 		return $this->sql->sql_fetch_array($resultset);
 	}
 	
 	public function db_fetch_array_wrow($resultset, $row)
 	{
 		return $this->sql->sql_fetch_array_wrow($resultset, $row);
 	}
 
 	public function db_close()
 	{
 		$this->sql->sql_close();
 	}
 	
 	public function db_num_rows($resultset)
 	{
 		return $this->sql->sql_num_rows($resultset);
 	}
 	
 	public function db_affected_rows($resultset)
 	{
 		return $this->sql->sql_affected_rows($resultset);
 	}
 	
 	public function db_escape_string($string)
 	{
 		return $this->sql->sql_escape_string($string);
 	}
 	
 	public function db_escape_bytea($string)
 	{
 		return $this->sql->sql_escape_bytea($string);
 	}
 	
 	public function db_unescape_bytea($string)
 	{
 		return $this->sql->sql_unescape_bytea($string);
 	}
 	
 	public function db_database_size()
 	{
 		return $this->sql->sql_database_size();
 	}
 	
	public function db_last_error()
 	{
 		return $this->sql->sql_last_error();
 	}
}

?>
