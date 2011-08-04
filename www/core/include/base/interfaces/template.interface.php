<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Template Engine Interface
 * @package base
 */
interface TemplateInterface
{
	/**
	 * @param string $file complete path of file
	 */
	function __construct($file);
	
	function __destruct();
	
	/**
	 * Sets a var. of template
	 * @param string $name Address
	 * @param string $value Content
	 */
	public function set_var($name, $value);
	
	/**
	 * Writes the complete tempalte string into stdout
	 */
	public function output();
	
	/**
	 * Returns the complete template string
	 * @return string
	 */
	public function get_string();
}
?>