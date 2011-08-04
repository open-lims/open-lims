<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Project Security Interface
 * @package project
 */ 
interface ProjectSecurityInterface
{
	function __construct($project_id);
	function __destruct();
	
	public function is_access($intention, $ignore_admin_status);
	public function list_involved_users();
	public function change_owner_permission($owner_id);
	public function change_ou_user_permission($organisation_unit_id);
	public function change_organisation_unit_permission($organisation_unit_id);
}
?>
