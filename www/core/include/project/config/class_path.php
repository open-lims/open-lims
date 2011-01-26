<?php 
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
	$classes['ProjectException']			= $path_prefix."core/include/project/exceptions/project_exception.class.php";
	$classes['ProjectSecurityException']	= $path_prefix."core/include/project/exceptions/project_security_exception.class.php";
		
	$classes['Project'] 					= $path_prefix."core/include/project/project.class.php";
	$classes['ProjectItem'] 				= $path_prefix."core/include/project/project_item.class.php";
	$classes['ProjectLog']					= $path_prefix."core/include/project/project_log.class.php";
	$classes['ProjectLogHasItem']			= $path_prefix."core/include/project/project_log_has_item.class.php";
	$classes['ProjectPermission'] 			= $path_prefix."core/include/project/project_permission.class.php";
	$classes['ProjectSecurity'] 			= $path_prefix."core/include/project/project_security.class.php";
	$classes['ProjectStatus']				= $path_prefix."core/include/project/project_status.class.php";
	$classes['ProjectStatusRelation']		= $path_prefix."core/include/project/project_status_relation.class.php";
	$classes['ProjectTask']					= $path_prefix."core/include/project/project_task.class.php";
	$classes['ProjectTaskPoint']			= $path_prefix."core/include/project/project_task_point.class.php";
	$classes['ProjectTemplate']				= $path_prefix."core/include/project/project_template.class.php";
	$classes['ProjectTemplateCat']			= $path_prefix."core/include/project/project_template_cat.class.php";
?>