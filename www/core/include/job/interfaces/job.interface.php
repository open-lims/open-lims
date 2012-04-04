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
 * Job Interface
 * @package job
 */
class JobInterface
{
	/**
	 * @param $job_id
	 */
	function __construct($job_id);
	
	/**
	 * @param integer $type_id
	 * @return integer
	 */
	public function create($type_id);
	
	
	/**
	 * @param string $internal_name
	 * @return integer
	 */
	public static function get_type_id_by_internal_name($internal_name);
}