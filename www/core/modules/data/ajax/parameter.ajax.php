<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz, Roman Quiring
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
 * Parameter AJAX IO Class
 * @package data
 */
class ParameterAjax
{
	/**
	 * @todo business logic exceptions
	 * @param integer $folder_id
	 * @param integer $type_id
	 * @param string $parameter_array
	 * @param string $get_array
	 * @return string
	 */
	public static function add_as_item($folder_id, $type_id, $limit_id, $parameter_array, $get_array)
	{
		global $user, $transaction;
		
		$parent_folder = Folder::get_instance($folder_id);
		
		if ($parent_folder->is_write_access())
		{
			$transaction_id = $transaction->begin();
			
			$parameter_array = json_decode($parameter_array, true);

			$parameter = ParameterTemplateParameter::get_instance(null);
			$parameter_add_successful = $parameter->create($folder_id, $user->get_user_id(), $type_id, $limit_id, $parameter_array);
			
			if ($parameter_add_successful)
			{				
				$item_id = $parameter->get_item_id();
				
				$item_add_event = new ItemAddEvent($item_id, unserialize($get_array), null);
				$event_handler = new EventHandler($item_add_event);
				if ($event_handler->get_success() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return "1";
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new BaseException();
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new BaseException();
			}
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}

	public static function update($parameter_id, $parameter_array,  $limit_id, $major, $current)
	{
		if (is_numeric($parameter_id))
		{
			$parameter = ParameterTemplateParameter::get_instance($parameter_id);
			$parameter_array = json_decode($parameter_array, true);

			$parameter->update($parameter_array, $limit_id, null, $major, $current);
			return "1";
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
	
	public static function get_limits($parameter_template_id, $parameter_limit_id)
	{
		if (is_numeric($parameter_template_id) and is_numeric($parameter_limit_id))
		{
			$parameter_template = new ParameterTemplate($parameter_template_id);
			
			return json_encode($parameter_template->get_limits($parameter_limit_id));
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
}
?>