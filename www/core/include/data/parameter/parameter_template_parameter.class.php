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
 * 
 */
require_once("interfaces/parameter_template_parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_has_template.access.php");
}

/**
 * Parameter Template Management Class
 * @package data
 */
class ParameterTemplateParameter extends Parameter implements ParameterTemplateParameterInterface
{
	
	
	function __construct($parameter_id)
	{
		
	}
	
	function __destruct()
	{
		
	}
	
	public function create($folder_id, $owner_id, $template_id, $parameter_array)
	{
		
	}
	
	public function update($value_array, $previous_version_id, $major, $current)
	{
		
	}
}
?>