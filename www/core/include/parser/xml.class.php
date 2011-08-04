<?php
/**
 * @package parser
 * @version 0.4.0.0
 * @author Roman Konertz
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
	 * Cuts the XML String
	 * @deprecated Unsafe function, will removed in further versions
	 * @param string $part
	 * @return bool
	 */
	public function cut($part)
	{
		$part = strtolower(trim($part));
		
		$searchstring = strtolower($this->string);
				
		$start_needle = "<".$part.">";
		$end_needle = "</".$part.">";
		$status_start_pos = strpos($searchstring, $start_needle); // Sucht nach StartZeichen
		$status_end_pos = strpos($searchstring, $end_needle, $status_start_pos); // sucht nach EndZeichen
								
		if ($status_start_pos === false)
		{
			return false;
		}		

		$return_string = substr($this->string, $status_start_pos, ($status_end_pos-$status_start_pos));
		$return_string = str_replace("<".$part.">","",$return_string); // Entfernt zuletzt StartZeichen

		$this->string = $return_string;
	
		return true;
	}

	/**
	 * Parses the given XML-String
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
			}
			unset ($xml_tag_type_end_brace);
			unset ($xml_tag_type_end_space);
		}
		$this->array = $return_array;
	}
	
	/**
	 * Returns the XML-String
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
	 * Returns the XML-Array
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
