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
require_once("interfaces/mail.interface.php");

/**
 * Mail Class
 * Sends information via mail
 * @package base
 */
class Mail implements MailInterface
{
	private $user_id;
	private $subject;
	private $text;
    
    function __destruct()
    {
    	if ($this->type != null)
    	{
	    	unset($this->type);	
	    	unset($this->user_id);
	    	unset($this->subject);
	    	unset($this->text);
    	}
    }
    
    /**
     * @see CommunicatorInterface::set_recipient()
     * @param integer $user_id
     * @return bool
     */
    public function set_recipient($user_id)
    {
    	if (is_numeric($user_id))
    	{
    		$this->user_id = $user_id;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see CommunicatorInterface::set_subject()
     * @param string $subject
     * @return bool
     */
    public function set_subject($subject)
    {
    	$this->subject = $subject;
    }
    
    /**
     * @see CommunicatorInterface::set_text()
     * @param string $text
     * @return bool
     */
    public function set_text($text)
    {
    	$this->text = $text;
    }
    
    /**
     * @see CommunicatorInterface::send()
     * @return bool
     */
    public function send()
    {
    	if ($this->user_id and $this->subject and $this->text)
    	{
    		$user = new User($this->user_id);
    		$mail = $user->get_profile("mail");
    			
    		$header = "From: ".constant("SENDMAIL_FROM")."\r\n";
    			
    		if (@mail($mail, $this->subject, $this->text, $header) == true)
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

}
?>