<?php
/**
 * @package base
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
 * Batch IO Class
 * @package base
 */
class BatchIO
{
	public static function list_batches()
	{
		global $user;
		
		$list = new List_IO("BaseBatchList", "ajax.php?nav=base", "batch_list_batches", "batch_count_batches", $argument_array, "BatchList");
		
		$list->add_column("", "symbol", false, "16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"), "name", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnStatus", "general"), "status", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnUser", "general"), "user", true, null);
		$list->add_column(Language::get_message("BaseGeneralListColumnCreatedAt", "general"), "created_at", true, null);
		
		$template = new HTMLTemplate("base/batch/list.html");
		
		if ($user->is_admin() and Batch::get_type_id_by_internal_name("TEST") != null)
		{
			$template->set_var("test_batch", true);
		}
		else
		{
			$template->set_var("test_batch", false);
		}
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
}
?>