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
 * PostgreSQL Class
 * @package base
 */
class Postgresql
{
	public $database;

 	public function sql_connect($server,$port,$dbuser,$pass,$database)
 	{
 		if (!extension_loaded("pgsql"))
 		{
			die('Extension "pgsql" is missing!');
		}
 		
 		$options = "";
 		if ($server)
 		{
 			$options = $options." host=".$server; 			
 		}
 		if ($port)
 		{
 			$options = $options." port=".$port; 			
 		}
 		if ($dbuser)
 		{
 			$options = $options." user=".$dbuser; 			
 		}
 		if ($pass)
 		{
 			$options = $options." password=".$pass; 			
 		}
 		if ($database)
 		{
 			$options = $options." dbname=".$database; 			
 		}
 		else
 		{
 			return 0;
 		}
 		
 		$connection = pg_connect($options, PGSQL_CONNECT_FORCE_NEW);
 		if ($connection == true)
 		{
 			$this->database = $database;
 		}
 		 		
 		return $connection;
 	}
 
 	public function sql_query($connection, $query)
 	{
 	 	$pg_result = pg_query($connection, $query);
 	 	if (!$pg_result)
 	 	{
 	 		if (constant("DEBUG") == true)
 	 		{
 	 			echo $query;
 	 			echo "<br />".pg_last_error();
 	 		}
 	 		throw new DatabaseQueryFailedException(pg_last_error(), 2);
 	 	}
 	 	else
 	 	{
 	 		return $pg_result;
 	 	}
 	}
 	
	public function sql_fetch_assoc($assoc)
	{
 		return pg_fetch_assoc($assoc);
 	}
 	
 	public function sql_fetch_assoc_wrow($assoc, $row)
 	{
 		return pg_fetch_assoc($assoc, $row);
 	}
 
 	public function sql_fetch_array($array)
 	{
 		return pg_fetch_array($array);
 	}
 	
 	public function sql_fetch_array_wrow($assoc, $row)
 	{
 		return pg_fetch_array($assoc, $row);
 	}
 
 	public function sql_close()
 	{
 		pg_close();
 	}
 	
 	public function sql_num_rows($result)
 	{
 		return pg_num_rows($result);
 	}
 	
 	public function sql_affected_rows($result)
 	{
 		return pg_affected_rows($result);
 	}
 	
 	public function sql_escape_string($string)
 	{
 		return pg_escape_string($string);
 	}
 	
 	public function sql_escape_bytea($string)
 	{
 		return pg_escape_bytea($string);
 	}
 	
 	public function sql_unescape_bytea($string)
 	{
 		return pg_unescape_bytea($string);
 	}
 	
 	public function sql_database_size()
 	{
 		$res = pg_query("SELECT pg_database_size('".$this->database."') AS size");
 		return pg_fetch_assoc($res);
 	}
 
 	public function sql_last_error()
 	{
 		return pg_last_error();
 	}
 }
  
?>
