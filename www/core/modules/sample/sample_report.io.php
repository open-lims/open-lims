<?php
/**
 * @package sample
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
require_once("report/sample_pdf.class.php");

/**
 * Sample Report IO Class
 * @package sample
 */
class SampleReportIO
{
	/**
	 * @return object
	 */
	public static function get_full_report()
	{
		if (class_exists("TCPDF"))
		{
			if ($_GET[sample_id])
			{
				$sample_id = $_GET[sample_id];
				$sample = new Sample($sample_id);
				$owner = new User($sample->get_owner_id());
				$owner_name = str_replace("&nbsp;"," ", $owner->get_full_name(false));
				
				$paper_size_info_array = PaperSize::get_standard_size();
				
				$format = Array($paper_size_info_array['width'], $paper_size_info_array['height']);
				
				if ($paper_size_info_array['width'] >= $paper_size_info_array['height'])
				{
					$orientation = "L";
				}
				else
				{
					$orientation = "P";
				}
				
				$pdf = new SamplePDF($sample_id, $sample->get_name(), $orientation, "mm", $format, true, 'UTF-8', false);
	
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('Open-LIMS');
				$pdf->SetTitle('Sample Report');
				
				$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
				
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
				
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				
				$pdf->SetMargins($paper_size_info_array['margin_left'], $paper_size_info_array['margin_top']*3, $paper_size_info_array['margin_right']);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
				
				$pdf->SetAutoPageBreak(TRUE, $paper_size_info_array['margin_bottom']);
				
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				
				$pdf->setLanguageArray($l);
				
				$pdf->setFontSubsetting(true);
				
				$pdf->SetFont('dejavusans', '', 14, '', true);
				
				$pdf->AddPage();
				
				$print_sample_id = "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
				
				$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
				
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetTextColor(0, 0, 0);
				
				$pdf->MultiCell(90, 0, "ID", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(100, 0, $print_sample_id, 1, '', 1, 1, '', '', true, 0, false, true, 0);
				
				$pdf->MultiCell(90, 0, "Name", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(100, 0, $sample->get_name(), 1, '', 1, 1, '', '', true, 0, false, true, 0);
				
				$pdf->MultiCell(90, 0, "Type/Template", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(100, 0, $sample->get_template_name(), 1, '', 1, 1, '', '', true, 0, false, true, 0);
				
				$pdf->MultiCell(90, 0, "Owner", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(100, 0, $owner_name, 1, '', 1, 1, '', '', true, 0, false, true, 0);
				
				$pdf->MultiCell(90, 0, "Status", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				if ($sample->get_availability() == true)
				{
					$pdf->MultiCell(100, 0, "available", 1, '', 1, 1, '', '', true, 0, false, true, 0);
				}
				else
				{
					$pdf->MultiCell(100, 0, "not available", 1, '', 1, 1, '', '', true, 0, false, true, 0);
				}
				
				$pdf->MultiCell(90, 0, "Date/Time", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$datetime = new DatetimeHandler($sample->get_datetime());
				$pdf->MultiCell(100, 0, $datetime->get_formatted_string("dS M Y H:i"), 1, '', 1, 1, '', '', true, 0, false, true, 0);
				
				if ($sample->get_manufacturer_id())
				{
					$manufacturer = new Manufacturer($sample->get_manufacturer_id());
					
					$pdf->MultiCell(90, 0, "Manufacturer", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(100, 0, $manufacturer->get_name(), 1, '', 1, 1, '', '', true, 0, false, true, 0);
				}
				
				if ($sample->get_date_of_expiry())
				{
					$pdf->MultiCell(90, 0, "Date of Expiry", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$date_of_expiry = new DatetimeHandler($sample->get_date_of_expiry());
					$pdf->MultiCell(100, 0, $date_of_expiry->get_formatted_string("dS M Y"), 1, '', 1, 1, '', '', true, 0, false, true, 0);
				}
				
				$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_report");
				
				if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
				{
					foreach($module_dialog_array as $key => $value)
					{
						if (file_exists($value['class_path']))
						{
							require_once($value['class_path']);
							if (class_exists($value['class']))
							{
								if (method_exists($value['class'], $value['method']))
								{
									$sql = " SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$_GET[sample_id]."";
									$pdf = $value['class']::$value['method']($sql, $sample->get_item_id(), $pdf);
								}
							}
						}
					}
				}
				
				return $pdf;
			}
			else
			{
				// Error
			}
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * @return object
	 */
	public static function get_barcode_report()
	{
		if (class_exists("TCPDF"))
		{
			if ($_GET[sample_id])
			{		
				$sample_id = $_GET[sample_id];
				
				if ($_GET[paper_size])
				{
					$paper_size_info_array = PaperSize::get_size_by_id($_GET[paper_size]);
				}
				else
				{
					$paper_size_info_array = PaperSize::get_standard_size();
				}
				
				$format = Array($paper_size_info_array['width'], $paper_size_info_array['height']);
				
				if ($paper_size_info_array['width'] >= $paper_size_info_array['height'])
				{
					$orientation = "L";
				}
				else
				{
					$orientation = "P";
				}
				
				$pdf = new TCPDF($orientation, "mm", $format, true, 'UTF-8', false);
	
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('Open-LIMS');
				$pdf->SetTitle('Sample Report');
				
				$pdf->setPrintHeader(false);
				$pdf->setPrintFooter(false);
								
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				
				$pdf->SetMargins($paper_size_info_array['margin_left'], $paper_size_info_array['margin_top'], $paper_size_info_array['margin_right']);
				
				$pdf->SetAutoPageBreak(TRUE, $paper_size_info_array['margin_bottom']);
				
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				
				$pdf->setLanguageArray($l);
								
				$pdf->AddPage();
				
				$page_width = $paper_size_info_array['width']-$paper_size_info_array['margin_left']-$paper_size_info_array['margin_right'];
				$page_height = $paper_size_info_array['height']-$paper_size_info_array['margin_top'];
				
				$font_size = $page_height*0.1;
				
				if (($page_width*0.6) > $page_height)
				{
					$barcode_height = $page_height-$font_size;
					$barcode_width = null;
				}
				else
				{
					$barcode_height = $page_width * 0.6;
					$barcode_width = $page_width;
				}
				
				$print_sample_id = "S".str_pad($sample_id, 8 ,'0', STR_PAD_LEFT);
				
				$style = array(
				    'position' => '',
				    'align' => 'C',
				    'stretch' => true,
				    'fitwidth' => false,
				    'cellfitalign' => '',
				    'border' => false,
				    'hpadding' => 'auto',
				    'vpadding' => 'auto',
				    'fgcolor' => array(0,0,0),
				    'bgcolor' => false, //array(255,255,255),
				    'text' => true,
				    'font' => 'helvetica',
				    'fontsize' => $font_size,
				    'stretchtext' => 4
				);
				
				$pdf->write1DBarcode($print_sample_id, 'C128B', '', '', $barcode_width, $barcode_height, 0.2, $style, 'M');
			
				return $pdf;
			}
			else
			{
				// Error
			}
		}
		else
		{
			// Error
		}
	}
	
	/**
	 * @param string $sql
	 * @param integer $item_id
	 * @param object $pdf
	 * @return object
	 */
	public static function get_sample_item_report($sql, $item_id, $pdf)
	{
		if ($sql and is_object($pdf))
		{
			$new_page = false;
			
			$child_sample_array = Sample_Wrapper::list_item_samples($sql, null, null, null, null);
			
			if (is_array($child_sample_array) and count($child_sample_array) >= 1)
			{
				$pdf->addPage();
				$new_page = true;
				
				$pdf->SetFont('dejavusans', 'B', 14, '', true);
				
				$pdf->Write(0, 'Samples', '', 0, 'C', true, 0, false, false, 0);
				$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
				
				$pdf->MultiCell(35, 0, "ID", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(60, 0, "Name", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(50, 0, "Date/Time", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
				$pdf->MultiCell(45, 0, "User", 1, 'L', 1, 1, '', '', true, 0, false, true, 0);

				$pdf->SetFont('dejavusans', '', 14, '', true);
				
				foreach($child_sample_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($value[datetime]);
					$value[datetime] = $datetime_handler->get_formatted_string("dS M y H:i");
					$value[id]		= "S".str_pad($value[id], 8 ,'0', STR_PAD_LEFT);
					$owner = new User($value[owner]);
					
					$pdf->MultiCell(35, 0, $value[id], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
					$pdf->MultiCell(60, 0, $value[name], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
					$pdf->MultiCell(50, 0, $value[datetime], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
					$pdf->MultiCell(45, 0, $owner->get_full_name(true), 1, 'L', 1, 1, '', '', true, 0, true, true, 0);
				}
			}
			
			if ($item_id)
			{
				$parent_sample_array = Sample_Wrapper::list_samples_by_item_id($item_id, null, null, null, null);
				
				if (is_array($parent_sample_array) and count($parent_sample_array) >= 1)
				{
					if ($new_page == false)
					{
						$pdf->addPage();
					}
					else
					{
						$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
						$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
					}
					
					$pdf->SetFont('dejavusans', 'B', 14, '', true);
					
					$pdf->Write(0, 'Parent Samples', '', 0, 'C', true, 0, false, false, 0);
					$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
					
					$pdf->MultiCell(35, 0, "ID", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(60, 0, "Name", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(50, 0, "Date/Time", 1, 'L', 1, 0, '', '', true, 0, false, true, 0);
					$pdf->MultiCell(45, 0, "User", 1, 'L', 1, 1, '', '', true, 0, false, true, 0);
	
					$pdf->SetFont('dejavusans', '', 14, '', true);
					
					foreach($parent_sample_array as $key => $value)
					{
						$datetime_handler = new DatetimeHandler($value[datetime]);
						$value[datetime] = $datetime_handler->get_formatted_string("dS M y H:i");
						$value[id]		= "S".str_pad($value[id], 8 ,'0', STR_PAD_LEFT);
						$owner = new User($value[owner]);
						
						$pdf->MultiCell(35, 0, $value[id], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
						$pdf->MultiCell(60, 0, $value[name], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
						$pdf->MultiCell(50, 0, $value[datetime], 1, 'L', 1, 0, '', '', true, 0, true, true, 0);
						$pdf->MultiCell(45, 0, $owner->get_full_name(true), 1, 'L', 1, 1, '', '', true, 0, true, true, 0);
					}
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