<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Admin Base Service AJAX IO Class
 * @package base
 */
class AdminBaseServiceAjax
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
	public static function list_service($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
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
						
			$list_array = System_Wrapper::list_base_service($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));

			
			if (is_array($list_array) and count($list_array) >= 1)
			{		
				foreach($list_array as $key => $value)
				{	
					$list_array[$key]['icon'] = "<img src='images/icons/service.png' alt='' style='border: 0;' />";
					
					switch($list_array[$key]['status']):
					
						case "0":
							$list_array[$key]['status'] = "Stopped";
							
							$list_array[$key]['start'] = "<a href='#' class='BaseAdminServiceStartButton' id='BaseAdminServiceStartButton".$list_array[$key]['id']."'><img src='images/icons/service_start.png' alt='E' style='border: 0;' /></a>";
							$list_array[$key]['stop'] = "<a href='#'><img src='images/icons/service_stop_d.png' alt='E' style='border: 0;' /></a>";
						break;
					
						case "1":
							$service = new Service($list_array[$key]['id']);
							if ($service->is_responding() == false)
							{
								$list_array[$key]['status'] = "Not Responing (Not Running?)";
								
								$list_array[$key]['start'] = "<a href='#' class='BaseAdminServiceStartButton' id='BaseAdminServiceStartButton".$list_array[$key]['id']."'><img src='images/icons/service_start.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#' class='BaseAdminServiceStopButton' id='BaseAdminServiceStopButton".$list_array[$key]['id']."'><img src='images/icons/service_stop.png' alt='E' style='border: 0;' /></a>";
							}
							else
							{
								$list_array[$key]['status'] = "Running";
								
								$list_array[$key]['start'] = "<a href='#'><img src='images/icons/service_start_d.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#' class='BaseAdminServiceStopButton' id='BaseAdminServiceStopButton".$list_array[$key]['id']."'><img src='images/icons/service_stop.png' alt='E' style='border: 0;' /></a>";
							}
						break;
						
						case "2":
							$service = new Service($list_array[$key]['id']);
							if ($service->is_responding() == false)
							{
								$list_array[$key]['status'] = "Stopping (Not Responing)";
							
								$list_array[$key]['start'] = "<a href='#'><img src='images/icons/service_start_d.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#' class='BaseAdminServiceStopButton' id='BaseAdminServiceStopButton".$list_array[$key]['id']."'><img src='images/icons/service_stop.png' alt='E' style='border: 0;' /></a>";
							}
							else
							{
								$list_array[$key]['status'] = "Stopping";
							
								$list_array[$key]['start'] = "<a href='#'><img src='images/icons/service_start_d.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#'><img src='images/icons/service_stop_d.png' alt='E' style='border: 0;' /></a>";
							}
						break;
						
						case "3":
							$service = new Service($list_array[$key]['id']);
							if ($service->is_responding() == false)
							{
								$list_array[$key]['status'] = "Stopping (Not Responing)";
							
								$list_array[$key]['start'] = "<a href='#'><img src='images/icons/service_start_d.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#' class='BaseAdminServiceStopButton' id='BaseAdminServiceStopButton".$list_array[$key]['id']."'><img src='images/icons/service_stop.png' alt='E' style='border: 0;' /></a>";
							}
							else
							{
								$list_array[$key]['status'] = "Stopping (Hard)";
							
								$list_array[$key]['start'] = "<a href='#'><img src='images/icons/service_start_d.png' alt='E' style='border: 0;' /></a>";
								$list_array[$key]['stop'] = "<a href='#'><img src='images/icons/service_stop_d.png' alt='E' style='border: 0;' /></a>";
							}	
						break;
					
						case "4":
							$list_array[$key]['status'] = "Error";
							
							$list_array[$key]['start'] = "<a href='#' class='BaseAdminServiceStartButton' id='BaseAdminServiceStartButton".$list_array[$key]['id']."'><img src='images/icons/service_start.png' alt='E' style='border: 0;' /></a>";
							$list_array[$key]['stop'] = "<a href='#'><img src='images/icons/service_stop_d.png' alt='E' style='border: 0;' /></a>";
						break;
						
						default:
							$list_array[$key]['status'] = "Unknown Status";
							
							$list_array[$key]['start'] = "<a href='#' class='BaseAdminServiceStartButton' id='BaseAdminServiceStartButton".$list_array[$key]['id']."'><img src='images/icons/service_start.png' alt='E' style='border: 0;' /></a>";
							$list_array[$key]['stop'] = "<a href='#' class='BaseAdminServiceStopButton' id='BaseAdminServiceStopButton".$list_array[$key]['id']."'><img src='images/icons/service_stop.png' alt='E' style='border: 0;' /></a>";
						break;
					
					endswitch;
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
	public static function count_service($json_argument_array)
	{
		global $user;
		
		if ($user->is_admin())
		{
			return System_Wrapper::count_base_service();
		}
		else
		{
			throw new BaseUserAccessDeniedException();	
		}
	}
	
	/**
	 * @param string $service_id
	 * @return string
	 * @throws BaseServiceIDMissingException
	 * @throws BaseUserAccessDeniedException
	 */
	public static function start($service_id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (!is_numeric($service_id))
			{
				throw new BaseServiceIDMissingException();
			}
			
			$service = new Service($service_id);
			if ($service->start())
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
	
	/**
	 * @param string $service_id
	 * @return string
	 * @throws BaseServiceIDMissingException
	 * @throws BaseUserAccessDeniedException
	 */
	public static function stop($service_id)
	{
		global $user;
		
		if ($user->is_admin())
		{
			if (!is_numeric($service_id))
			{
				throw new BaseServiceIDMissingException();
			}
			
			$service = new Service($service_id);
			if ($service->stop())
			{
				return 1;	
			}
			else
			{
				return 0;
			}
		}
		else
		{
			throw new BaseUserAccessDeniedException();
		}
	}
}
?>