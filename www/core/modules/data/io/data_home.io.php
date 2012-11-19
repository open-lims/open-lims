<?php
/**
 * @package data
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
 * Data Home IO Class
 * @package data
 */
class DataHomeIO
{
	public static function quota()
	{
		global $user;
		
		$user_data = new DataUserData($user->get_user_id());
		$user_quota = $user_data->get_quota();
		 
		if ($user_quota == 0)
		{
			$quota = "unlimited";
		}
		else
		{
		 	$quota = Convert::convert_byte_1024($user_quota); 	
		}
		
		$template = new HTMLTemplate("data/home/summary/quota.html");
		$template->set_var("quota",$quota);
		return $template->get_string();
	}
	
	public static function used_diskspace()
	{
		global $user;
		
		$user_data = new DataUserData($user->get_user_id());
 
		$user_filesize = $user_data->get_filesize();
		$user_quota = $user_data->get_quota();
		
		if ($user_quota != 0)
		{
			$diskspace_per = $user_filesize / $user_quota*100;
			 
			if ($diskspace_per == 0)
			{
				$diskspace_per_display = "(0%)";
			}
			else
			{
			 	$diskspace_per = floor($diskspace_per);
			 	
			 	if ($diskspace_per == 0)
			 	{
			 		$diskspace_per_display = "(> 1%)";	
			 	}
			 	else
			 	{
			 		$diskspace_per_display = "(".$diskspace_per."%)";
			 	}
			}
			 
			if (round($user_filesize/$user_quota*100,0) >= (int)Registry::get_value("data_quota_warning"))
			{
				$quotaWarn = " <img src='images/icons/notice.png' alt='W' />";
			}
			else
			{
				$quotaWarn = "";
			}
		}
		else
		{
		 	$quotaWarn = "";
		 	$diskspace_per_display = "";
		}
		
		$act_filesize = Convert::convert_byte_1024($user_filesize);
 
		$template = new HTMLTemplate("data/home/summary/used_diskspace.html");
		$template->set_var("used_diskspace",$act_filesize." ".$diskspace_per_display."".$quotaWarn);
		return $template->get_string();
	}
}
?>