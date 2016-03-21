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
 * Base Environment Access Class
 * @package base
 */
class Environment_Wrapper_Access
{	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_languages($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "english_name":
					$sql_order_by = "ORDER BY english_name ".$sql_order_method;
				break;
				
				case "language_name":
					$sql_order_by = "ORDER BY language_name ".$sql_order_method;
				break;
				
				case "iso_639":
					$sql_order_by = "ORDER BY iso_639 ".$sql_order_method;
				break;
				
				case "iso_3166":
					$sql_order_by = "ORDER BY iso_3166 ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY english_name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY english_name ASC";
		}
		
		$sql = "SELECT ".constant("LANGUAGE_TABLE").".id, " .
					"".constant("LANGUAGE_TABLE").".english_name, " .
					"".constant("LANGUAGE_TABLE").".language_name, " .
					"".constant("LANGUAGE_TABLE").".iso_639, " .
					"".constant("LANGUAGE_TABLE").".iso_3166 " .
					"FROM ".constant("LANGUAGE_TABLE")." " .
					"".$sql_order_by."";
		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_languages()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("LANGUAGE_TABLE").".id) AS result " .
					 "FROM ".constant("LANGUAGE_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_timezones($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "name":
					$sql_order_by = "ORDER BY title ".$sql_order_method;
				break;
				
				case "deviation":
					$sql_order_by = "ORDER BY deviation ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY deviation ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY deviation ASC";
		}
		
		$sql = "SELECT ".constant("TIMEZONE_TABLE").".id, " .
					"".constant("TIMEZONE_TABLE").".title AS name, " .
					"".constant("TIMEZONE_TABLE").".deviation " .
					"FROM ".constant("TIMEZONE_TABLE")." " .
					"".$sql_order_by."";
		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_timezones()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("TIMEZONE_TABLE").".id) AS result " .
					 "FROM ".constant("TIMEZONE_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_paper_sizes($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY name ASC";
		}
		
		$sql = "SELECT ".constant("PAPER_SIZE_TABLE").".id, " .
					"".constant("PAPER_SIZE_TABLE").".name, " .
					"".constant("PAPER_SIZE_TABLE").".width, " .
					"".constant("PAPER_SIZE_TABLE").".height, " .
					"".constant("PAPER_SIZE_TABLE").".margin_left, " .
					"".constant("PAPER_SIZE_TABLE").".margin_right, " .
					"".constant("PAPER_SIZE_TABLE").".margin_top, " .
					"".constant("PAPER_SIZE_TABLE").".margin_bottom, " .
					"".constant("PAPER_SIZE_TABLE").".base, " .
					"".constant("PAPER_SIZE_TABLE").".standard " .
					"FROM ".constant("PAPER_SIZE_TABLE")." " .
					"".$sql_order_by."";
		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_paper_sizes()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("PAPER_SIZE_TABLE").".id) AS result " .
					 "FROM ".constant("PAPER_SIZE_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_units($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
				
				case "type":
					$sql_order_by = "ORDER BY type ".$sql_order_method;
				break;
				
				case "min_value":
					$sql_order_by = "ORDER BY min_value ".$sql_order_method;
				break;
				
				case "max_value":
					$sql_order_by = "ORDER BY max_value ".$sql_order_method;
				break;
				
				case "category":
					$sql_order_by = "ORDER BY category ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY type ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY type ASC";
		}
		
		$sql = "SELECT ".constant("MEASURING_UNIT_TABLE").".id, " .
					"".constant("MEASURING_UNIT_TABLE").".name, " .
					"".constant("MEASURING_UNIT_TABLE").".type, " .
					"".constant("MEASURING_UNIT_TABLE").".unit_symbol, " .
					"".constant("MEASURING_UNIT_TABLE").".min_value, " .
					"".constant("MEASURING_UNIT_TABLE").".max_value, " .
					"".constant("MEASURING_UNIT_TABLE").".min_prefix_exponent, " .
					"".constant("MEASURING_UNIT_TABLE").".max_prefix_exponent, " .
					"".constant("MEASURING_UNIT_CATEGORY_TABLE").".name AS category " .
					"FROM ".constant("MEASURING_UNIT_TABLE")." " .
					"LEFT JOIN ".constant("MEASURING_UNIT_CATEGORY_TABLE")." ON ".constant("MEASURING_UNIT_TABLE").".category_id = ".constant("MEASURING_UNIT_CATEGORY_TABLE").".id ".
					"".$sql_order_by."";

		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_measuring_units()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("MEASURING_UNIT_TABLE").".id) AS result " .
					 "FROM ".constant("MEASURING_UNIT_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_unit_ratios($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "numerator":
					$sql_order_by = "ORDER BY numerator ".$sql_order_method;
				break;
				
				case "numerator_exp":
					$sql_order_by = "ORDER BY numerator_exp ".$sql_order_method;
				break;
				
				case "denominator":
					$sql_order_by = "ORDER BY denominator ".$sql_order_method;
				break;
				
				case "denominator_exp":
					$sql_order_by = "ORDER BY denominator_exp ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY id ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY id ASC";
		}
		
		$sql = "SELECT a.name AS numerator, " .
					"b.name AS denominator, " .
					"".constant("MEASURING_UNIT_RATIO_TABLE").".numerator_unit_exponent AS numerator_exp, " .
					"".constant("MEASURING_UNIT_RATIO_TABLE").".denominator_unit_exponent AS denominator_exp, " .
					"".constant("MEASURING_UNIT_RATIO_TABLE").".id " .
					"FROM ".constant("MEASURING_UNIT_RATIO_TABLE")." " .
					"LEFT JOIN ".constant("MEASURING_UNIT_TABLE")." AS a ON ".constant("MEASURING_UNIT_RATIO_TABLE").".numerator_unit_id = a.id ".
					"LEFT JOIN ".constant("MEASURING_UNIT_TABLE")." AS b ON ".constant("MEASURING_UNIT_RATIO_TABLE").".denominator_unit_id = b.id ".
					"".$sql_order_by."";

		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_measuring_unit_ratios()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("MEASURING_UNIT_RATIO_TABLE").".id) AS result " .
					 "FROM ".constant("MEASURING_UNIT_RATIO_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}

	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_measuring_unit_categories($order_by, $order_method, $start, $end)
	{
		global $db;
		
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
										
				default:
					$sql_order_by = "ORDER BY name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY name ASC";
		}
		
		$sql = "SELECT ".constant("MEASURING_UNIT_CATEGORY_TABLE").".id, " .
					"".constant("MEASURING_UNIT_CATEGORY_TABLE").".name " .
					"FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")." " .
					"".$sql_order_by."";

		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}

	/**
	 * @return integer
	 */
	public static function count_measuring_unit_categories()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("MEASURING_UNIT_CATEGORY_TABLE").".id) AS result " .
					 "FROM ".constant("MEASURING_UNIT_CATEGORY_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
	/**
	 * @param string $order_by
	 * @param string $order_method
	 * @param integer $start
	 * @param integer $end
	 * @return array
	 */
	public static function list_currencies($order_by, $order_method, $start, $end)
	{
		global $db;
	
		if ($order_by and $order_method)
		{
			if ($order_method == "asc")
			{
				$sql_order_method = "ASC";
			}
			else
			{
				$sql_order_method = "DESC";
			}
			
			switch($order_by):
			
				case "name":
					$sql_order_by = "ORDER BY name ".$sql_order_method;
				break;
				
				case "iso_4217":
					$sql_order_by = "ORDER BY iso_4217 ".$sql_order_method;
				break;
						
				default:
					$sql_order_by = "ORDER BY name ASC";
				break;
			
			endswitch;
		}
		else
		{
			$sql_order_by = "ORDER BY name ASC";
		}
		
		$sql = "SELECT ".constant("CURRENCY_TABLE").".id, " .
					"".constant("CURRENCY_TABLE").".name, " .
					"".constant("CURRENCY_TABLE").".symbol AS currency_symbol, " .
					"".constant("CURRENCY_TABLE").".iso_4217 " .
					"FROM ".constant("CURRENCY_TABLE")." " .
					"".$sql_order_by."";
		
		$return_array = array();
		
		$res = $db->prepare($sql);
		$db->execute($res);
		
		if (is_numeric($start) and is_numeric($end))
		{
			for ($i = 0; $i<=$end-1; $i++)
			{
				if (($data = $db->fetch($res)) == null)
				{
					break;
				}
				
				if ($i >= $start)
				{
					array_push($return_array, $data);
				}
			}
		}
		else
		{
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data);
			}
		}
		return $return_array;
	}
	
	/**
	 * @return integer
	 */
	public static function count_currencies()
	{
		global $db;

		$sql = "SELECT COUNT(".constant("CURRENCY_TABLE").".id) AS result " .
					 "FROM ".constant("CURRENCY_TABLE")."";
			
		$res = $db->prepare($sql);
		$db->execute($res);
		$data = $db->fetch($res);

		return $data['result'];
	}
	
}

?>
