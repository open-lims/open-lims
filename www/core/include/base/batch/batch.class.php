<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
require_once("interfaces/batch.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/base_batch_run.access.php");
	require_once("access/base_batch_type.access.php");
}

/**
 * Batch Class
 * @package base
 */
class Batch implements BatchInterface
{
	/**
	 * @see BatchInterface::__construct()
	 * @param $batch_id
	 */
	function __construct($batch_id)
	{
		
	}
	
	/**
	 * @see BatchInterface::create()
	 * @param integer $type_id
	 * @return integer
	 */
	public function create($type_id)
	{
		global $user;
		
		if (is_numeric($type_id))
		{
			$batch_type_access = new BaseBatchType_Access($type_id);
			if ($batch_type_binary_id = $batch_type_access->get_binary_id())
			{
				$batch_run_access = new BaseBatchRun_Access(null);
				if ($batch_run_id = $batch_run_access->create($type_id, $batch_type_binary_id, $user->get_user_id()))
				{
					$this->__construct($batch_run_id);
					return $batch_run_id;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @see BatchInterface::get_type_id_by_internal_name()
	 * @param string $internal_name
	 * @return integer
	 */
	public static function get_type_id_by_internal_name($internal_name)
	{
		return BaseBatchType_Access::get_id_by_internal_name($internal_name);
	}
}
?>