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
 * Open-LIMS Base Exception
 * @package base
 */
class BaseException extends Exception
{
	private $write_log;
	private $write_hdd_session;
	private $write_hdd_sql;
	private $additional_information;
	
    function __construct($write_log = false, $write_hdd_session = false, $write_hdd_sql = false, $message = null, $additional_information = null)
    {
    	parent::__construct($message);	
    	$this->write_log = $write_log;
    	$this->write_hdd_session = $write_hdd_session;
    	$this->write_hdd_sql = $write_hdd_sql;
    	$this->additional_information = $additional_information;
    	$exception_handler = new ExceptionHandler($this);
    }
    
    public function get_write_log()
    {
    	return $this->write_log;
    }
    
    public function get_write_hdd_session()
    {
    	return $this->write_hdd_session;
    }
    
    public function get_write_hdd_sql()
    {
    	return $this->write_hdd_sql;
    }
    
    public function get_message()
    {
    	return $this->message;
    }
    
    public function get_additional_information()
    {
    	return $this->additional_information;
    }
}

?>