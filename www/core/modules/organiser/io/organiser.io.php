<?php
/**
 * @package organiser
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
 * Organiser IO Class
 * Personal Organiser is planned for further versions
 * This class contains test-code only
 * @package organiser
 */
class OrganiserIO
{
	public static function personal_organiser()
	{
		
		// Function Test Array
		$test_array = array();
		
		$test_array[0][type] 		= 0;
		$test_array[0][name] 		= "Test 1";
		$test_array[0][start_date]	= "2010-07-01";
		$test_array[0][start_time]	= "12:00:00";
		$test_array[0][end_date]	= "2010-07-10";
		$test_array[0][end_time]	= "18:00:00";
		$test_array[0][color]		= "32cdaf";
		$test_array[0][id]			= 0;
		$test_array[0][serial_id]	= 0;
		
		$test_array[1][type] 		= 0;
		$test_array[1][name] 		= "Test 2";
		$test_array[1][start_date]	= "2010-07-05";
		$test_array[1][start_time]	= "06:00:00";
		$test_array[1][end_date]	= "2010-07-12";
		$test_array[1][end_time]	= "15:00:00";
		$test_array[1][color]		= "0000FF";
		$test_array[1][id]			= 0;
		$test_array[1][serial_id]	= 0;
		
		$test_array[2][type] 		= 0;
		$test_array[2][name] 		= "Test 3";
		$test_array[2][start_date]	= "2010-07-03";
		$test_array[2][start_time]	= "13:00:00";
		$test_array[2][end_date]	= "2010-07-03";
		$test_array[2][end_time]	= "17:00:00";
		$test_array[2][color]		= "FF0000";
		$test_array[2][id]			= 0;
		$test_array[2][serial_id]	= 0;
		
		
		$todo_array = array();
		
		$todo_array[0][name]		= "Test 1";
		$todo_array[0][enddate]		= "2010-07-20";
		$todo_array[0][endtime]		= "14:13:00";
		$todo_array[0][done]		= false;
		$todo_array[0][link]		= null;
		$todo_array[0][id]			= null;
		$todo_array[0][user_id]		= 2;
		
		$todo_array[1][name]		= "Test 2";
		$todo_array[1][enddate]		= "2010-07-07";
		$todo_array[1][endtime]		= "02:00:00";
		$todo_array[1][done]		= false;
		$todo_array[1][link]		= null;
		$todo_array[1][id]			= null;
		$todo_array[1][user_id]		= 2;
		
		$todo_array[2][name]		= "Test 3";
		$todo_array[2][enddate]		= "2010-07-21";
		$todo_array[2][endtime]		= "21:34:00";
		$todo_array[2][done]		= true;
		$todo_array[2][link]		= null;
		$todo_array[2][id]			= null;
		$todo_array[2][user_id]		= 2;
		
		$todo_array[3][name]		= "Test 4";
		$todo_array[3][enddate]		= "2010-07-07";
		$todo_array[3][endtime]		= "14:00:00";
		$todo_array[3][done]		= true;
		$todo_array[3][link]		= null;
		$todo_array[3][id]			= null;
		$todo_array[3][user_id]		= 2;
		
		$template = new HTMLTemplate("organiser/personal_organiser.html");
		
		require_once("organiser_library.io.php");
		
		$organiser_library_io = new OrganiserLibraryIO(63);
		
		$organiser_library_io->set_calendar_array($test_array);
		$organiser_library_io->set_todo_array($todo_array);
		
		$template->set_var("content", $organiser_library_io->get_content());
		
		$template->output();
	}

	public static function list_upcoming_appointments()
	{
		$template = new HTMLTemplate("organiser/list_upcoming_appointments.html");
		
		$template->set_var("exist_appointment", false);
		
		return $template->get_string();
	}
	
	public static function list_upcoming_tasks()
	{
		$template = new HTMLTemplate("organiser/list_upcoming_tasks.html");
		
		$template->set_var("exist_todo_task", false);
		
		return $template->get_string();
	}
}

?>
