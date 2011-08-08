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
 * Default PDF Class for Sample Reports
 * @package sample
 * @ignore
 */
class SamplePDF extends TCPDF
{
	private $sample_id;
	private $sample_name;
	
	/**
	 * @param integer $sample_id
	 * @param string $sample_name
	 * @param string $orientation
	 * @param string $unit
	 * @param string $format
	 * @param bool $unicode
	 * @param string $encoding
	 */
	function __construct($sample_id, $sample_name, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = "UTF-8")
	{
		$this->sample_id = $sample_id;
		$this->sample_name = $sample_name;
		parent::__construct($orientation, $unit, $format, $unicode, $encoding);
	}
	
    public function Header()
    {
    	$print_sample_id = "S".str_pad($this->sample_id, 8 ,'0', STR_PAD_LEFT);
    	$headline = "Sample Report: ".$this->sample_name;
    	
    	$style = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => '',
		    'border' => false,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 8,
		    'stretchtext' => 4
		);
    	
        $this->write1DBarcode($print_sample_id, 'C128B', '', '', '', 20, 0.2, $style, 'M');
        $this->SetFont('helvetica', 'B', 20);

        $this->Cell(0, 15, $headline, 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 15, "", "B", 1, 'C', 0, '', 0, false, 'M', 'M');
    }
    
	public function Footer()
	{
        $this->SetFont('helvetica', '', 10);
        parent::Footer();
        $this->Cell(0, 5, "", 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 8, "Report generated with Open-LIMS (".date("dS M Y H:i").")", 0, 1, 'L', 0, '', 0, false, 'M', 'M');
    }
}
?>