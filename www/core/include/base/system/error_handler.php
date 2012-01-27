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
 * ErrorHandler-Function
 * Handles internal PHP-Errors
 * @param string $code
 * @param string $message
 * @param string $file
 * @param integer $line
 */
function error_handler($code, $message, $file, $line)
{
	global $db;		
			
	if ($code != 8 and $code != 2048)
	{
		if (stripos($message, "Failed to connect to mailserver") === false and stripos($message, "pg_query()") === false)
		{
			if (class_exists("BasePHPErrorException"))
			{
				$e = new BasePHPErrorException("PHP Error occurs in: ".$file." on Line ".$line." with Message: ".$message."");
				
				if (class_exists("Error_IO"))
				{
					$error_io = new Error_IO($e);
					$error_io->display_error();
				}
			}
			die();
		}
	}
	
}
?>
