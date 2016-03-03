<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
function register_data($include_id)
{
	if (Item::delete_type_by_include_id($include_id))
	{
		if (Item::register_type("file", "DataEntity", $include_id) == false or 
			Item::register_type("value", "DataEntity", $include_id) == false or 
			Item::register_type("parameter", "DataEntity", $include_id) == false)
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
		if (Folder::register_type("system_folder", "SystemFolder", $include_id) == false)
		{
			return false;
		}
		
		if (Folder::register_type("user_folder", "UserFolder", $include_id) == false)
		{
			return false;
		}
		
		if (Folder::register_type("group_folder", "GroupFolder", $include_id) == false)
		{
			return false;
		}
		
		if (Folder::register_type("organisation_unit_folder", "OrganisationUnitFolder", $include_id) == false)
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
		if (ValueVar::register_type("item", "ItemValueVar", true, $include_id) == false)
		{
			return false;
		}
	}
	else
	{
		return false;
	}

	if (!Registry::is_value("data_user_default_quota"))
	{
		$registry = new Registry(null);
		$registry->create("data_user_default_quota", $include_id, "53687091200");
	}
	
	if (!Registry::is_value("data_quota_warning"))
	{
		$registry = new Registry(null);
		$registry->create("data_quota_warning", $include_id, "90");
	}
	
	if (!Registry::is_value("data_max_cached_images"))
	{
		$registry = new Registry(null);
		$registry->create("data_max_cached_images", $include_id, "100000");
	}
	
	if (!Registry::is_value("data_max_cache_period"))
	{
		$registry = new Registry(null);
		$registry->create("data_max_cache_period", $include_id, "30");
	}
	
	return true;
}
$result = register_data($key);
?>