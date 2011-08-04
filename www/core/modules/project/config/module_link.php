<?php 
/**
 * @package project
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
	$link[0][type]				= "home_button";
	$link[0]['array'][nav]		= "project";
	$link[0]['array'][run]		= "new";
	$link[0][file]				= "projects/home_buttons/create.html";
	$link[0][weight]			= 100;
	
	$link[1][type]				= "home_button";
	$link[1]['array'][nav]		= "project";
	$link[1][file]				= "projects/home_buttons/view_my.html";
	$link[1][weight]			= 200;
	
	$link[2][type]				= "ou_navigation";
	$link[2]['array'][nav]		= "project";
	$link[2]['array'][run]		= "organ_unit";
	$link[2]['array'][ou_id]	= "%OU_ID%";
	$link[2][weight]			= 0;
?>