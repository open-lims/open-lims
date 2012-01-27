<?php 
/**
 * @package equipment
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
	$link[0][type]				= "ou_detail_buttons";
	$link[0]['array'][nav]		= "%NAV%";
	$link[0]['array'][run]		= "common_dialog";
	$link[0]['array'][dialog]	= "list_ou_equipment";
	$link[0]['array'][ou_id]	= "%OU_ID%";
	$link[0][file]				= "equipment/links/lab_equipment.html";
	$link[0][weight]			= 200;
?>