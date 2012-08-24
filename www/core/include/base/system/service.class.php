<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
require_once("interfaces/service.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_service.access.php");
	require_once("access/base_binary.access.php");
}

/**
 * Service Class
 * @package base
 */
class Service implements ServiceInterface
{	
	private $service_id;
	private $service;
	
	/**
	 * @see ServiceInterface::__construct()
	 * @param integer $id
	 */
	function __construct($id)
	{
		if (is_numeric($id))
		{
			$this->service_id = $id;
			$this->service = new BaseService_Access($id);
		}
		else
		{
			$this->service_id = null;
			$this->service = new BaseService_Access(null);
		}
	}
	
	/**
	 * @see ServiceInterface::__destruct()
	 */
	function __destruct()
	{
		if ($this->service_id)
		{
			unset($this->service_id);
			unset($this->service);
		}
	}
	
	/**
	 * @see ServiceInterface::is_responding()
	 * @return boolean
	 */
	public function is_responding()
	{
		if ($this->service and $this->service_id)
		{
			$last_lifesign = $this->service->get_last_lifesign();
			$last_lifesign_datetime_handler = new DatetimeHandler($last_lifesign);
			
			$current_datetime_handler = new DatetimeHandler();
			
			if ($last_lifesign_datetime_handler->distance($current_datetime_handler) >= 600)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ServiceInterface::start()
	 * @return boolean
	 */
	public function start()
	{
		if ($this->service and $this->service_id)
		{
			$java_vm = Registry::get_value("base_java_vm");

			if ($java_vm)
			{
				$binary_access = new BaseBinary_Access($this->service->get_binary_id());
				
				$file = constant("BIN_DIR")."/".$binary_access->get_path()."/".$binary_access->get_file();
				
				$cmd = "start /B ".$java_vm." -jar ".$file." ".$this->service_id;
				
				if (($handle = popen($cmd, "r")) !== false)
				{
					pclose($handle);
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
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ServiceInterface::stop()
	 * @return boolean
	 */
	public function stop()
	{
		if ($this->service and $this->service_id)
		{
			if ($this->is_responding() == false)
			{
				return $this->service->set_status(0);
			}
			else
			{
				return $this->service->set_status(2);
			}
		}
		else
		{
			return false;
		}
	}
}