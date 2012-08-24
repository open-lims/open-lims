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
 * 
 */
require_once("interfaces/auth.interface.php");

/**
 * Authentication Class
 * Manages Login, Logout and Forgot Password
 * @package base
 */
class Auth implements AuthInterface
{
	private $session_id;
	
	/**
	 * @see AuthInterface::login()
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
    public function login($username, $password)
    {
    	if ($username and $password)
    	{
    		$system_log = new SystemLog(null);

    		if (User::exist_username($username))
    		{
    			$user_id = User::get_user_id_by_username($username);
    			$user = new User($user_id);
    		
    			if ($user->check_password($password))
    			{
	    			if ($user->get_boolean_user_entry("user_inactive") == false)
	    			{
		    			$session = new Session(null);
		    			$session_id = $session->create($user_id);
		    			    			
		    			$this->session_id = $session_id;
		    			
		    			if ($user->get_boolean_user_entry("must_change_password") == true)
		    			{
		    				$session->write_value("must_change_password",true, true);
		    			}
		    			
		    			if ($user->get_boolean_user_entry("user_locked") == true)
		    			{
		    				$session->write_value("user_locked",true , false);
		    			}
		    			
		    			// Login Successful
		    			$system_log->create($user_id,1,1,"Login Successful","Login","auth.php",null,null);
		    			return true;
	    			}
	    			else
	    			{
	    				// Inactive Login
	    				$system_log->create($user_id,1,1,"Inactive User","Login","auth.php",null,null);
	    				return false;
	    			}
    			}
    			else
    			{
    				// Wring Password
    				$system_log->create($user_id,1,0,"Wrong Password","Login","auth.php",null,null);
    				return false;
    			}
    		}
    		else
    		{
    			// User Not Found
    			$system_log->create(null,1,0,"User \"".$username."\" Not Found","Login","auth.php",null,null);
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
   	/**
   	 * @see AuthInterface::logout()
   	 * @param integer $user_id
     * @param string $session_id
     * @return bool
   	 */
    public function logout($user_id, $session_id)
    {
    	$session = new Session($session_id);
    	
    	Session::check_all();
    	
    	if ($session->is_valid($user_id) == true)
    	{
    		if ($session->destroy() == true)
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
     * @see AuthInterface::forgot_password()
     * @param string $username
     * @param string $name
     * @return bool
     */
    public function forgot_password($username, $mail)
    {
    	if ($username and $mail)
    	{
    		$system_log = new SystemLog(null);
    		
    		if (User::exist_username($username))
    		{
    			$user_id = User::get_user_id_by_username($username);
    			$user = new User($user_id);
    		    		
    			if ($user->check_mail(strtolower($mail)))
    			{
	    			if ($user->get_boolean_user_entry("user_inactive") == false)
	    			{
	    				$new_password = User::generate_password();
	    			
	    				$mail = new Mail();
	    				$mail->set_recipient($user_id);
	    				$mail->set_subject("Your New Open-LIMS Password");
	    				$mail->set_text("Your new password: ".$new_password);
	    				$success = $mail->send();
	    			
		    			if ($success == true)
		    			{
		    				$user->set_password($new_password);
		    				$user->set_boolean_user_entry("must_change_password", true);
		    				
		    				// Password sended successfully
			    			$system_log->create($user_id,1,1,"Password Send","Forgot Password","auth.php",null,null);
			    			return true;
		    			}
		    			else
		    			{
		    				// Error via sending
		    				throw new AuthForgotPasswordSendFailedException("",0);
		    			}
	    			}
	    			else
	    			{
	    				// Inactive User
		    			$system_log->create($user_id,1,1,"Inactive User","Forgot Password","auth.php",null,null);
	    				throw new AuthUserNotFoundException("",0);
	    			}
    			}
    			else
    			{
    				// Wrong E-Mail
    				$system_log->create($user_id,1,0,"Wrong E-Mail","Forgot Password","auth.php",null,null);
    				throw new AuthUserNotFoundException("",0);
    			}
    		}
    		else
    		{
    			// User Not Found
    			$system_log->create(null,1,0,"User \"".$username."\" Not Found","Forgot Password","auth.php",null,null);
    			throw new AuthUserNotFoundException("",0);
    		}
    	}
    	else
    	{
    		throw new AuthUserNotFoundException("",0);
    	}
    }
    
    /**
     * @see AuthInterface::get_session_id()
     * @return string
     */
    public function get_session_id()
    {
    	if ($this->session_id != null)
    	{
    		return $this->session_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
}
?>