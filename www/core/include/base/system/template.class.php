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
// require_once("interfaces/html_template.interface.php");

/**
 * Template Engine Class (View)
 * @package base
 */
class Template // implements TemplateInterface
{
	private static $current_folder;
	private static $fallback_folder;
	
	protected $string;
	
	protected function __construct($file)
	{
		$current_folder_file = constant("WWW_DIR")."/template/".self::$current_folder."/".$file;
		
		if (file_exists($current_folder_file) == true)
		{
			$this->open_file($current_folder_file);
		}
		else
		{
			$fallback_folder_file = constant("WWW_DIR")."/template/".self::$fallback_folder."/".$file;
			
			if (file_exists($fallback_folder_file) == true)
			{
				$this->open_file($fallback_folder_file);
			}
			else
			{
				if (file_exists($file) == true)
				{
					$this->open_file($file);
				}
				else
				{
					die("Template Engine: File Not Found!<br />".$file);
				}
			}
		}
	}
	
	private function open_file($file)
	{
		if ($file)
		{
			$handler = fopen($file ,"r");
				
			if (filesize($file) > 0)
			{
				$this->string = fread($handler, filesize($file));
				$this->string = str_replace("\\[[","[%OB%]",$this->string);
				$this->string = str_replace("\\]]","[%CB%]",$this->string);
			}
			else
			{
				die("Template Engine: File Is Empty!<br />".$file);
			}
		}
	}
	
	public static function set_current_folder($folder)
	{
		if ($folder)
		{
			self::$current_folder = $folder;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function set_fallback_folder($folder)
	{
		if ($folder)
		{
			self::$fallback_folder = $folder;
			return true;
		}
		else
		{
			return false;
		}
	}
}