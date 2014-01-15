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
 * 
 */
require_once("interfaces/concrete_template.interface.php");

/**
 * Template Engine Class for HTML Files
 * @package base
 */
class HTMLTemplate extends Template implements ConcreteTemplateInterface
{
	/**
	 * @see TemplateInterface::__construct
	 * @param string $file
	 */
	function __construct($file, $path = null, $language_id = null)
	{
		parent::__construct($file, $path, $language_id);
		$this->replace_containers();
	}
	
	function __destruct()
	{
		parent::__destruct();
	}
		
	/**
	 * Replaces <!-- CONTAINER [...] --> in template
	 */
	private function replace_containers() 
	{		
		$number_of_commands = substr_count($this->string,"<!--");
		
		$start_position = 0;		

		for ($i=1;$i<=$number_of_commands;$i++)
		{
			$start_position 	= strpos($this->string, "<!--" , $start_position);
			$end_position 		= strpos($this->string, "-->", $start_position+1);
			
			$command 			= substr($this->string, $start_position+5, ($end_position-$start_position)-6);
			
			$command_array 		= explode(" ", $command, 3);
			
			$command_length = $end_position - $start_position;
			
			if (trim(strtolower($command_array[0])) == "container")
			{
				if (trim(strtolower($command_array[1])) == "begin")
				{					
					$container_begin = str_replace("(","",$command_array[2]);
					$container_begin = str_replace("\"","",$container_begin);
					$container_begin = str_replace(")","",$container_begin);
										
					$container_begin_array = explode(",",$container_begin);
					
					if ($container_begin_array[2])
					{
						$container_begin_string = Common_IO::container_begin($container_begin_array[0],$container_begin_array[1],$container_begin_array[2]);
					}
					elseif ($container_begin_array[1])
					{
						$container_begin_string = Common_IO::container_begin($container_begin_array[0],$container_begin_array[1]);
					}
					else
					{
						$container_begin_string = Common_IO::container_begin($container_begin_array[0]);
						
					}
					
					$this->string = substr_replace($this->string, $container_begin_string, $start_position, ($end_position-$start_position)+3);
					$container_begin_string_length = strlen($container_begin_string);
					
					$pointer_correction = $container_begin_string_length - $command_length;
					$end_position = $end_position + $pointer_correction;
				}
				elseif (trim(strtolower($command_array[1])) == "end")
				{
					$container_end = str_replace("(","",$command_array[2]);
					$container_end = str_replace("\"","",$container_end);
					$container_end = str_replace(")","",$container_end);
					
					$container_end_string = Common_IO::container_end();
					$container_end_string_length = strlen($container_end_string);
					
					$this->string = substr_replace($this->string, $container_end_string, $start_position, ($end_position-$start_position)+3);
				
					$pointer_correction = $container_end_string_length - $command_length;
					$end_position = $end_position + $pointer_correction;
				}
			}
			$start_position = $end_position+1;
		}
	}
}
?>