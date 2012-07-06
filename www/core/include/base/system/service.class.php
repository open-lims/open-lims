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
// require_once("interfaces/registry.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_service.access.php");
}

/**
 * Service Class
 * @package base
 */
class Service // implements RegistryInterface
{	
	private $service_id;
	private $service;
	
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
	
	function __destruct()
	{
		if ($this->service_id)
		{
			unset($this->service_id);
			unset($this->service);
		}
	}
	
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
	
	public function start()
	{
		if ($this->service and $this->service_id)
		{
			$java_vm = Registry::get_value("base_java_vm");

			if ($java_vm)
			{
				$cmd = "start /B ".$java_vm." -jar ".$command." ".$this->service_id;
				
				if (($handle = popen($cmd, "r")) !== false)
				{
					pclose();
					return $this->service->set_status(1);
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
	
	public function stop()
	{
		if ($this->service and $this->service_id)
		{
			return $this->service->set_status(2);
		}
		else
		{
			return false;
		}
	}
}