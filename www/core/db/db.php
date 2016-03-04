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
 * DB Class
 * @package base
 */
class Database
{
	private $pdo;	
	private $query_log;
	private $query_log_status;
				
	/**
	 * @todo move try catch to global
	 */
	public function connect($type, $server, $port, $username, $password, $database)
	{
		$options = "";
			
		$options = "host=".$server.";";
		$options = $options." dbname=".$database;
	
		if ($port)
		{
			$options = $options."; port:".$port."";
		}
	
		try
		{
			$this->pdo = new PDO($type.':'.$options, $username, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return true;
		}
		catch (PDOException $e)
		{
			return false;
		}
	}
	
	public function prepare($query)
	{
		return $this->pdo->prepare($query);
	}
	
	public function execute($statement)
	{
		$statement->execute();
	}
	
	public function row_count($statement)
	{
		return $statement->rowCount();
	}
	
	public function fetch($statement)
	{
		return $statement->fetch(PDO::FETCH_ASSOC);
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
	
	public function close()
	{
		unset($this->pdo);
	}
	
	
	
	
	// !! DECPRECATED
	
	public function db_query($query)
	{
 	 	$pg_result = $this->pdo->prepare($query);
 	 	$pg_result->execute();
 	 	
 	 	return $pg_result;
 	}
 	
	public function db_fetch_assoc($assoc)
	{
 		return $assoc->fetch(PDO::FETCH_ASSOC);
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
 
 
 	
 	public function db_num_rows($resultset)
 	{
 		return $this->sql->sql_num_rows($resultset);
 	}
 	
 	public function db_affected_rows($resultset)
 	{
 		return $resultset->rowCount();
 	}
 	 	 	
	public function db_last_error()
 	{
 		return $this->sql->sql_last_error();
 	}
}

?>
