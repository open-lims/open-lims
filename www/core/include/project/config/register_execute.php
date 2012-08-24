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
 * Registering Functions
 */ 
function register_project($include_id)
{	
	if (Item::delete_holder_by_include_id($include_id))
	{
		if (Item::register_holder("project", "Project", $include_id) == false)
		{
			return false;
		}
	}
	else
	{
		return false;
	}
	
	if (Folder::delete_type_by_include_id($include_id))
	{
		if (Folder::register_type("project_folder", "ProjectFolder", $include_id) == false)
		{
			return false;
		}	
		
		if (Folder::register_type("project_status_folder", "ProjectStatusFolder", $include_id) == false)
		{
			return false;
		}	
	}
	else
	{
		return false;
	}
	
	if (ValueVar::delete_by_include_id($include_id))
	{
		if (ValueVar::register_type("project", "ProjectValueVar", false, $include_id) == false)
		{
			return false;
		}
	}
	else
	{
		return false;
	}
	
	if (!Registry::is_value("project_user_default_quota"))
	{
		$registry = new Registry(null);
		$registry->create("project_user_default_quota", $include_id, "1073741824");
	}
	
	if (!Registry::is_value("project_user_default_permission"))
	{
		$registry = new Registry(null);
		$registry->create("project_user_default_permission", $include_id, "15");
	}
	
	if (!Registry::is_value("project_leader_default_permission"))
	{
		$registry = new Registry(null);
		$registry->create("project_leader_default_permission", $include_id, "51");
	}
	
	if (!Registry::is_value("project_quality_manager_default_permission"))
	{
		$registry = new Registry(null);
		$registry->create("project_quality_manager_default_permission", $include_id, "1");
	}
	
	if (!Registry::is_value("project_group_default_permission"))
	{
		$registry = new Registry(null);
		$registry->create("project_group_default_permission", $include_id, "1");
	}
	
	if (!Registry::is_value("project_organisation_unit_default_permission"))
	{
		$registry = new Registry(null);
		$registry->create("project_organisation_unit_default_permission", $include_id, "1");
	}
	
	return true;
}
$result = register_project($key);
?>