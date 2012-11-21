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
require_once("interfaces/retrace.interface.php");

/**
 * Retrace Class
 * @package base
 */
class Retrace implements RetraceInterface
{	
	/**
	 * @see RetraceInterface::create_retrace_string()
	 * @return string
	 */
	public static function create_retrace_string()
	{
		$module_retrace_array = SystemHandler::get_module_retrace_values();
				
		$retrace_array = array();
		
		foreach ($_GET as $key => $value)
		{
			switch ($key):
				case "nav":
				case "run":
				case "dialog":
				case "action":
				case "id":
					$retrace_array[$key] = $_GET[$key];
				break;
				
				default:
				if (in_array($key, $module_retrace_array))
				{
					$retrace_array[$key] = $_GET[$key];
				}
				break;
			endswitch;
		}
		
		return base64_encode(serialize($retrace_array));
	}
	
	/**
	 * @see RetraceInterface::resolve_retrace_string()
	 * @param string $retrace_string
	 * @return array
	 */
	public static function resolve_retrace_string($retrace_string)
	{
		if ($retrace_string)
		{
			$retrace_array = array();
			$retrace_array['username'] = $_GET['username'];
			$retrace_array['session_id'] = $_GET['session_id'];
			$retrace_array += unserialize(base64_decode($retrace_string));
			return $retrace_array;
		}
		else
		{
			return null;
		}
	}
}

?>