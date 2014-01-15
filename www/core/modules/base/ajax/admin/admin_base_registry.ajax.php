<?php
/**
 * @package base
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
 * Admin Base Registry AJAX IO Class
 * @package base
 */
class AdminBaseRegistryAjax
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
	 */
	public static function list_registry($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if ($get_array)
			{
				$_GET = unserialize($get_array);	
			}
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = System_Wrapper::list_base_registry($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
			if (is_array($list_array) and count($list_array) >= 1)
			{		
				foreach($list_array as $key => $value)
				{	
					$list_array[$key]['icon'] = "<img src='images/icons/registry.png' alt='' style='border: 0;' />";
					$list_array[$key]['edit'] = "<a href='#'><img src='images/icons/edit.png' alt='E' style='border: 0;' /></a>";
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
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_registry($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return System_Wrapper::count_base_registry();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseRegistyIDMissingException
	 */
	public static function edit($id)
	{
		if (is_numeric($id))
		{
			$registry = new Registry($id);
			
			$template = new HTMLTemplate("base/admin/base_registry/edit_window.html");
			$template->set_var("value", $registry->get_registry_value());
			$template->set_var("id", $id);
			$array['content_caption'] = "Edit Entry";
			$array['height'] = 170;
			$array['width'] = 400;

			$array['continue_caption'] = "Save";
			$array['cancel_caption'] = "Cancel";
			$array['content'] = $template->get_string();
			$array['container'] = "#BaseAdminRegistryEditWindow";
			
			$continue_handler_template = new JSTemplate("base/admin/base_registry/js/edit.js");
			$continue_handler_template->set_var("session_id", $_GET['session_id']);
			
			$array['continue_handler'] = $continue_handler_template->get_string();
			
			return json_encode($array);
		}
		else
		{
			throw new BaseRegistryIDMissingException();
		}
	}
	
	/**
	 * @param integer $id
	 * @param string $value
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseRegistyIDMissingException
	 */
	public static function edit_handler($id, $value)
	{
		global $user;
		
		if (is_numeric($id) and $value)
		{
			if ($user->is_admin())
			{
				$registry = new Registry($id);
				$registry->set_registry_value($value);
				return "1";
			}
			else
			{
				throw new BaseUserAccessDeniedException();
			}
		}
		else
		{
			throw new BaseRegistryIDMissingException();
		}
	}
}
?>