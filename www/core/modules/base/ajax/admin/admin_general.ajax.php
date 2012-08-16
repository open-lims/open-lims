<?php
/**
 * @package base
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
 * @package base
 */
class AdminGeneralAjax
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
	public static function list_languages($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{	
		global $user;
		
		if ($user->is_admin())
		{	
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_languages($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key][symbol] = "<img src='images/icons/language.png' alt='N' border='0' />";
				}
			}
			else
			{
				$list_request->override_last_line("<span class='italic'>No results found!</span>");
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
	public static function count_languages($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_languages();
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
	 */
	public static function list_currencies($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_currencies($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key][symbol] = "<img src='images/icons/currency.png' alt='N' border='0' />";
				}
			}
			else
			{
				$list_request->override_last_line("<span class='italic'>No results found!</span>");
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
	public static function count_currencies($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_currencies();
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
	 */
	public static function list_timezones($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_timezones($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key][symbol] = "<img src='images/icons/timezone.png' alt='N' border='0' />";
					
					if ($list_array[$key][deviation] > 0)
					{
						$list_array[$key][deviation] = "GMT+".$list_array[$key][deviation];
					}
					elseif ($list_array[$key][deviation] < 0)
					{
						$list_array[$key][deviation] = "GMT".$list_array[$key][deviation];
					}
					else
					{
						$list_array[$key][deviation] = "GTM+/-0";
					}
				}
			}
			else
			{
				$list_request->override_last_line("<span class='italic'>No results found!</span>");
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
	public static function count_timezones($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_timezones();
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
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function list_measuring_units($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$argument_array = json_decode($json_argument_array);
			
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
			
			$list_array = Environment_Wrapper::list_measuring_units($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
		
			
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key][symbol] = "<img src='images/icons/measuring_unit.png' alt='N' border='0' />";
					
					switch ($list_array[$key][type]):
					
						case 1:
							$list_array[$key][type] = "length";
						break;
						
						case 2:
							$list_array[$key][type] = "mass";
						break;
						
						case 3:
							$list_array[$key][type] = "electric current";
						break;
						
						case 4:
							$list_array[$key][type] = "thermodynamic temperature";
						break;
						
						case 5:
							$list_array[$key][type] = "amount of substance";
						break;
						
						case 6:
							$list_array[$key][type] = "luminous intensity";
						break;
						
						case 7:
							$list_array[$key][type] = "time";
						break;
					
					endswitch;
				}
			}
			else
			{
				$list_request->override_last_line("<span class='italic'>No results found!</span>");
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
	 * @param string $json_argument_array´
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_measuring_units($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_measuring_units();
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
	 */
	public static function list_paper_sizes($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$list_request = new ListRequest_IO();
			$list_request->set_column_array($json_column_array);
		
			if (!is_numeric($entries_per_page) or $entries_per_page < 1)
			{
				$entries_per_page = 20;
			}
						
			$list_array = Environment_Wrapper::list_paper_sizes($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
					
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					$list_array[$key][symbol] = "<img src='images/icons/text.png' alt='' />";
					
					if ($value['standard'] == 't')
					{
						$list_array[$key][name] = $list_array[$key][name]." <img src='images/icons/status_ok.png' alt='Standard' />";
					}
					
					$list_array[$key][width] = $list_array[$key][width]." mm";
					$list_array[$key][height] = $list_array[$key][height]." mm";
					$list_array[$key][margin_left] = $list_array[$key][margin_left]." mm";
					$list_array[$key][margin_right] = $list_array[$key][margin_right]." mm";
					$list_array[$key][margin_top] = $list_array[$key][margin_top]." mm";
					$list_array[$key][margin_bottom] = $list_array[$key][margin_bottom]." mm";
					
					$list_array[$key]['edit'] = "<a href='#' class='BaseAdminPaperSizeEdit' id='BaseAdminPaperSizeEdit".$list_array[$key][id]."'><img src='images/icons/edit.png' alt='' style='border: 0;' /></a>";
					
					if ($value['base'] == 'f')
					{
						$list_array[$key]['delete'] = "<a href='#' class='BaseAdminPaperSizeDelete' id='BaseAdminPaperSizeDelete".$list_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
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
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @return integer
	 * @throws BaseUserAccessDeniedException
	 */
	public static function count_paper_sizes($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return Environment_Wrapper::count_paper_sizes();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $name
	 * @param string $width
	 * @param string $height
	 * @param string $margin_left
	 * @param string $margin_right
	 * @param string $margin_top
	 * @param string $margin_bottom
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 */
	public static function add_paper_size($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		global $user;
		
		if ($user->is_admin())
		{
			$paper_size = new PaperSize(null);
			if ($paper_size->create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom) == true)
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
	 * @param string $id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function get_paper_size($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$paper_size = new PaperSize($id);
				
				$return_array = array();
				
				$return_array[0] = $paper_size->get_base();
				$return_array[1] = $paper_size->get_name();
				$return_array[2] = $paper_size->get_width();
				$return_array[3] = $paper_size->get_height();
				$return_array[4] = $paper_size->get_margin_left();
				$return_array[5] = $paper_size->get_margin_right();
				$return_array[6] = $paper_size->get_margin_top();
				$return_array[7] = $paper_size->get_margin_bottom();
				
				return json_encode($return_array);
			}
			else
			{
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @param string $name
	 * @param string $width
	 * @param string $height
	 * @param string $margin_left
	 * @param string $margin_right
	 * @param string $margin_top
	 * @param string $margin_bottom
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function set_paper_size($id, $name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id) and $name and is_numeric($width) and is_numeric($height) and is_numeric($margin_left) and is_numeric($margin_right) and is_numeric($margin_top) and is_numeric($margin_bottom))
			{
				$paper_size = new PaperSize($id);
				
				if ($paper_size->set_name($name) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_width($width) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_height($height) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_left($margin_left) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_right($margin_right) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_top($margin_top) == false)
				{
					return "0";
				}
				
				if ($paper_size->set_margin_bottom($margin_bottom) == false)
				{
					return "0";
				}
				
				return "1";
			}
			else
			{
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $id
	 * @return string
	 * @throws BaseUserAccessDeniedException
	 * @throws BaseEnvironmentPaperSizeIDMissingException
	 */
	public static function delete_paper_size($id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (is_numeric($id))
			{
				$paper_size = new PaperSize($id);
				if ($paper_size->delete() == true)
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
				throw new BaseEnvironmentPaperSizeIDMissingException();
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
}
?>