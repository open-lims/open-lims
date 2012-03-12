<?php
/**
 * @package parser
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
require_once("interfaces/xml.interface.php");

/**
 * XML Parser
 * @package parser
 */
class Xml implements XmlInterface
{
	private $string;
	private $array;

	/**
	 * @see XmlInterface::__construct()
	 * @param string $string
	 */
	function __construct($string)
	{
		if ($string)
		{
			$this->string = $string;
		}
		else
		{
			$this->string = null;
		}
	}

	/**
	 * @see XmlInterface::parser()
	 */
	public function parser()
	{
		$number_of_tags = substr_count($this->string,"<");
		
		$xml_array = explode("<",$this->string);

		$return_array = array();
		$return_array_count = 0;
		
		$layer = 0;
		
		for ($i=1;$i<=$number_of_tags;$i++)
		{
			$xml_array[$i] = trim($xml_array[$i]);
				
			if (stripos(trim($xml_array[$i]),"/>") === false)
			{
				if ($xml_array[$i]{0} == "/")
				{
					$layer--;
					$return_array[$return_array_count][0] = $layer;
					
					$xml_tag_type_begin = 0;
					$xml_tag_type_end= stripos($xml_array[$i],">");
											
					$xml_tag_type = substr($xml_array[$i],$xml_tag_type_begin+1,($xml_tag_type_end-$xml_tag_type_begin)-1);
					
					$return_array[$return_array_count][1] = $xml_tag_type;
					$return_array[$return_array_count][2] = "#";
					$return_array[$return_array_count][3] = "#";
					
					$return_array_count++;
				}
				else
				{
					$return_array[$return_array_count][0] = $layer;
					
					$xml_tag_type_begin = 0;
					
					$xml_tag_type_end_brace = stripos($xml_array[$i],">");
					$xml_tag_type_end_space = stripos($xml_array[$i]," ");
					
					if ($xml_tag_type_end_space < $xml_tag_type_end_brace and $xml_tag_type_end_space != 0)
					{
						$xml_tag_type_end = $xml_tag_type_end_space;
					}
					else
					{
						$xml_tag_type_end = $xml_tag_type_end_brace;
					}
					
					$xml_tag_type = substr($xml_array[$i],$xml_tag_type_begin,($xml_tag_type_end-$xml_tag_type_begin));
					
					$return_array[$return_array_count][1] = $xml_tag_type;
					
					
					$xml_values_begin = $xml_tag_type_end_brace;
					$xml_values_end = strlen($xml_array[$i]);
					$xml_values = substr($xml_array[$i],$xml_values_begin+1,($xml_values_end-$xml_values_begin));
					
					$return_array[$return_array_count][2] = $xml_values;
					
					if ($xml_tag_type_end_space < $xml_tag_type_end_brace and $xml_tag_type_end_space != 0)
					{
						$xml_parameters_array = array();
						
						$xml_parameters_begin = $xml_tag_type_end_space;
						$xml_parameters_end = $xml_tag_type_end_brace;
						
						$xml_parameters_string = substr($xml_array[$i],$xml_parameters_begin+1,($xml_parameters_end-$xml_parameters_begin));
						
						$xml_parameters_string_length = strlen($xml_parameters_string);
						
						$xml_parameters_array = array();
						
						$value_found = false;
						$value_begin = 0;
						
						$in_value = false;
						$in_value_begin = 0;
						
						$key = "";
						$value = "";

						for ($j=0;$j<=$xml_parameters_string_length;$j++)
						{
							if ($xml_parameters_string{$j} != " " and $in_value == false)
							{
								if ($xml_parameters_string{$j} == "=")
								{
									$value_found = true;
									$value_begin = $j;
								}
								
								if ($value_found == false)
								{
									$key = $key."".$xml_parameters_string{$j};
								}
							}	
							
							if ($value_found == true and $value_begin != $j)
							{
								if ($xml_parameters_string{$j} == "\"")
								{
									if ($in_value == true)
									{
										$in_value = false;
									}
									else
									{
										$in_value = true;
										$in_value_begin = $j;
									}
								}
								
								if ($in_value == true and $in_value_begin != $j)
								{
									$value=$value."".$xml_parameters_string{$j};
								}
								elseif ($in_value == false and $in_value_begin != $j)
								{
									$xml_parameters_array[$key] = $value;
									$value_found = false;
									unset($key);
									unset($value);
								}
							}						
						}
						$return_array[$return_array_count][3] = $xml_parameters_array;
					}
					else
					{
						$return_array[$return_array_count][3] = false;
					}

					$layer++;
					$return_array_count++;
				}	
			}
			else
			{				
				$return_array[$return_array_count][0] = $layer;
				
				$xml_tag_type_begin = 0;
				
				$xml_tag_type_end_brace = stripos($xml_array[$i],">");
				$xml_tag_type_end_space = stripos($xml_array[$i]," ");
				
				if ($xml_tag_type_end_space < $xml_tag_type_end_brace and $xml_tag_type_end_space != 0)
				{
					$xml_tag_type_end = $xml_tag_type_end_space;
				}
				else
				{
					$xml_tag_type_end = $xml_tag_type_end_brace;
				}
				
				$xml_tag_type = substr($xml_array[$i],$xml_tag_type_begin,($xml_tag_type_end-$xml_tag_type_begin));
				
				$return_array[$return_array_count][1] = $xml_tag_type;

				$xml_values_begin = $xml_tag_type_end_brace;
				$xml_values_end = strlen($xml_array[$i]);
				$xml_values = substr($xml_array[$i],$xml_values_begin+1,($xml_values_end-$xml_values_begin));
				
				$return_array[$return_array_count][2] = $xml_values;
				
				if ($xml_tag_type_end_space < $xml_tag_type_end_brace and $xml_tag_type_end_space != 0)
				{
					$xml_parameters_array = array();
						
					$xml_parameters_begin = $xml_tag_type_end_space;
					$xml_parameters_end = $xml_tag_type_end_brace;
					
					$xml_parameters_string = substr($xml_array[$i],$xml_parameters_begin+1,($xml_parameters_end-$xml_parameters_begin));
					
					$xml_parameters_string_length = strlen($xml_parameters_string);
					
					$xml_parameters_array = array();
					
					$value_found = false;
					$value_begin = 0;
					
					$in_value = false;
					$in_value_begin = 0;
					
					$key = "";
					$value = "";

					for ($j=0;$j<=$xml_parameters_string_length;$j++)
					{
						if ($xml_parameters_string{$j} != " " and $in_value == false)
						{
							if ($xml_parameters_string{$j} == "=")
							{
								$value_found = true;
								$value_begin = $j;
							}
							
							if ($value_found == false)
							{
								$key = $key."".$xml_parameters_string{$j};
							}
						}	
						
						if ($value_found == true and $value_begin != $j)
						{
							if ($xml_parameters_string{$j} == "\"")
							{
								if ($in_value == true)
								{
									$in_value = false;
								}
								else
								{
									$in_value = true;
									$in_value_begin = $j;
								}
							}
							
							if ($in_value == true and $in_value_begin != $j)
							{
								$value=$value."".$xml_parameters_string{$j};
							}
							elseif ($in_value == false and $in_value_begin != $j)
							{
								$xml_parameters_array[$key] = $value;
								$value_found = false;
								unset($key);
								unset($value);
							}
						}						
					}
					$return_array[$return_array_count][3] = $xml_parameters_array;
				}
				else
				{
					$return_array[$return_array_count][3] = false;
				}
				$return_array_count++;
				
				$return_array[$return_array_count][0] = $layer;
				$return_array[$return_array_count][1] = $xml_tag_type;
				$return_array[$return_array_count][2] = "#";
				$return_array[$return_array_count][3] = "#";
				
				$return_array_count++;
			}
			unset ($xml_tag_type_end_brace);
			unset ($xml_tag_type_end_space);
		}
		$this->array = $return_array;
	}
	
	/**
	 * @see XmlInterface::get_string()
	 * @return string
	 */
	public function get_string()
	{
		if ($this->string)
		{
			return $this->string;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see XmlInterface::get_array()
	 * @return array
	 */
	public function get_array()
	{
		if ($this->array)
		{
			return $this->array;
		}
		else
		{
			return null;
		}
	}

}

?>
