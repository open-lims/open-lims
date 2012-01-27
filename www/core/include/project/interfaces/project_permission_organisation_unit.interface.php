<?php
/**
 * @package project
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
 * Project Permission Organisation Unit Interface
 * @package project
 */ 	  	 
interface ProjectPermissionOrganisationUnitInterface
{	
	/**	
	 * @param integer $organisation_unit_id
	 * @param integer $project_id
	 * @param integer $permission
	 * @param integer $owner_id
	 * @param integer $intention
	 * @return integer
	 */
	public function create($organisation_unit_id, $project_id, $permission, $owner_id, $intention);
	
	/**
	 * @return bool
	 */
	public function delete();
}
?>
