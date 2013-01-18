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
 * User Regional Setting Access Class
 * @package base
 */
class UserRegionalSetting_Access
{
	private $user_id;
	
	private $language_id;
	private $timezone_id;
	private $time_display_format;
	private $time_enter_format;
	private $date_display_format;
	private $date_enter_format;
	private $country_id;
	private $system_of_units;
	private $system_of_paper_format;
	private $currency_id;
	private $currency_significant_digits;
	private $decimal_separator;
	private $thousand_separator;
	private $name_display_format;
	
	/**
	 * @param integer $user_id
	 */
	function __construct($user_id)
	{
		global $db;
		
		if ($user_id == null)
		{
			$this->user_id = null;
		}
		else
		{			
			$sql = "SELECT * FROM ".constant("USER_REGIONAL_SETTING_TABLE")." WHERE id='".$user_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{				
				$this->user_id 						= $user_id;
				
				$this->language_id					= $data['language_id'];
				$this->timezone_id					= $data['timezone_id'];
				$this->date_display_format			= $data['date_display_format'];
				$this->date_enter_format			= $data['date_enter_format'];
				$this->country_id					= $data['country_id'];
				$this->system_of_units				= $data['system_of_units'];
				$this->system_of_paper_format		= $data['system_of_paper_format'];
				$this->currency_id					= $data['currency_id'];
				$this->currency_significant_digits 	= $data['currency_significant_digits'];
				$this->decimal_separator 			= $data['decimal_separator'];
				$this->thousand_separator 			= $data['thousand_separator'];
				$this->name_display_format 			= $data['name_display_format'];
				
				if ($data['time_display_format'] == "t")
				{
					$this->time_display_format = true;
				}
				else
				{
					$this->time_display_format = false;
				}
				
				if ($data['time_enter_format'] == "t")
				{
					$this->time_enter_format = true;
				}
				else
				{
					$this->time_enter_format = false;
				}
			}
			else
			{
				$this->user_id			= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->user_id)
		{
			unset($this->user_id);

			unset($this->language_id);
			unset($this->timezone_id);		
		}
	}
	
	/**
	 * @param integer $user_id
	 * @param integer $language_id
	 * @param integer $timezone_id
	 * @param bool $time_display_format
	 * @param bool $time_enter_format
	 * @param string $date_display_format
	 * @param string $date_enter_format
	 * @param integer $country_id
	 * @param string $system_of_units
	 * @param string $system_of_paper_formats
	 * @param integer $currency_id
	 * @return bool
	 */
	public function create($user_id, 
			$language_id, 
			$timezone_id, 
			$time_display_format,
			$time_enter_format,
			$date_display_format,
			$date_enter_format,
			$country_id,
			$system_of_units,
			$system_of_paper_formats,
			$currency_id,
			$currency_significant_digits,
			$decimal_separator,
			$thousand_separator,
			$name_display_format)
	{
		global $db;
		
		if ($user_id and 
			is_numeric($language_id) and 
			is_numeric($timezone_id) and 
			isset($time_display_format) and 
			isset($time_enter_format) and 
			$date_display_format and 
			$date_enter_format and 
			$system_of_units and 
			$system_of_paper_formats and 
			$decimal_separator and 
			$name_display_format)
		{
			if ($time_display_format == true)
			{
				$time_display_format_insert = "t";
			}
			else
			{
				$time_display_format_insert = "f";
			}
			
			if ($time_enter_format == true)
			{
				$time_enter_format_insert = "t";
			}
			else
			{
				$time_enter_format_insert = "f";
			}
			
			if (is_numeric($currency_id))
			{
				$currency_id_insert = $currency_id;
			}
			else
			{
				$currency_id_insert = "NULL";
			}
			
			if (is_numeric($currency_significant_digits))
			{
				$currency_significant_digits_insert = $currency_significant_digits;
			}
			else
			{
				$currency_significant_digits_insert = "NULL";
			}
			
			$sql_write = "INSERT INTO ".constant("USER_REGIONAL_SETTING_TABLE")." (id," .
															"language_id," .
															"timezone_id," .
															"time_display_format," .
															"time_enter_format," .
															"date_display_format," .
															"date_enter_format," .
															"country_id," .
															"system_of_units," .
															"system_of_paper_format," .
															"currency_id, " .
															"currency_significant_digits," .
															"decimal_separator," .
															"thousand_separator," .
															"name_display_format)" .
											"VALUES (".$user_id."," .
															"".$language_id."," .
															"".$timezone_id."," .
															"'".$time_display_format_insert."'," .
															"'".$time_enter_format_insert."'," .
															"'".$date_display_format."'," .
															"'".$date_enter_format."'," .
															"".$country_id."," .
															"'".$system_of_units."'," .
															"'".$system_of_paper_formats."'," .
															"".$currency_id_insert.",";
															"".$currency_significant_digits_insert."," .
															"'".$decimal_separator."'," .
															"'".$thousand_separator."'," .
															"'".$name_display_format."')";
																	
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$this->__construct($user_id);
				return true;
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;

		if ($this->user_id)
		{
				
			$user_id_tmp = $this->user_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("USER_REGIONAL_SETTING_TABLE")." WHERE id = ".$user_id_tmp."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res) == 1)
			{
				return true;
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
	 * @return integer
	 */
	public function get_language_id()
	{
		if ($this->language_id)
		{
			return $this->language_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_timezone_id()
	{
		if ($this->timezone_id)
		{
			return $this->timezone_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_time_display_format()
	{
		if (isset($this->time_display_format))
		{
			return $this->time_display_format;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_time_enter_format()
	{
		if (isset($this->time_enter_format))
		{
			return $this->time_enter_format;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_date_display_format()
	{
		if ($this->date_display_format)
		{
			return $this->date_display_format;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_date_enter_format()
	{
		if ($this->date_enter_format)
		{
			return $this->date_enter_format;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_country_id()
	{
		if ($this->country_id)
		{
			return $this->country_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_system_of_units()
	{
		if ($this->system_of_units)
		{
			return $this->system_of_units;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_system_of_paper_format()
	{
		if ($this->system_of_paper_format)
		{
			return $this->system_of_paper_format;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_currency_id()
	{
		if ($this->currency_id)
		{
			return $this->currency_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_currency_significant_digits()
	{
		if (isset($this->currency_significant_digits))
		{
			return $this->currency_significant_digits;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_decimal_separator()
	{
		if ($this->decimal_separator)
		{
			return $this->decimal_separator;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_thousand_separator()
	{
		if ($this->thousand_separator)
		{
			return $this->thousand_separator;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name_display_format()
	{
		if ($this->name_display_format)
		{
			return $this->name_display_format;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $language_id
	 * @return bool
	 */
	public function set_language_id($language_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($language_id))
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET language_id = ".$language_id." WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->language_id = $language_id;
				return true;
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
	 * @param integer $timezone_id
	 * @return bool
	 */
	public function set_timezone_id($timezone_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($timezone_id))
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET timezone_id = ".$timezone_id." WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->timezone_id = $timezone_id;
				return true;
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
	 * @param bool $time_display_format
	 * @return bool
	 */
	public function set_time_display_format($time_display_format)
	{
		global $db;
			
		if ($this->user_id and isset($time_display_format))
		{
			if ($time_display_format == true)
			{
				$time_display_format_insert = "t";
			}
			else
			{
				$time_display_format_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET time_display_format = '".$time_display_format_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->time_display_format = $time_display_format;
				return true;
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
	 * @param bool $time_enter_format
	 * @return bool
	 */
	public function set_time_enter_format($time_enter_format)
	{
		global $db;
			
		if ($this->user_id and isset($time_enter_format))
		{
			if ($time_enter_format == true)
			{
				$time_enter_format_insert = "t";
			}
			else
			{
				$time_enter_format_insert = "f";
			}
			
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET time_enter_format = '".$time_enter_format_insert."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->time_enter_format = $time_enter_format;
				return true;
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
	 * @param string $date_display_format
	 * @return bool
	 */
	public function set_date_display_format($date_display_format)
	{
		global $db;
			
		if ($this->user_id and $date_display_format)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET date_display_format = '".$date_display_format."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->date_display_format = $date_display_format;
				return true;
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
	 * @param string $date_enter_format
	 * @return bool
	 */
	public function set_date_enter_format($date_enter_format)
	{
		global $db;
			
		if ($this->user_id and $date_enter_format)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET date_enter_format = '".$date_enter_format."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->date_enter_format = $date_enter_format;
				return true;
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
	 * @param integer $country_id
	 * @return bool
	 */
	public function set_country_id($country_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($country_id))
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET country_id = '".$country_id."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->country_id = $country_id;
				return true;
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
	 * @param string $system_of_units
	 * @return bool
	 */
	public function set_system_of_units($system_of_units)
	{
		global $db;
			
		if ($this->user_id and $system_of_units)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET system_of_units = '".$system_of_units."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->system_of_units = $system_of_units;
				return true;
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
	 * @param string $system_of_paper_format
	 * @return bool
	 */
	public function set_system_of_paper_format($system_of_paper_format)
	{
		global $db;
			
		if ($this->user_id and $system_of_paper_format)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET system_of_paper_format = '".$system_of_paper_format."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->system_of_paper_format = $system_of_paper_format;
				return true;
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
	 * @param integer $currency_id
	 * @return bool
	 */
	public function set_currency_id($currency_id)
	{
		global $db;
			
		if ($this->user_id and is_numeric($currency_id))
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET currency_id = '".$currency_id."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->currency_id = $currency_id;
				return true;
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
	 * @param integer $currency_significant_digits
	 * @return bool
	 */
	public function set_currency_significant_digits($currency_significant_digits)
	{
		global $db;
			
		if ($this->user_id and is_numeric($currency_significant_digits))
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET currency_significant_digits = '".$currency_significant_digits."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->currency_significant_digits = $currency_significant_digits;
				return true;
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
	 * @param string $decimal_separator
	 * @return bool
	 */
	public function set_decimal_separator($decimal_separator)
	{
		global $db;
			
		if ($this->user_id and $decimal_separator)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET decimal_separator = '".$decimal_separator."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->decimal_separator = $decimal_separator;
				return true;
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
	 * @param string $thousand_separator
	 * @return bool
	 */
	public function set_thousand_separator($thousand_separator)
	{
		global $db;
			
		if ($this->user_id and $thousand_separator)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET thousand_separator = '".$thousand_separator."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->thousand_separator = $thousand_separator;
				return true;
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
	 * @param string $name_display_format
	 * @return bool
	 */
	public function set_name_display_format($name_display_format)
	{
		global $db;
			
		if ($this->user_id and $name_display_format)
		{
			$sql = "UPDATE ".constant("USER_REGIONAL_SETTING_TABLE")." SET name_display_format = '".$name_display_format."' WHERE id = ".$this->user_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name_display_format = $name_display_format;
				return true;
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
}
?>
