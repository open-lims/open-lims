<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Quiring
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
 * 
 */
 
 /**
  * This file checks running uploads and returns detailed progress data
  * Needs PHP uploadprogress extension to work
  */

if(function_exists('uploadprogress_get_info'))
{
	if (isset($_GET["unique_id"])) 
	{
	    $status = uploadprogress_get_info($_GET["unique_id"]);
	    if ($status) 
	    {
	 		$total_uploads = $status['files_uploaded'];
	    	$percent = round($status['bytes_uploaded']/$status['bytes_total']*100); 
	    	$time_left = $status["est_sec"];
			$speed = number_format(($status["speed_average"] / 1024), 1, ",", ".");
					
	        echo $percent." ".$time_left." ".$speed." ".$total_uploads;
	    }
	    else 
	    {
	        echo -1;
	    }
	}
}
else
{
	echo "NOT_INSTALLED";
}
?>