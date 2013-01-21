<?php
/**
 * @package extension
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
 * Extension Request Class
 * @package extension
 */
class ExtensionRequest
{
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		
	}
	
	/**
	 * @param string $alias
	 * @throws BaseExtensionClassNotFoundException
	 * @throws BaseExtensionFileNotFoundException
	 */
	public static function io_handler($alias)
	{
		if ($_GET['extension'])
		{
			$extension = new Extension($_GET['extension']);

			$main_file = constant("EXTENSION_DIR")."/".$extension->get_folder()."/".$extension->get_main_file();
			$main_class = $extension->get_class();
			
			if (file_exists($main_file))
			{
				require_once($main_file);
				
				if (class_exists($main_class))
				{			
					$main_class::main();
				}
				else
				{
					throw new BaseExtensionClassNotFoundException();
				}
			}
			else
			{
				throw new BaseExtensionFileNotFoundException();
			}
		}
		else
		{
			require_once("io/extension.io.php");
			ExtensionIO::home();
		}
	}
}