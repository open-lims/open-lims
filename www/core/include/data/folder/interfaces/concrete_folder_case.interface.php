<?php
/**
 * @package data
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
 * Concrete Folder Case Interface
 * @package data
 */
interface ConcreteFolderCaseInterface
{
	function __construct($folder_id);
	function __destruct();
	
	/**
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 * Called from Folder directly. No direct call necessary
	 */
	public function delete($recursive, $content);
	
	/**
	 * Checks if $folder_id is a case of User Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id);
}

?>
