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
	$classes['SampleException']				= $path_prefix."core/include/sample/exceptions/sample_exception.class.php";
	$classes['SampleSecurityException']		= $path_prefix."core/include/sample/exceptions/sample_security_exception.class.php";
	
	$classes['Sample']						= $path_prefix."core/include/sample/sample.class.php";
	$classes['SampleDepository']			= $path_prefix."core/include/sample/sample_depository.class.php";
	$classes['SampleFolder']				= $path_prefix."core/include/sample/sample_folder.class.php";
	$classes['SampleVirtualFolder']			= $path_prefix."core/include/sample/sample_virtual_folder.class.php";
	$classes['SampleItem']					= $path_prefix."core/include/sample/sample_item.class.php";
	$classes['SampleItemFactory']			= $path_prefix."core/include/sample/sample_item_factory.class.php";
	$classes['SampleSecurity']				= $path_prefix."core/include/sample/sample_security.class.php";
	$classes['SampleTemplate']				= $path_prefix."core/include/sample/sample_template.class.php";
	$classes['SampleTemplateCat']			= $path_prefix."core/include/sample/sample_template_cat.class.php";
?>