<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * 
 */
require_once("interfaces/sample_item_factory.interface.php");

/**
 * Sample Item Factory Class
 * @package sample
 */
class SampleItemFactory implements SampleItemFactoryInterface
{
	/**
	 * @todo check over time tasks via event
	 */
	public static function create($sample_id, $item_id, $gid, $keywords, $description)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true)
		{
			$sample = new Sample($sample_id);
			$sample_item = new SampleItem($sample_id);
			
			$sample_item->set_gid($gid);
			
			if ($sample_item->set_item_id($item_id) == false)
			{
				return false;	
			}
			
			if ($sample_item->link_item() == false)
			{
				return false;	
			}
		
			if (($class_name = $sample_item->is_classified()) == true)
			{
				if ($sample_item->set_class($class_name) == false)
				{
					return false;
				}
			}
			
			if ($description_required == true xor $keywords_required == true)
			{
				if ($description_required == false and $keywords_required == true)
				{
					if ($sample_item->set_information(null,$_POST[keywords]) == false)
					{
						return false;
					}
				}
				else
				{
					if ($sample_item->set_information($_POST[description],null) == false)
					{
						return false;
					}
				}
			}
			else
			{
				if ($description_required == true and $keywords_required == true)
				{
					if ($sample_item->set_information($_POST[description],$_POST[keywords]) == false)
					{
						return false;
					}
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}
}
?>