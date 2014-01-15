<?php 
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
	$classes['OrganisationUnitException']			= $path_prefix."core/include/organisation_unit/exceptions/organisation_unit.exception.class.php";
	$classes['OrganisationUnitNotFoundException']	= $path_prefix."core/include/organisation_unit/exceptions/organisation_unit_not_found.exception.class.php";
	$classes['OrganisationUnitIDMissingException']	= $path_prefix."core/include/organisation_unit/exceptions/organisation_unit_id_missing.exception.class.php";

	$classes['OrganisationUnit']			= $path_prefix."core/include/organisation_unit/organisation_unit.class.php";
	$classes['OrganisationUnit_Wrapper']	= $path_prefix."core/include/organisation_unit/organisation_unit.wrapper.class.php";
?>