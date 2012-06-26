<?php
/**
 * @package extension
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
 * Extension Request Class
 * @package extension
 */
class ExtensionRequest
{
	public static function ajax_handler($alias)
	{
		
	}
	
	public static function io_handler($alias)
	{
		if ($_GET['extension'])
		{
			$extension = new Extension($_GET['extension']);

			$main_file = constant("EXTENSION_DIR")."/".$extension->get_folder()."/".$extension->get_main_file();
			$main_class = $extension->get_class();
			
			require_once($main_file);
			
			$main_class::main();
		}
		else
		{
			require_once("io/extension.io.php");
			ExtensionIO::home();
		}
	}
}