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
require_once("interfaces/template.interface.php");

/**
 * Template Engine Class (View)
 * @package base
 */
class Template implements TemplateInterface
{
	private $string;
	private $var_array;
	
	/**
	 * @see TemplateInterface::__construct
	 * @param string $file
	 */
	function __construct($file)
	{
		if (file_exists($file) == true)
		{
			$handler = fopen($file ,"r");
			
			if (filesize($file) > 0)
			{
				$this->string = fread($handler, filesize($file));
				$this->string = str_replace("\\[[","[%OB%]",$this->string);
				$this->string = str_replace("\\]]","[%CB%]",$this->string);
				$this->replace_containers();
			}
			else
			{
				die("Template Engine: File Is Empty!");
			}
		}
		else
		{
			die("Template Engine: File Not Found!");
		}
	}
	
	function __destruct()
	{
		unset($this->string);
		unset($this->var_array);
	}
	
	/**
	 * @see TemplateInterface::set_var()
	 * @param string $name Address
	 * @param string $value Content
	 */
	public function set_var($name, $value)
	{
		$this->var_array[trim(strtolower($name))] = $value;
	}
			
	/**
	 * @see TemplateInterface::output()
	 */
	public function output()
	{
		while (strpos($this->string, "<!-- FOREACH") !== false)
		{
			$this->call_foreach();
		}
		$this->call_control_structures();
		$this->fill_string();
	
		$this->string = str_replace("[%OB%]","[[",$this->string);
		$this->string = str_replace("[%CB%]","]]",$this->string);
		
		echo $this->string;
	}
	
	/**
	 * @see TemplateInterface::get_string()
	 * @return string
	 */
	public function get_string()
	{
		while (strpos($this->string, "<!-- FOREACH") !== false)
		{
			$this->call_foreach();
		}
		$this->call_control_structures();
		$this->fill_string();
	
		$this->string = str_replace("[%OB%]","[[",$this->string);
		$this->string = str_replace("[%CB%]","]]",$this->string);
		
		return $this->string;
	}
	
	/**
	 * Finds <!-- IF [...] --> statements in template
	 */	
	private function call_control_structures()
	{
		$block_begin = null;
		$pointer = 0;
		
		$inner_block_counter = 0;
		
		while(($start_pos = strpos($this->string, "<!--", $pointer)) !== false)
		{
			$pointer = $start_pos + 1;
			$end_pos = strpos($this->string, "-->", $start_pos+1);
			$pointer = $start_pos + 1;  

			$command_line = substr($this->string, $start_pos, ($end_pos - $start_pos)+3);
			
			$command_array = explode(" ", $command_line, 3);

			if (trim(strtolower($command_array[1])) == "if")
			{
				if ($block_begin === null)
				{
					$block_begin = $start_pos;
				}
				else
				{
					$inner_block_counter++;
				}				
			}
			
			if (trim(strtolower($command_array[1])) == "endif")
			{
				if ($inner_block_counter == 0)
				{					
					$current_inner_block_string = substr($this->string, $block_begin, ($start_pos-$block_begin)+14);
					$current_inner_block_string_length = strlen($current_inner_block_string);
					
					$new_inner_block_string = $this->replace_control_structure($current_inner_block_string);
					$new_inner_block_string_length = strlen($new_inner_block_string);

					$pointer_correction = $new_inner_block_string_length - $current_inner_block_string_length;
					
					$pointer = $pointer + $pointer_correction;

					$this->string = substr_replace($this->string, $new_inner_block_string, $block_begin, ($start_pos-$block_begin)+14);
				
					$block_begin = null;
				}
				else
				{
					$inner_block_counter--;
				}	
			}
		}
	}	
	
	/**
	 * Finds <!-- FOREACH [...] --> statements in template
	 */		
	private function call_foreach()
	{
		$pointer = 0;
		
		$block_begin = null;
		
		$inner_block_counter = 0;
		
		while(($start_pos = strpos($this->string, "<!--", $pointer)) !== false)
		{
			$end_pos = strpos($this->string, "-->", $start_pos+1);
			$pointer = $start_pos + 1;  
			
			$command_line = substr($this->string, $start_pos, ($end_pos - $start_pos)+3);
			
			$command_array = explode(" ", $command_line, 4);

			if (trim(strtolower($command_array[1])) == "foreach" and trim(strtolower($command_array[2])) == "begin")
			{
				if ($block_begin === null)
				{
					$block_begin = $start_pos;
				}
				else
				{
					$inner_block_counter++;
				}
			}
			
			if (trim(strtolower($command_array[1])) == "foreach" and trim(strtolower($command_array[2])) == "end")
			{
				if ($inner_block_counter == 0)
				{	
					$current_inner_block_string = substr($this->string, $block_begin, ($start_pos-$block_begin)+20);
					$current_inner_block_string_length = strlen($current_inner_block_string);

					$new_inner_block_string = $this->replace_foreach($current_inner_block_string);
					$new_inner_block_string_length = strlen($new_inner_block_string);

					$pointer_correction = $new_inner_block_string_length - $current_inner_block_string_length;
					
					$pointer = $pointer + $pointer_correction;

					$this->string = substr_replace($this->string, $new_inner_block_string, $block_begin, ($start_pos-$block_begin)+20);

					$block_begin = null;
				}
				else
				{
					$inner_block_counter--;
				}
			}
		}
	}
	
	/**
	 * Replaces Foreachs in string
	 * @param string $string
	 * @return string
	 */
	private function replace_foreach($string)
	{
		$pointer = 0;
		
		$block_begin = null;
		$block_end = null;
		
		$inner_block_counter = 0;
		
		while(($start_pos = strpos($string, "<!--", $pointer)) !== false)
		{
			$end_pos = strpos($string, "-->", $start_pos+1);
			$pointer = $start_pos + 1;  
			
			$command_line = substr($string, $start_pos, ($end_pos - $start_pos)+3);
			
			$command_array = explode(" ", $command_line, 4);

			if (trim(strtolower($command_array[1])) == "foreach" and trim(strtolower($command_array[2])) == "begin")
			{
				if ($block_begin === null)
				{
					$block_begin = $start_pos;
					
					$check_begin = strpos($command_array[3], "(");
					$check_end = strpos($command_array[3], ")");
					
					if (is_numeric($check_begin) and is_numeric($check_end))
					{
						$check_line = substr($command_array[3], $check_begin+1, ($check_end - $check_begin)-1);
						$check_line = str_replace("]]","",$check_line);
					}
				}
				else
				{
					$inner_block_counter++;
				}
			}
			
			if (trim(strtolower($command_array[1])) == "foreach" and trim(strtolower($command_array[2])) == "end")
			{
				if ($inner_block_counter == 0 and $check_line)
				{
					$block_end = $start_pos;
				}
				else
				{
					$inner_block_counter--;
				}
			}
		}
		
		$inner_block_string = substr($string, $block_begin, ($block_end-$block_begin));
		$inner_block_header_end = strpos($inner_block_string, "-->");
		$inner_block_string = substr($string, $block_begin+$inner_block_header_end+3, ($block_end-$block_begin)-$inner_block_header_end-3);

		$array_size = $this->get_var_cardinality($check_line);
				
		$return_string = "";
		
		if ($array_size >= 1)
		{
			for($i=0;$i<=$array_size-1;$i++)
			{
				$temp_string = $inner_block_string;
	
				$pointer = 0;
				
				while(($start_pos = stripos($temp_string, $check_line, $pointer)) !== false)
				{
					$pointer = $start_pos + 1;
					
					$current_length = strlen($check_line);
					
					if (strpos($check_line,",") !== false or strpos($check_line,":") !== false)
					{
						$temp_string = substr_replace($temp_string, ",".$i, $start_pos+$current_length, 0);
						$pointer = $pointer + strlen($i) + 1;
					}
					else
					{
						if ($temp_string{$start_pos+$current_length} == ":")
						{
							$temp_string = substr_replace($temp_string, ":".$i.",", $start_pos+$current_length, 1);
							$pointer = $pointer + strlen($i) + 2;
						}
						else
						{
							$temp_string = substr_replace($temp_string, ":".$i, $start_pos+$current_length, 0);
							$pointer = $pointer + strlen($i) + 1;
						}
					}
				}
				$return_string .= $temp_string;
			}
		}
		return $return_string;
	}
	
	/**
	 * Replaces IFs in string
	 * @param string $string
	 * @return string
	 */
	private function replace_control_structure($string)
	{
		$block_array = array();
		
		$block = null;
		$block_counter = 1;
		
		$inner_block_counter = 0;
		$inner_block_begin = 0;
		$inner_block_end = 0;
		
		$pointer = 0;
		
		while(($start_pos = strpos($string, "<!--", $pointer)) !== false)
		{
			$end_pos = strpos($string, "-->", $start_pos+1);
			$pointer = $start_pos + 1;  

			$command_line = substr($string, $start_pos, ($end_pos - $start_pos)+3);
			
			$command_array = explode(" ", $command_line, 3);

			if (trim(strtolower($command_array[1])) == "if")
			{
				if ($block_array[1] === null)
				{
					$block_array[1] = $start_pos;
			
					$check_begin = strpos($command_array[2], "(");
					$check_end = strpos($command_array[2], ")");
					
					if (is_numeric($check_begin) and is_numeric($check_end))
					{
						$check_line = substr($command_array[2], $check_begin+1, ($check_end - $check_begin)-1);
						
						$check_array = explode(" ",$check_line);
						
						if (count($check_array) == 3)
						{
							if (strpos($check_array[0],"[[") === false)
							{
								$left_value = $check_array[0];
							}
							else
							{
								$left_value = $this->get_var_value($check_array[0]);
							}
							
							if (strpos($check_array[2],"[[") === false)
							{
								$right_value = $check_array[2];
							}
							else
							{
								$right_value = $this->get_var_value($check_array[2]);
							}
							
							switch($check_array[1]):
							
								case("=="):
									if ($left_value == $right_value)
									{
										$block = 1;
									}
								break;
								
								case(">="):
									if ($left_value >= $right_value)
									{
										$block = 1;
									}
								break;
								
								case("<="):
									if ($left_value <= $right_value)
									{
										$block = 1;
									}
								break;
								
								case(">"):
									if ($left_value > $right_value)
									{
										$block = 1;
									}
								break;
								
								case("<"):
									if ($left_value < $right_value)
									{
										$block = 1;
									}
								break;
								
								case("!="):
									if ($left_value != $right_value)
									{
										$block = 1;
									}
								break;
							
							endswitch;							
						}
						else
						{
							if (strpos($check_array[0],"[[") === false)
							{
								if (trim(strtolower($check_array[0])) == "true")
								{
									$block = 1;
								}
							}
							else
							{
								$var_value = $this->get_var_value($check_array[0]);
								if ($var_value != false)
								{
									$block = 1;
								}
							}
						}
					}
					else
					{
						// Exception
					}
				}
				else
				{					
					$inner_block_counter++;
					if ($inner_block_counter == 1)
					{
						$inner_block_begin = $start_pos;
					}
				}
			}
			
			if (trim(strtolower($command_array[1])) == "elseif")
			{
				if ($block_array[1] !== null and $inner_block_counter == 0)
				{
					$block_counter++;
					$block_array[$block_counter] = $start_pos;
					
					if ($block == null)
					{
						$block_begin = $start_pos;
				
						$check_begin = strpos($command_array[2], "(");
						$check_end = strpos($command_array[2], ")");
						
						if (is_numeric($check_begin) and is_numeric($check_end))
						{
							$check_line = substr($command_array[2], $check_begin+1, ($check_end - $check_begin)-1);
							
							$check_array = explode(" ",$check_line);
							
							if (count($check_array) == 3)
							{
								if (strpos($check_array[0],"[[") === false)
								{
									$left_value = $check_array[0];
								}
								else
								{
									$left_value = $this->get_var_value($check_array[0]);
								}
								
								if (strpos($check_array[2],"[[") === false)
								{
									$right_value = $check_array[2];
								}
								else
								{
									$left_value = $this->get_var_value($check_array[2]);
								}
								
								switch($check_array[1]):
								
									case("=="):
										if ($left_value == $right_value)
										{
											$block = $block_counter;
										}
									break;
									
									case(">="):
										if ($left_value >= $right_value)
										{
											$block = $block_counter;
										}
									break;
									
									case("<="):
										if ($left_value <= $right_value)
										{
											$block = $block_counter;
										}
									break;
									
									case(">"):
										if ($left_value > $right_value)
										{
											$block = $block_counter;
										}
									break;
									
									case("<"):
										if ($left_value < $right_value)
										{
											$block = $block_counter;
										}
									break;
									
									case("!="):
										if ($left_value != $right_value)
										{
											$block = $block_counter;
										}
									break;
								
								endswitch;							
							}
							else
							{
								if (strpos($check_array[0],"[[") === false)
								{
									if (trim(strtolower($check_array[0])) == "true")
									{
										$block = $block_counter;
									}
								}
								else
								{
									$var_value = $this->get_var_value($check_array[0]);
									if ($var_value != false)
									{
										$block = $block_counter;
									}
								}
							}
						}else{
							// Exception
						}
					}
				}
			}
			
			if (trim(strtolower($command_array[1])) == "else")
			{
				if ($block_array[1] !== null and $inner_block_counter == 0)
				{					
					$block_counter++;
					$block_array[$block_counter] = $start_pos;
					if ($block == null)
					{
						$block = $block_counter;
					}
				}
			}
			
			if (trim(strtolower($command_array[1])) == "endif")
			{
				if ($block_array[1] !== null and $inner_block_counter == 0)
				{					
					$block_counter++;
					$block_array[$block_counter] = $start_pos;
				}
				else
				{
					if ($inner_block_counter == 1)
					{
						$inner_block_end = $start_pos;
						
						$current_inner_block_string = substr($string, $inner_block_begin, ($inner_block_end-$inner_block_begin)+14);
						$current_inner_block_string_length = strlen($current_inner_block_string);
						
						$new_inner_block_string = $this->replace_control_structure($current_inner_block_string);
						$new_inner_block_string_length = strlen($new_inner_block_string);
						
						$pointer_correction = $new_inner_block_string_length - $current_inner_block_string_length;
						
						$pointer = $pointer + $pointer_correction;

						$string = substr_replace($string, $new_inner_block_string, $inner_block_begin, ($inner_block_end-$inner_block_begin)+14);
					}
					$inner_block_counter--;
				}
			}
		}

		if (is_numeric($block))
		{
			$block_begin = $block_array[$block];
			$block_end = $block_array[$block+1];
			$header_end = strpos($string, "-->", $block_begin);
			
			$string = substr($string, $header_end+3, ($block_end - $header_end)-3);

			return $string;
		}
		else
		{
			return "";
		}
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
			
			if (trim(strtolower($command_array[0])) == "container")
			{
				if (trim(strtolower($command_array[1])) == "begin")
				{					
					$container_begin = str_replace("(","",$command_array[2]);
					$container_begin = str_replace("\"","",$container_begin);
					$container_begin = str_replace(")","",$container_begin);
										
					$container_begin_array = explode(",",$container_begin);
					
					$this->string = substr_replace($this->string, Common_IO::container_begin($container_begin_array[0],$container_begin_array[1]), $start_position, ($end_position-$start_position)+3);
					
				}
				elseif (trim(strtolower($command_array[1])) == "end")
				{
					
					$container_end = str_replace("(","",$command_array[2]);
					$container_end = str_replace("\"","",$container_end);
					$container_end = str_replace(")","",$container_end);
					
					$this->string = substr_replace($this->string, Common_IO::container_end($container_end), $start_position, ($end_position-$start_position)+3);
					
				}
			}
			$start_position 	= $end_position+1;
		}
	
	}
	
	/**
	 * Fills string with variables of set_var()
	 */
	private function fill_string()
	{
		$pointer = 0;
		
		while(($start_pos = strpos($this->string, "[[", $pointer)) !== false)
		{
			$end_pos = strpos($this->string, "]]", $start_pos+1);
			$pointer = $start_pos + 1;  
			
			$current_var = substr($this->string, $start_pos+2, ($end_pos - $start_pos)-2);
			$current_var_length = strlen($current_var);

			$new_var = $this->get_var_value($current_var);
			$new_var_length = strlen($new_var);
			
			$this->string = substr_replace($this->string, $new_var, $start_pos, ($end_pos - $start_pos)+2);
			
			$pointer_correction = $new_var_length - $current_var_length;
						
			$pointer = $pointer + $pointer_correction;
		}
	}
	
	/**
	 * Returns the cardinality of a variable
	 * @param string $var
	 * @return integer
	 */
	private function get_var_cardinality($var)
	{
		$var = str_replace("[[","",$var);
		$var = str_replace("]]","",$var);
		if (strpos($var,":") === false)
		{
			if ($this->var_array[trim(strtolower($var))])
			{
				if (is_array($this->var_array[trim(strtolower($var))]))
				{
					return count($this->var_array[trim(strtolower($var))]);
				}
				else
				{
					return 1;
				}
			}
			else
			{
				return 0;
			}
		}
		else
		{
			$var_array = explode(":",$var,2);
			if (strpos($var_array[1],",") === false)
			{
				$counter = 0;
				if ($this->var_array[trim(strtolower($var_array[0]))][trim(strtolower($var_array[1]))]) {
					foreach($this->var_array[trim(strtolower($var_array[0]))][trim(strtolower($var_array[1]))] as $key => $value) {
						if (is_numeric($key)) {
							$counter++;
						}
					}
				}
				return $counter;
				// return count($this->var_array[trim(strtolower($var_array[0]))][trim(strtolower($var_array[1]))]);
			}
			else
			{
				$sub_var_array = explode(",",$var_array[1]);
				$array = $this->var_array[trim(strtolower($var_array[0]))];
				for($i=0;$i<=count($sub_var_array)-1;$i++)
				{
					$array = $array[$sub_var_array[$i]];
				}
				
				$counter = 0;
				
				if ($array) {
					foreach($array as $key => $value) {
						if (is_numeric($key)) {
							$counter++;
						}
					}
				}
				return $counter;
			}
		}
	}
	
	/**
	 * Returns the value of a variable
	 * @param string $var
	 * @return string
	 */
	private function get_var_value($var)
	{
		$var = str_replace("[[","",$var);
		$var = str_replace("]]","",$var);
		if (strpos($var,":") === false)
		{
			if (isset($this->var_array[trim(strtolower($var))]))
			{
				if (is_array($this->var_array[trim(strtolower($var))]))
				{
					return "[ARRAY]";
				}
				else
				{
					return $this->var_array[trim(strtolower($var))];
				}
			}
			else
			{
				return "[".$var."]";
			}
		}
		else
		{
			$var_array = explode(":",$var,2);
			if (strpos($var_array[1],",") === false)
			{
				return $this->var_array[trim(strtolower($var_array[0]))][trim(strtolower($var_array[1]))];
			}
			else
			{
				$sub_var_array = explode(",",$var_array[1]);
				$array = $this->var_array[trim(strtolower($var_array[0]))];
				for($i=0;$i<=count($sub_var_array)-1;$i++)
				{
					$array = $array[trim(strtolower($sub_var_array[$i]))];
				}
				if (isset($array))
				{
					if (is_array($array))
					{
						return "[ARRAY]";
					}
					else
					{
						return $array;
					}
				}
				else
				{
					return "[".$var."]";
				}
			}
		}
	}
	
}

?>
