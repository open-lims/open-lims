<?php
/**
 * @package sample
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
 * Sample Home IO Class
 * @package sample
 */
class SampleHomeIO
{
	public static function samples()
	{
		global $user;
		
		$template = new HTMLTemplate("sample/home/summary/my_samples.html");
		$template->set_var("samples",Sample_Wrapper::count_user_samples($user->get_user_id()));
		return $template->get_string();
	}
	
	/**
	 * Reserved for further usage
	 */
	public static function empty_space()
	{
		$template = new HTMLTemplate("sample/home/summary/empty.html");
		return $template->get_string();
	}
}
?>