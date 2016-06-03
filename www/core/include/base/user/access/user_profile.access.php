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
 * User Profile Access Class
 * @package base
 */
class UserProfile_Access
{
	private $user_id;
	
	private $gender;
	private $title;
	private $forename;
	private $surname;
	private $mail;
	
	private $institution;
	private $department;
	private $street;
	private $zip;
	private $city;
	private $country;
	private $phone;
	private $icq;
	private $msn;
	private $yahoo;
	private $aim;
	private $skype;
	private $lync;
	private $jabber;
	
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
			$sql = "SELECT * FROM ".constant("USER_PROFILE_TABLE")." WHERE id=:user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $user_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->user_id 			= $user_id;
				
				$this->gender			= $data['gender'];
				$this->title			= $data['title'];
				$this->forename			= $data['forename'];
				$this->surname			= $data['surname'];
				$this->mail				= $data['mail'];
				
				$this->institution		= $data['institution'];
				$this->department		= $data['department'];
				$this->street			= $data['street'];
				$this->zip				= $data['zip'];
				$this->city				= $data['city'];
				$this->country			= $data['country'];
				$this->phone			= $data['phone'];
				$this->icq				= $data['icq'];
				$this->msn				= $data['msn'];
				$this->yahoo			= $data['yahoo'];
				$this->aim				= $data['aim'];
				$this->skype			= $data['skype'];
				$this->lync				= $data['lync'];
				$this->jabber			= $data['jabber'];
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
	
			unset($this->gender);
			unset($this->title);
			unset($this->forename);
			unset($this->surname);
			unset($this->mail);
			
			unset($this->institution);
			unset($this->department);
			unset($this->street);
			unset($this->zip);
			unset($this->city);
			unset($this->country);
			unset($this->phone);
			unset($this->icq);
			unset($this->msn);
			unset($this->yahoo);
			unset($this->aim);
			unset($this->skype);
			unset($this->lync);
			unset($this->jabber);
		}
	}
	
	/**
	 * @param integer $user_id
	 * @param char $gender
	 * @param string $title
	 * @param string $forename
	 * @param string $surname
	 * @param string $mail
	 * @return integer
	 */
	public function create($user_id, $gender, $title, $forename, $surname, $mail)
	{
		global $db;
			
		if (is_numeric($user_id) and $gender and $forename and $surname and $mail)
		{
					
			$sql_write = "INSERT INTO ".constant("USER_PROFILE_TABLE")." (id," .
																"gender," .
																"title," .
																"forename," .
																"surname," .
																"mail," .
																"institution," .
																"department," .
																"street," .
																"zip," .
																"city," .
																"country," .
																"phone," .
																"icq," .
																"msn," .
																"yahoo," .
																"aim," .
																"skype, " .
																"lync, " .
																"jabber) " .
													"VALUES (:user_id," .
																":gender," .
																":title," .
																":forename," .
																":surname," .
																":mail," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL," .
																"NULL)";
			
			
			$res_write = $db->prepare($sql_write);
			
			$db->bind_value($res_write, ":user_id", $user_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":gender", $gender, PDO::PARAM_STR);
			$db->bind_value($res_write, ":title", $title, PDO::PARAM_STR);
			$db->bind_value($res_write, ":forename", $forename, PDO::PARAM_STR);
			$db->bind_value($res_write, ":surname", $surname, PDO::PARAM_STR);
			$db->bind_value($res_write, ":mail", $mail, PDO::PARAM_STR);
			
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				self::__construct($user_id);
				return $user_id;
			}
			else
			{
				return null;
			}
			
		}
		else
		{
			return null;
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
			$id_tmp = $this->user_id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("USER_PROFILE_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id_tmp, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res) == 1)
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
	 * @return char
	 */
	public function get_gender()
	{
		if ($this->gender)
		{
			return $this->gender;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_title()
	{
		if ($this->title)
		{
			return $this->title;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_forename()
	{
		if ($this->forename)
		{
			return $this->forename;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_surname()
	{
		if ($this->surname)
		{
			return $this->surname;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_mail()
	{
		if ($this->mail)
		{
			return $this->mail;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_institution()
	{
		if ($this->institution)
		{
			return $this->institution;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_department()
	{
		if ($this->department)
		{
			return $this->department;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_street()
	{
		if ($this->street)
		{
			return $this->street;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_zip()
	{
		if ($this->zip)
		{
			return $this->zip;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_city()
	{
		if ($this->city)
		{
			return $this->city;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_country()
	{
		if ($this->country)
		{
			return $this->country;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_phone()
	{
		if ($this->phone)
		{
			return $this->phone;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return integer
	 */
	public function get_icq()
	{
		if ($this->icq)
		{
			return $this->icq;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_msn()
	{
		if ($this->msn)
		{
			return $this->msn;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_yahoo()
	{
		if ($this->yahoo)
		{
			return $this->yahoo;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_aim()
	{
		if ($this->aim)
		{
			return $this->aim;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_skype()
	{
		if ($this->skype)
		{
			return $this->skype;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_lync()
	{
		if ($this->lync)
		{
			return $this->lync;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_jabber()
	{
		if ($this->jabber)
		{
			return $this->jabber;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param char $gender
	 * @return bool
	 */
	public function set_gender($gender)
	{
		global $db;

		if ($this->user_id and $gender)
		{
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET gender = :gender WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":gender", $gender, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->gender = $gender;
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
	 * @param string $title
	 * @return bool
	 */
	public function set_title($title)
	{
		global $db;

		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET title = :title WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":title", $title, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->title = $title;
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
	 * @param string $forename
	 * @return bool
	 */
	public function set_forename($forename)
	{
		global $db;
		
		if ($this->user_id and $forename)
		{
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET forename = :forename WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":forename", $forename, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->forename = $forename;
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
	 * @param string $surname
	 * @return bool
	 */
	public function set_surname($surname)
	{
		global $db;

		if ($this->user_id and $surname)
		{
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET surname = :surname WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":surname", $surname, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->surname = $surname;
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
	 * @param string $mail
	 * @return bool
	 */
	public function set_mail($mail)
	{
		global $db;
		
		if ($this->user_id and $mail)
		{
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET mail = :mail WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":mail", $mail, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->mail = $mail;
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
	 * @param string $institution
	 * @return bool
	 */
	public function set_institution($institution)
	{
		global $db;
			
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET institution = :institution WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":institution", $institution, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->institution = $institution;
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
	 * @param string $department
	 * @return bool
	 */
	public function set_department($department)
	{
		global $db;

		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET department = :department WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":department", $department, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->department = $department;
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
	 * @param string $street
	 * @return bool
	 */
	public function set_street($street)
	{
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET street = :street WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":street", $street, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->street = $street;
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
	 * @param string $zip
	 * @return bool
	 */
	public function set_zip($zip)
	{
		global $db;
			
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET zip = :zip WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":zip", $zip, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->zip = $zip;
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
	 * @param string $city
	 * @return bool
	 */
	public function set_city($city)
	{
		global $db;
			
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET city = :city WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":city", $city, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res)){
				$this->city = $city;
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
	 * @param string $country
	 * @return bool
	 */
	public function set_country($country)
	{
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET country = :country WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":country", $country, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->country = $country;
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
	 * @param string $phone
	 * @return bool
	 */
	public function set_phone($phone)
	{
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET phone = :phone WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":phone", $phone, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->phone = $phone;
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
	 * @param integer $icq
	 * @return bool
	 */
	public function set_icq($icq)
	{	
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET icq = :icq WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			
			if (is_numeric($icq))
			{
				$db->bind_value($res, ":icq", $icq, PDO::PARAM_INT);
			}
			else
			{
				$db->bind_value($res, ":icq", null, PDO::PARAM_NULL);
			}
			
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->icq = $icq;
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
	 * @param string $msn
	 * @return bool
	 */
	public function set_msn($msn)
	{	
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET msn = :msn WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":msn", $msn, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->msn = $msn;
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
	 * @param string $yahoo
	 * @return bool
	 */
	public function set_yahoo($yahoo)
	{
		global $db;
			
		if ($this->user_id)
		{
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET yahoo = :yahoo WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":yahoo", $yahoo, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->yahoo = $yahoo;
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
	 * @param string $aim
	 * @return bool
	 */
	public function set_aim($aim)
	{
		global $db;
		
		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET aim = :aim WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":aim", $aim, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->aim = $aim;
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
	 * @param string $skype
	 * @return bool
	 */
	public function set_skype($skype)
	{	
		global $db;

		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET skype = :skype WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":skype", $skype, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->skype = $skype;
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
	 * @param string $lync
	 * @return bool
	 */
	public function set_lync($lync)
	{	
		global $db;

		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET lync = :lync WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":lync", $lync, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->lync = $lync;
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
	 * @param string $jabber
	 * @return bool
	 */
	public function set_jabber($jabber)
	{	
		global $db;

		if ($this->user_id)
		{			
			$sql = "UPDATE ".constant("USER_PROFILE_TABLE")." SET jabber = :jabber WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->user_id, PDO::PARAM_INT);
			$db->bind_value($res, ":jabber", $jabber, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->jabber = $jabber;
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
