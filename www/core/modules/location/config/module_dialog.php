<?php 
/**
 * @package location
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
	$dialog[0][type]			= "module_admin";
	$dialog[0][class_path]		= "core/modules/location/io/admin/admin_location.io.php";
	$dialog[0]['class']			= "AdminLocationIO";
	$dialog[0][method]			= "handler";
	$dialog[0][internal_name]	= "locations";
	$dialog[0][display_name]	= "Locations";
	$dialog[0][weight]			= 20000;
?>