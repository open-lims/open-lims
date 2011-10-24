<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
require_once("interfaces/transaction.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/transaction.access.php");
}

/**
 * DB Transaction Management Class
 * @package base
 */
class Transaction implements TransactionInterface
{
	private $unique_id;
	private $is_copy;

	/**
	 * @see TransactionInterface::__construct()
	 */
    function __construct()
    {
    	if (!$GLOBALS['transaction'] or $GLOBALS['transaction'] == false)
    	{
    		$this->unique_id = null;
    		$this->is_copy = false;
    		$GLOBALS['transaction'] = true;
    	}
    	else
    	{
    		$this->is_copy = true;
    	}
    }
    
    function __destruct()
    {
    	unset($GLOBALS['transaction']);
    	unset($this->unique_id);
    	unset($this->is_copy);
    }
    
    /**
     * @see TransactionInterface::begin()
     * @return string
     */
    public function begin()
    {
    	global $db;
    	
    	if ($this->unique_id == null and $this->is_copy == false)
    	{
    		$this->unique_id = uniqid();
    		$db->query_log_start();
    		Transaction_Access::begin();
    		return $this->unique_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see TransactionInterface::commit()
     * @param string $unique_id
     * @return bool
     */
    public function commit($unique_id, $write_log = false)
    {
    	global $db;
    	
    	if ($unique_id != null and $this->unique_id != null and $this->is_copy == false)
    	{
    		if ($unique_id == $this->unique_id)
    		{
    			Transaction_Access::commit();
    			$db->query_log_end();
    			$this->unique_id = null;
    			if (constant("ENABLE_DB_LOG_ON_COMMIT") == true and $write_log == true)
    			{
	    			if (is_writable(constant("LOG_DIR")))
	    			{
	    				$filename = date("Ymd-His")."-".uniqid()."-commit.txt";
	    				$handle = fopen(constant("LOG_DIR")."/".$filename, "w");
	    				fwrite($handle, $db->get_query_log());
	    				fclose($handle);
	    			}
    			}
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
     * @see TransactionInterface::rollback()
     * @param string $unique_id
     * @return bool
     */
    public function rollback($unique_id, $write_log = true)
    {
    	global $db;
    	
    	if ($unique_id != null and $this->unique_id != null and $this->is_copy == false)
    	{
    		if ($unique_id == $this->unique_id)
    		{
    			Transaction_Access::rollback();
    			$db->query_log_end();
    			$this->unique_id = null;
    			if (constant("ENABLE_DB_LOG_ON_ROLLBACK") == true and $write_log == true)
    			{
	    			if (is_writable(constant("LOG_DIR")))
	    			{
	    				$filename = date("Ymd-His")."-".uniqid()."-rollback.txt";
	    				$handle = fopen(constant("LOG_DIR")."/".$filename, "w");
	    				fwrite($handle, $db->get_query_log());
	    				fclose($handle);
	    			}
    			}
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
     * @see TransactionInterface::expected_rollback()
     * @param string $unique_id
     * @return bool
     */
    public function expected_rollback($unique_id, $write_log = false)
    {
    	global $db;
    	
    	if ($unique_id != null and $this->unique_id != null and $this->is_copy == false)
    	{
    		if ($unique_id == $this->unique_id)
    		{
    			Transaction_Access::rollback();
    			$db->query_log_end();
    			$this->unique_id = null;
    			if (constant("ENABLE_DB_LOG_ON_EXP_ROLLBACK") == true and $write_log == true)
    			{
	    			if (is_writable(constant("LOG_DIR")))
	    			{
	    				$filename = date("Ymd-His")."-".uniqid()."-expected_rollback.txt";
	    				$handle = fopen(constant("LOG_DIR")."/".$filename, "w");
	    				fwrite($handle, $db->get_query_log());
	    				fclose($handle);
	    			}
    			}
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
     * @see TransactionInterface::force_rollback()
     * @return bool
     */
    public function force_rollback($write_log = false)
    {
    	global $db;
    	
    	if ($this->unique_id != null)
    	{
    		Transaction_Access::rollback();
	    	$db->query_log_end();
	    	$this->unique_id = null;
	    	if (constant("ENABLE_DB_LOG_ON_EXP_ROLLBACK") == true and $write_log == true)
	    	{
		    	if (is_writable(constant("LOG_DIR")))
		    	{
		    		$filename = date("Ymd-His")."-".uniqid()."-expected_rollback.txt";
		    		$handle = fopen(constant("LOG_DIR")."/".$filename, "w");
		    		fwrite($handle, $db->get_query_log());
		    		fclose($handle);
		    	}
	    	}
    		return true;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    /**
     * @see TransactionInterface::is_in_transction()
     * @return bool
     */
    public function is_in_transction()
    {
    	if ($this->unique_id != null)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
}
?>