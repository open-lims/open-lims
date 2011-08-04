<?php
/**
 * @package equipment
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
 * Equipment Report IO Class
 * @package equipment
 */
class EquipmentReportIO
{
	public static function get_equipment_item_report($sql, $item_id, $pdf)
	{
		if ($sql and is_object($pdf))
		{	
			$result_array = Equipment_Wrapper::list_item_equipments($sql, null, null, null, null);
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				$pdf->addPage();
				
				$pdf->SetFont('dejavusans', 'B', 14, '', true);
				
				$pdf->Write(0, 'Equipment', '', 0, 'C', true, 0, false, false, 0);
				$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
				
				$pdf->MultiCell(70, 0, "Equipment Name", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(60, 0, "Category", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(60, 0, "Date/Time", 1, 'L', 1, 1, '', '', true, 0, false, true, 0);
				
				$pdf->SetFont('dejavusans', '', 14, '', true);
				
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M y H:i");
					
					$pdf->MultiCell(70, 0, $result_array[$key][name], 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(60, 0, $result_array[$key][category], 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(60, 0, $result_array[$key][datetime], 1, 'L', 1, 1, '', '', true, 0, false, true, 0);
				}
			}
			
			return $pdf;
		}
		else
		{
			return null;
		}
	}
}