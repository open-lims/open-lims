<?php
/**
 * @package base
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
 * Communicator Interface
 * @package base
 */  
interface CommunicatorInterface
{
	/**
	 * @param string $type
	 */
	function __construct($type);
	function __destruct();
	
	/**
     * @param integer $user_id
     * @return bool
     */
	public function set_recipient($user_id);
	
	/**
     * @param string $subject
     * @return bool
     */
	public function set_subject($subject);
	
	/**
     * @param string $text
     * @return bool
     */
	public function set_text($text);
	
	/**
     * Sends information via type (only mail yet)
     * @return bool
     */
	public function send();
}
?>