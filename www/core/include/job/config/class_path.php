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
	$classes['BaseJobException']					= $path_prefix."core/include/job/exceptions/base_job.exception.class.php";
	$classes['BaseJobNotFoundException']			= $path_prefix."core/include/job/exceptions/base_job_not_found.exception.class.php";
	$classes['BaseJoMissingException']				= $path_prefix."core/include/job/exceptions/base_job_missing.exception.class.php";
	$classes['BaseJobInvalidArgumentException']		= $path_prefix."core/include/job/exceptions/base_job_invalid_argument.exception.class.php";

	$classes['Job']									= $path_prefix."core/include/job/job.class.php";
	$classes['Job_Wrapper']							= $path_prefix."core/include/job/job.wrapper.class.php";
?>