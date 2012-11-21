<?php
/**
 * @package organisation_unit
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
 * Organisation Unit AJAX IO Class
 * @package organisation_unit
 */
class AdminOrganisationUnitAjax 
{		
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_members($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				$type_id = $argument_array[0][1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = OrganisationUnit_Wrapper::list_organisation_unit_members($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$user = new User($value['id']);
						$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
						$list_array[$key]['username'] = $user->get_username();
						$list_array[$key]['fullname'] = $user->get_full_name(false);
						$list_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}

	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function count_members($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				return OrganisationUnit_Wrapper::count_organisation_unit_members($organisation_unit_id);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function delete_member($organisation_unit_id, $user_id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->delete_user_from_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function add_member($organisation_unit_id, $user_id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->create_user_in_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_owners($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				$type_id = $argument_array[0][1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = OrganisationUnit_Wrapper::list_organisation_unit_owners($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$user = new User($value['id']);
						$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
						$list_array[$key]['username'] = $user->get_username();
						$list_array[$key]['fullname'] = $user->get_full_name(false);
						$list_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function count_owners($json_argument_array)
	{
		global $user;
			
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				return OrganisationUnit_Wrapper::count_organisation_unit_owners($organisation_unit_id);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function delete_owner($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->delete_owner_from_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}	
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function add_owner($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->create_owner_in_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_leaders($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				$type_id = $argument_array[0][1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = OrganisationUnit_Wrapper::list_organisation_unit_leaders($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$user = new User($value['id']);
						$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
						$list_array[$key]['username'] = $user->get_username();
						$list_array[$key]['fullname'] = $user->get_full_name(false);
						$list_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param unknown_type $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function count_leaders($json_argument_array)
	{
		global $user;
			
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				return OrganisationUnit_Wrapper::count_organisation_unit_leaders($organisation_unit_id);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function delete_leader($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->delete_leader_from_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function add_leader($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->create_leader_in_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_quality_managers($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				$type_id = $argument_array[0][1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = OrganisationUnit_Wrapper::list_organisation_unit_quality_managers($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$user = new User($value['id']);
						$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' />";
						$list_array[$key]['username'] = $user->get_username();
						$list_array[$key]['fullname'] = $user->get_full_name(false);
						$list_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function count_quality_managers($json_argument_array)
	{
		global $user;
			
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				return OrganisationUnit_Wrapper::count_organisation_unit_quality_managers($organisation_unit_id);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function delete_quality_manager($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->delete_quality_manager_from_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $user_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function add_quality_manager($organisation_unit_id, $user_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (is_numeric($organisation_unit_id) and is_numeric($user_id))
			{
				$organisation_unit = new OrganisationUnit($organisation_unit_id);
				if ($organisation_unit->create_quality_manager_in_organisation_unit($user_id) == true)
				{
					return "1";
				}
				else
				{
					return "0";
				}
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}

	/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function list_groups($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				$type_id = $argument_array[0][1];
	
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = OrganisationUnit_Wrapper::list_organisation_unit_groups($organisation_unit_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$group = new Group($value['id']);
						$list_array[$key]['symbol'] = "<img src='images/icons/groups.png' alt='' />";
						$list_array[$key]['groupname'] = $group->get_name();
						$list_array[$key]['delete'] = "<a href='#' class='OrganisationUnitAdminListDelete' id='OrganisationUnitAdminListDelete".$list_array[$key]['id']."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 */
	public static function count_groups($json_argument_array)
	{
		global $user;
			
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			$organisation_unit_id = $argument_array[0][1];
			
			if (is_numeric($organisation_unit_id))
			{
				return OrganisationUnit_Wrapper::count_organisation_unit_groups($organisation_unit_id);
			}
			else
			{
				throw new OrganisationUnitIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $group_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 * @throws GroupIDMissingException
	 */
	public static function delete_group($organisation_unit_id, $group_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (!is_numeric($organisation_unit_id))
			{
				throw new OrganisationUnitIDMissingException();
			}
			
			if(!is_numeric($group_id))
			{
				throw new GroupIDMissingException();
			}

			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->delete_group_from_organisation_unit($group_id) == true)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @param integer $group_id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws OrganisationUnitIDMissingException
	 * @throws GroupIDMissingException
	 */
	public static function add_group($organisation_unit_id, $group_id)
	{
		global $user;
			
		if ($user->is_admin())
		{
			if (!is_numeric($organisation_unit_id))
			{
				throw new OrganisationUnitIDMissingException();
			}
			
			if(!is_numeric($group_id))
			{
				throw new GroupIDMissingException();
			}
	
			$organisation_unit = new OrganisationUnit($organisation_unit_id);
			if ($organisation_unit->create_group_in_organisation_unit($group_id) == true)
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
}
?>