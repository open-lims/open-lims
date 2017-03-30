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
 * DB Class
 * @package base
 */
class Database
{
	private $pdo;	
	
	private $last_statement;
	
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
	
	public function query($query)
	{
		if ($this->query_log_status === true)
		{
			$this->query_log = $this->query_log."\n".$query;
		}
		
		return $this->pdo->query($query);
	}
	
	public function prepare($query)
	{
		if ($this->query_log_status === true)
		{
			$this->query_log = $this->query_log."\n".$query;
		}
		
		return $this->pdo->prepare($query);
	}
	
	public function execute($statement)
	{
		$last_statement = $statement;
		$statement->execute();
	}
	
	public function bind_value($statement, $parameter, $variable, $data_type = null)
	{
		$statement->bindValue($parameter, $variable, $data_type);
	}
	
	public function row_count($statement)
	{
		$last_statement = $statement;
		return $statement->rowCount();
	}
	
	public function fetch($statement)
	{
		$last_statement = $statement;
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
	
	public function begin_transaction()
	{
		return $this->pdo->beginTransaction();
	}
	
	public function commit()
	{
		return $this->pdo->commit();
	}
	
	public function rollback()
	{
		return $this->pdo->rollBack();
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
	
	public function get_last_error()
	{
		return $last_statement->errorInfo();
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
}

?>
