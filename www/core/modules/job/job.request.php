<?php
/**
 * @package job
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * Job Request Class
 * @package job
 */
class JobRequest
{	
	public static function ajax_handler($alias)
	{
		switch($_GET['run']):
	
			case "list_jobs":
				require_once("ajax/job.ajax.php");
				echo JobAjax::list_jobs(
						$_POST['column_array'], 
						$_POST['argument_array'], 
						$_POST['get_array'], 
						$_POST['css_page_id'], 
						$_POST['css_row_sort_id'], 
						$_POST['entries_per_page'], 
						$_GET['page'], 
						$_GET['sortvalue'], 
						$_GET['sortmethod']
						);
			break;
			
			case "count_jobs":
				require_once("ajax/job.ajax.php");
				echo JobAjax::count_jobs($_POST['argument_array']);
			break;
			
			case "start_test":
				require_once("ajax/job.ajax.php");
				echo JobAjax::start_test();
			break;
			
			case "start_test_handler":
				require_once("ajax/job.ajax.php");
				echo JobAjax::start_test_handler($_POST['number_of_jobs']);
			break;
			
		endswitch;
	}
	
	public static function io_handler($alias)
	{
		
	}
}
?>
