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
			return false;
		}
		
		if ($user->set_profile("title",$title) === false)
		{
			return false;
		}
		
		if ($user->set_profile("forename",$forename) === false)
		{
			return false;
		}
		
		if ($user->set_profile("surname",$surname) === false)
		{
			return false;
		}
		
		
		if ( $user->set_profile("mail",$mail) === false)
		{
			return false;
		}
		
		if ($user->set_profile("institution",$institution) === false)
		{
			return false;
		}
		
		if ($user->set_profile("department",$department) === false)
		{
			return false;
		}
		
		if ($user->set_profile("street",$street) === false)
		{
			return false;
		}
		
		if ($user->set_profile("zip",$zip) === false)
		{
			return false;
		}
		
		if ($user->set_profile("city",$city) === false)
		{
			return false;
		}
		
		if ($user->set_profile("country",$country) === false)
		{
			return false;
		}
		
		if ($user->set_profile("phone",$phone) === false)
		{
			return false;
		}
		
		
		if ($user->set_profile("icq",$icq) === false)
		{
			return false;
		}
		
		if ($user->set_profile("msn",$msn) === false)
		{
			return false;
		}
		
		if ($user->set_profile("yahoo",$yahoo) === false)
		{
			return false;
		}
		
		if ($user->set_profile("aim",$aim) === false)
		{
			return false;
		}
		
		if ($user->set_profile("skype",$skype) === false)
		{
			return false;
		}
		
		if ($user->set_profile("lync",$lync) === false)
		{
			return false;
		}
		
		if ($user->set_profile("jabber",$jabber) === false)
		{
			return false;
		}
		
		return true;
	}
}
?>