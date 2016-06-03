<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * User Ajax Class
 * @package base
 */
class UserAjax
{
	
	public static function profile_personal_data_change($gender, 
		$forename, 
		$surname, 
		$title, 
		$mail, 
		$institution, 
		$department, 
		$street, 
		$zip, 
		$city, 
		$country, 
		$phone, 
		$icq, 
		$msn, 
		$yahoo, 
		$aim, 
		$skype, 
		$lync, 
		$jabber)
	{
		global $user;
		
		if ($user->set_profile("gender",$gender) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("title",$title) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("forename",$forename) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("surname",$surname) === false)
		{
			return "0";
		}
		
		
		if ( $user->set_profile("mail",$mail) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("institution",$institution) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("department",$department) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("street",$street) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("zip",$zip) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("city",$city) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("country",$country) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("phone",$phone) === false)
		{
			return "0";
		}
		
		
		if ($user->set_profile("icq",$icq) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("msn",$msn) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("yahoo",$yahoo) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("aim",$aim) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("skype",$skype) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("lync",$lync) === false)
		{
			return "0";
		}
		
		if ($user->set_profile("jabber",$jabber) === false)
		{
			return "0";
		}
		
		return "1";
	}
	
	public static function profile_regional_settings_change($language_id, 
		$country_id, 
		$timezone_id, 
		$time_display, 
		$time_enter, 
		$date_display, 
		$date_enter, 
		$system_of_units, 
		$currency_id, 
		$currency_significant_digits, 
		$decimal_separator, 
		$thousand_separator, 
		$name_display_format, 
		$system_of_paper_format)
	{
		global $regional;
		
		if ($regional->set_language_id($language_id) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_country_id($country_id) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_timezone_id($timezone_id) === false)
		{
			return "0";
		}
		
		
		if ($time_display == 0)
		{
			$time_display = false;
		}
		else
		{
			$time_display = true;
		}
		
		if ($regional->set_time_display_format($time_display) === false)
		{
			return "0";
		}
		
		
		if ($time_enter == 0)
		{
			$time_enter = false;
		}
		else
		{
			$time_enter = true;
		}
		
		if ($regional->set_time_enter_format($time_enter) === false)
		{
			return "0";
		}

		
		switch($date_display):
		
			case "1":
				$date_display = "d.m.Y";
			break;
			
			case "2":
				$date_display = "Y.m.d";
			break;
			
			case "3":
				$date_display = "d-m-Y";
			break;
			
			case "4":
				$date_display = "m-d-Y";
			break;
			
			case "5":
				$date_display = "Y-m-d";
			break;
			
			case "6":
				$date_display = "d/m/Y";
			break;
			
			case "7":
				$date_display = "m/d/Y";
			break;
			
			case "9":
				$date_display = "j. M Y";
			break;
			
			default: // 8
				$date_display = "jS M Y";
			break;
		
		endswitch;
		
		if ($regional->set_date_display_format($date_display) === false)
		{
			return "0";
		}
		
		
		switch($date_enter):
					
			case "2":
				$date_enter = "yyyy.mm.dd";
			break;
			
			case "3":
				$date_enter = "dd-mm-yyyy";
			break;
			
			case "4":
				$date_enter = "mm-dd-yyyy";
			break;
			
			case "5":
				$date_enter = "yyyy-mm-dd";
			break;
			
			case "6":
				$date_enter = "dd/mm/yyyy";
			break;
			
			case "7":
				$date_enter = "mm/dd/yyyy";
			break;
			
			default: // 1
				$date_enter = "dd.mm.yyyy";
			break;
		
		endswitch;
		
		if ($regional->set_date_enter_format($date_enter) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_system_of_units($system_of_units) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_currency_id($currency_id) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_currency_significant_digits($currency_significant_digits) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_decimal_separator($decimal_separator) === false)
		{
			return "0";
		}

		
		if ($regional->set_thousand_separator($thousand_separator) === false)
		{
			return "0";
		}
		
		
		switch($name_display_format):
		
			case "2":
				$name_display_format = "F S";
			break;
			
			case "3":
				$name_display_format = "T S";
			break;
			
			case "4":
				$name_display_format = "S, T F";
			break;
			
			case "5":
				$name_display_format = "S, F";
			break;
			
			case "6":
				$name_display_format = "T S, F";
			break;
			
			case "7":
				$name_display_format = "S T F";
			break;
			
			case "8":
				$name_display_format = "S F T";
			break;
			
			case "9":
				$name_display_format = "S F";
			break;
			
			default: // 1
				$name_display_format = "T F S";
			break;
		
		endswitch;
		
		if ($regional->set_name_display_format($name_display_format) === false)
		{
			return "0";
		}
		
		
		if ($regional->set_system_of_paper_format($system_of_paper_format) === false)
		{
			return "0";
		}
		
		
		return "1";
	}
	
	public static function password_change($current_password, 
						$new_password_1, 
						$new_password_2)
	{
		global $user;
		
		if ($current_password and $new_password_1 and $new_password_2)
		{
			if ($user->check_password($current_password) == false)
			{
				return "error1";
			}
			
			if ($new_password_1 != $new_password_2)
			{
				return "error2";
			}
			
			if ($user->set_password($new_password_1))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
}
?>