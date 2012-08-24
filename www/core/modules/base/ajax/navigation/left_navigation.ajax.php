<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz, Roman Quiring
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
 * Left Nav AJAX IO Class
 * @package base
 */
class LeftNavigationAjax
{
	/**
	 * @param integer $id
	 */
	public function set_active($id)
	{
		global $session;	
	
		if ($id)
		{
			$session->write_value("LEFT_NAVIGATION_ACTIVE", $id, true);
		}
	}
}


?>