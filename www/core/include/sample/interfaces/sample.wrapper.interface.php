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
 * Sample Wrapper Interface
 * @package sample
 */ 		 
interface Sample_WrapperInterface
{
	public static function list_sample_templates($order_by, $order_method, $start, $end);
	public static function count_list_sample_templates();
	public static function list_sample_template_categories($order_by, $order_method, $start, $end);
	public static function count_list_sample_template_categories();
	public static function count_user_samples($user_id);
}
?>