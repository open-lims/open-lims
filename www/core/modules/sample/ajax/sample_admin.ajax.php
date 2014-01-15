<?php
/**
 * @package sample
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
 * Sample Admin AJAX IO Class
 * @package sample
 */
class SampleAdminAjax
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
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function list_user_permissions($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[1];
		
		if (is_numeric($sample_id))
		{
			$sample = new Sample($sample_id);
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = Sample_Wrapper::list_sample_users($sample_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$list_array[$key]['symbol'] = "<img src='images/icons/user.png' alt='' style='border:0;' />";
						
						if ($list_array[$key]['user'])
						{
							$user = new User($list_array[$key]['user']);
						}
						else
						{
							$user = new User(1);
						}
						
						$list_array[$key]['username'] = $user->get_username();
						$list_array[$key]['name'] = $user->get_full_name(false);
						
						if ($list_array[$key]['read'] == 't')
						{
							$list_array[$key]['read'] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key]['read'] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						if ($list_array[$key]['write'] == 't')
						{
							$list_array[$key]['write'] = "<img src='images/icons/permission_ok_active.png' alt='' />";
						}
						else
						{
							$list_array[$key]['write'] = "<img src='images/icons/permission_denied_active.png' alt='' />";
						}
						
						$delete_paramquery = $_GET;
						$delete_paramquery['run'] = "admin_permission_user_delete";
						$delete_paramquery['id'] = $list_array[$key]['user'];
						unset($delete_paramquery['sure']);
						$delete_params = http_build_query($delete_paramquery,'','&#38;');

						if ($sample->get_owner_id() == $list_array[$key]['user'])
						{
							$list_array[$key]['delete']['link'] = "";
							$list_array[$key]['delete']['content'] = "";
						}
						else
						{
							$list_array[$key]['delete']['link'] = $delete_params;
							$list_array[$key]['delete']['content'] = "delete";
						}
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
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws SampleIDMissingException
	 */
	public static function count_user_permissions($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[1];
		
		if (is_numeric($sample_id))
		{
			return Sample_Wrapper::count_sample_users($sample_id);
		}
		else
		{
			throw new SampleIDMissingException();
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
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function list_organisation_unit_permissions($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[1];
		
		if (is_numeric($sample_id))
		{
			$sample = new Sample($sample_id);
		
			if ($sample->get_owner_id() == $user->get_user_id() or
				$user->is_admin() == true)
			{
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
							
				$list_array = Sample_Wrapper::list_sample_organisation_units($sample_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$list_array[$key]['symbol'] = "<img src='images/icons/organisation_unit.png' alt='' style='border:0;' />";
						
						if ($list_array[$key]['organisation_unit_id'])
						{
							$organisation_unit = new OrganisationUnit($list_array[$key]['organisation_unit_id']);
						}
						else
						{
							$organisation_unit = new OrganisationUnit(1);
						}
						
						$list_array[$key]['name'] = $organisation_unit->get_name();
						
						$delete_paramquery = $_GET;
						$delete_paramquery['run'] = "admin_permission_ou_delete";
						$delete_paramquery['id'] = $list_array[$key]['organisation_unit_id'];
						unset($delete_paramquery['sure']);
						$delete_params = http_build_query($delete_paramquery,'','&#38;');
						
						$list_array[$key]['delete']['link'] = $delete_params;
						$list_array[$key]['delete']['content'] = "delete";
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
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws SampleIDMissingException
	 */
	public static function count_organisation_unit_permissions($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$sample_id = $argument_array[1];
		
		if (is_numeric($sample_id))
		{
			return Sample_Wrapper::count_sample_organisation_units($sample_id);
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
}