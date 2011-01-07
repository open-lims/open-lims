<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * ErrorHandler-Function
 * Handles internal PHP-Errors
 * @param string $code
 * @param string $message
 * @param string $file
 * @param integer $line
 * @todo overhaul
 */
function error_handler($code, $message, $file, $line)
{
	global $db;		
			
	if ($code != 8 and $code != 2048)
	{
		if (stripos($message, "Failed to connect to mailserver") === false)
		{
			$in_container = Common_IO::get_in_container();
		
			if ($in_container == false)
			{	
				echo "<br />";
				echo Common_IO::container_begin("PHP Script Error",null);
			}
		
			echo "<br /><span class='bold'>PHP Script Error</span>";
		
			switch ($code):
				case 2:
					echo "<br />Type: Warning";
				break;
				
				default:
					echo "<br />Type: Error";
				break;						        
			endswitch;
			
			echo "<br />in: <span class='bold'>".$file."</span> on Line <span class='bold'>".$line."</span>";
			echo "<br />Message: ".$message;
		
			if ($in_container == false)
			{
				echo Common_IO::container_end(null);
			}
		
			if (class_exists('Database') and $db)
			{
				if (isset($session->user_id))
				{
					$user_id = $session->user_id;
				}
				else
				{
					$user_id = null;
				}
				$system_log = new SystemLog(null);
				$system_log->create($user_id, 3, $code, $message, null, $file, $line, serialize($_GET));	
			}
			die();
		}
	}
	
}
?>
