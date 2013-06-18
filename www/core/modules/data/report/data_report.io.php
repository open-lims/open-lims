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
 * Data Report IO Class
 * @package data
 */
class DataReportIO
{
	/**
	 * @param string $sql
	 * @param integer $item_id
	 * @param object $pdf
	 * @return object
	 */
	public static function get_data_item_report($sql, $item_id, $pdf)
	{
		if ($sql and is_object($pdf))
		{
			// Values
			$value_array = Data_Wrapper::list_item_values($sql);
			
			if (is_array($value_array) and count($value_array) >= 1)
			{				
				foreach ($value_array as $key => $value)
				{
					$value_object = Value::get_instance($value['id']);
					$value_object_value_array = $value_object->get_value_content(false);
					
					$pdf->addPage();
					
					$pdf->SetFont('dejavusans', 'B', 14, '', true);
					$pdf->Write(0, 'Value - '.$value_object->get_name().'', '', 0, 'C', true, 0, false, false, 0);
					$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
					$pdf->SetFont('dejavusans', '', 14, '', true);
					
					if (is_array($value_object_value_array) and count($value_object_value_array) >= 1)
					{
						foreach ($value_object_value_array as $sub_key => $sub_value)
						{
							$sub_value['content'][0] = str_replace("\n","<br />",$sub_value['content'][0]);
							
							if ($sub_value['type'] == "textarea")
							{
								$pdf->SetFont('dejavusans', 'B', 14, '', true);
								$pdf->MultiCell(190, 9, $sub_value['title'], 1, 'L', 1, 1, '', '', true, 0, true, true, 0, "T");
								$pdf->SetFont('dejavusans', '', 14, '', true);
								$pdf->MultiCell(190, 40, $sub_value['content'][0], 1, 'L', 1, 1, '', '', true, 0, true, true, 0, "T");
							}
							else
							{
								$pdf->SetFont('dejavusans', 'B', 14, '', true);
								$pdf->MultiCell(80, 9, $sub_value['title'], 1, 'L', 1, 0, '', '', true, 0, true, true, 0, "T");
								$pdf->SetFont('dejavusans', '', 14, '', true);
								$pdf->MultiCell(110, 9, $sub_value['content'][0], 1, 'L', 1, 1, '', '', false, 0, true, true, 0, "T");
							}
						}
					}
					
					$value_object_version_array = $value_object->get_value_internal_revisions();
					
					if (is_array($value_object_version_array) and count($value_object_version_array) >= 1)
					{
						$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
						
						$header_array = array(array("name" => "name", "title" => "Name", "width" =>60),
												array("name" => "version", "title" => "Version", "width" =>25),
												array("name" => "datetime", "title" => "Date/Time", "width" =>55),
												array("name" => "user", "title" => "User", "width" =>50)
												);
						
						$report_table = new ReportTable_IO($pdf);						
						$report_table->add_header($header_array);
						
						foreach($value_object_version_array as $sub_key => $sub_value)
						{
							$value_version = clone $value_object;
							$value_version->open_internal_revision($sub_value);													
							$datetime_handler = new DatetimeHandler($value_version->get_datetime());
							$owner = new User($value_version->get_version_owner_id());
							
							$line_array = array(array("name" => "name", "content" => $value_version->get_name()),
												array("name" => "version", "content" => $value_version->get_version()),
												array("name" => "datetime", "content" => $datetime_handler->get_datetime(false)),
												array("name" => "user", "content" => $owner->get_full_name(true))
												);
												
							$report_table->add_line($line_array);
						}
					}
					
					$pdf = $report_table->get_pdf();
					
				}
			}
			
			
			// Files
			$file_array = Data_Wrapper::list_item_files($sql, null, null, null, null);
			
			if (is_array($file_array) and count($file_array) >= 1)
			{	
				$pdf->addPage();
				
				$pdf->SetFont('dejavusans', 'B', 14, '', true);
				$pdf->Write(0, 'Files', '', 0, 'C', true, 0, false, false, 0);
				$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
				$pdf->SetFont('dejavusans', '', 14, '', true);
				
				$header_array = array(array("name" => "name", "title" => "Name", "width" =>62),
												array("name" => "version", "title" => "Version", "width" =>25),
												array("name" => "datetime", "title" => "Date/Time", "width" =>55),
												array("name" => "user", "title" => "User", "width" =>48)
												);
				
				$report_table = new ReportTable_IO($pdf, '17');						
				$report_table->add_header($header_array);
												
				foreach ($file_array as $key => $value)
				{
					$file = File::get_instance($value['id']);
					$owner = new User($value['owner_id']);
					$datetime_handler = new DatetimeHandler($value['datetime']);
					
					$line_array = array(array("name" => "name", "content" => $value['name']),
												array("name" => "version", "content" => $file->get_version()),
												array("name" => "datetime", "content" => $datetime_handler->get_datetime(false)),
												array("name" => "user", "content" => $owner->get_full_name(true))
												);
												
					$report_table->add_line($line_array);
				}
				
				$pdf = $report_table->get_pdf();
			}
			
			
			// Parameters
			$parameter_array = Data_Wrapper::list_item_parameters($sql);
						
			if (is_array($parameter_array) and count($parameter_array) >= 1)
			{
				foreach ($parameter_array as $key => $value)
				{
					$parameter_object = Parameter::get_instance($value['id']);
					$parameter_template = new ParameterTemplate($parameter_object->get_template_id());
					
					$parameter_template_field_array = $parameter_template->get_fields();
					$parameter_template_limit_array = $parameter_template->get_limits();
			
					$parameter_value_array = $parameter_object->get_values();
					$parameter_method_array = $parameter_object->get_methods();
					$parameter_status_array = $parameter_object->get_status();
					
					$pdf->addPage();
						
					$pdf->SetFont('dejavusans', 'B', 14, '', true);
					$pdf->Write(0, ''.$parameter_object->get_name().'', '', 0, 'L', true, 0, false, false, 0);
					$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
					$pdf->SetFont('dejavusans', '', 12, '', true);
				
					$header_array = array(array("name" => "parameter", "title" => "Parameter", "width" =>44),
												array("name" => "value", "title" => "Value", "width" =>25),
												array("name" => "unit", "title" => "Unit", "width" =>25),
												array("name" => "min", "title" => "Min", "width" =>20),
												array("name" => "max", "title" => "Max", "width" =>20),
												array("name" => "method", "title" => "Method", "width" =>30),
												array("name" => "status", "title" => "Status", "width" =>25)
												);				

					$report_table = new ReportTable_IO($pdf, '', '12');						
					$report_table->add_header($header_array);
												
					if(is_array($parameter_template_field_array) and count($parameter_template_field_array) >= 1)
					{
						foreach($parameter_template_field_array as $key => $value)
						{
								
							if (is_numeric($value['unit']))
							{
								if ($value['unit_exponent'] < 0)
								{
									$unit_exponent = $value['unit_exponent']*-1;
									$unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
								}
								else
								{
									$unit_prefix = MeasuringUnit::get_prefix( $value['unit_exponent'], true);
								}
								
								$measuring_unit = new MeasuringUnit($value['unit']);
								
								$unit = $unit_prefix[1]."".$measuring_unit->get_unit_symbol();
							}
							elseif (is_numeric($value['unit_ratio']))
							{
								$measuring_unit_ratio = new MeasuringUnitRatio($value['unit_ratio']);
								$unit = $measuring_unit_ratio->get_symbol();
							}
							else
							{
								$unit = "";
							}
							
							if ($parameter_method_array[$key])
							{
								$method = $parameter_method_array[$key];
							}
							else
							{
								$method = "none";
							}
							
							if ($parameter_status_array[$key])
							{
								$status = $parameter_status_array[$key];
							}
							else
							{
								$status = "none";
							}
							
							$line_array = array(array("name" => "parameter", "content" => $value['name']),
												array("name" => "value", "content" => $parameter_value_array[$value['pk']]),
												array("name" => "unit", "content" => $unit),
												array("name" => "min", "content" => $parameter_template_limit_array[0]['lsl'][$key]),
												array("name" => "max", "content" => $parameter_template_limit_array[0]['usl'][$key]),
												array("name" => "method", "content" => $method),
												array("name" => "status", "content" => $status)
												);
												
							$report_table->add_line($line_array);
						}	
					}				
												
					
												
					$pdf = $report_table->get_pdf();							
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