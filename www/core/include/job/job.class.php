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
 * 
 */
// require_once("interfaces/job.wrapper.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/job.access.php");
	require_once("access/job_type.access.php");
}

/**
 * Job Class
 * @package job
 */
class Job
{
	function __construct($job_id)
	{
		
	}
	
	public function create($type_id)
	{
		global $user;
		
		if (is_numeric($type_id))
		{
			$job_type_access = new JobType_Access($type_id);
			if ($job_type_binary_id = $job_type_access->get_binary_id())
			{
				$job_access = new Job_Access(null);
				if ($job_id = $job_access->create($type_id, $job_type_binary_id, $user->get_user_id()))
				{
					$this->__construct($job_id);
					return $job_id;
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
		else
		{
			return null;
		}
	}
}
?>