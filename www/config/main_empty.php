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
$server['main_folder']							= "/var/open-lims/www";
$server['timezone']								= "Europe/Berlin";
$server['timezone_id']							= 26;


$database['type'] 								= "postgres";
$database['database'] 							= "open-lims";
$database['user'] 								= "";
$database['password']							= "";

// Primary Database Server
$database['server']								= "localhost";
$database['port'] 								= "";

// Secondary Database Server
// $database[1]['server']						= "localhost";
// $database[1]['port'] 						= "";


$server['os']									= "linux";
$server['user']									= "John Doe & Co.";
$server['function']								= "test server";
$server['html_title']							= "Open-LIMS";


$mail['enable'] 								= true;
$mail['enable_smtp'] 							= false;
$mail['from'] 									= "roman.konertz@uni-koeln.de";

// Primary SMTP
$mail[0]['smtp']['server'] 						= "";
$mail[0]['smtp']['port'] 						= "";
$mail[0]['smtp']['username'] 					= "";
$mail[0]['smtp']['password'] 					= "";

// Secondary SMTP
// $mail[1]['smtp']['server'] 					= "";
// $mail[1]['smtp']['port'] 					= "";
///$mail[1]['smtp']['username'] 				= "";
// $mail[1]['smtp']['password'] 				= "";


$ldap['enable']									= false;


$server['binary']['enable']						= false;
$server['binary']['java']['home']				= "";
$server['binary']['java']['vm']					= "";
$server['binary']['java']['xms']				= "";
$server['binary']['java']['xmx']				= "";


$server['imagick']['enable']					= true;	


$server['update_check']['enable']				= false;
$server['update_check']['url']					= "http://update.open-lims.org/check.php";


$server['security']['session_timeout']			= 36000;
$server['security']['max_ip_failed_logins']		= 10;
$server['security']['max_ip_lead_time']			= 36000;


$server['behaviour']['debug_mode']				= false;
$server['behaviour']['avoid_css_cache']			= false;
$server['behaviour']['avoid_js_cache']			= false;
$server['behaviour']['on_db_rollback']			= true;
$server['behaviour']['on_db_expected_rollback']	= false;
$server['behaviour']['on_db_commit']			= false;
?>
