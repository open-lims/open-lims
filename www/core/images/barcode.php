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
 
	$charset = array();
	
	$charset[' '] 		= "11011001100";
	$charset['!'] 		= "11001101100";
	$charset['"'] 		= "11001100110";
	$charset['#'] 		= "10010011000";
	$charset['$'] 		= "10010001100";
	$charset['%'] 		= "10001001100";
	$charset['&'] 		= "10011001000";
	$charset['\''] 		= "10011000100";
	$charset['('] 		= "10001100100";
	$charset[')'] 		= "11001001000";
	$charset['*'] 		= "11001000100";
	$charset['+'] 		= "11000100100";
	$charset[','] 		= "10110011100";
	$charset['-'] 		= "10011011100";
	$charset['.'] 		= "10011001110";
	$charset['/'] 		= "10111001100";
	$charset['0'] 		= "10011101100";
	$charset['1'] 		= "10011100110";
	$charset['2'] 		= "11001110010";
	$charset['3'] 		= "11001011100";
	$charset['4'] 		= "11001001110";
	$charset['5'] 		= "11011100100";
	$charset['6'] 		= "11001110100";
	$charset['7'] 		= "11101101110";
	$charset['8'] 		= "11101001100";
	$charset['9'] 		= "11100101100";
	$charset[':'] 		= "11100100110";
	$charset[';'] 		= "11101100100";
	$charset['<'] 		= "11100110100";
	$charset['='] 		= "11100110010";
	$charset['>'] 		= "11011011000";
	$charset['?'] 		= "11011000110";
	$charset['@'] 		= "11000110110";
	$charset['A'] 		= "10100011000";
	$charset['B'] 		= "10001011000";
	$charset['C'] 		= "10001000110";
	$charset['D'] 		= "10110001000";
	$charset['E'] 		= "10001101000";
	$charset['F'] 		= "10001100010";
	$charset['G'] 		= "11010001000";
	$charset['H'] 		= "11000101000";
	$charset['I'] 		= "11000100010";
	$charset['J'] 		= "10110111000";
	$charset['K'] 		= "10110001110";
	$charset['L'] 		= "10001101110";
	$charset['M'] 		= "10111011000";
	$charset['N'] 		= "10111000110";
	$charset['O'] 		= "10001110110";
	$charset['P'] 		= "11101110110";
	$charset['Q'] 		= "11010001110";
	$charset['R'] 		= "11000101110";
	$charset['S'] 		= "11011101000";
	$charset['T'] 		= "11011100010";
	$charset['U'] 		= "11011101110";
	$charset['V'] 		= "11101011000";
	$charset['W'] 		= "11101000110";
	$charset['X'] 		= "11100010110";
	$charset['Y'] 		= "11101101000";
	$charset['Z'] 		= "11101100010";
	$charset['['] 		= "11100011010";
	$charset['\\'] 		= "11101111010";
	$charset[']'] 		= "11001000010";
	$charset['^'] 		= "11110001010";
	$charset['_'] 		= "10100110000";
	$charset['`'] 		= "10100001100";
	$charset['a'] 		= "10010110000";
	$charset['b'] 		= "10010000110";
	$charset['c'] 		= "10000101100";
	$charset['d'] 		= "10000100110";
	$charset['e'] 		= "10110010000";
	$charset['f'] 		= "10110000100";
	$charset['g'] 		= "10011010000";
	$charset['h'] 		= "10011000010";
	$charset['i'] 		= "10000110100";
	$charset['j'] 		= "10000110010";
	$charset['k'] 		= "11000010010";
	$charset['l'] 		= "11001010000";
	$charset['m'] 		= "11110111010";
	$charset['n'] 		= "11000010100";
	$charset['o'] 		= "10001111010";
	$charset['p'] 		= "10100111100";
	$charset['q'] 		= "10010111100";
	$charset['r'] 		= "10010011110";
	$charset['s'] 		= "10111100100";
	$charset['t'] 		= "10011110100";
	$charset['u'] 		= "10011110010";
	$charset['v'] 		= "11110100100";
	$charset['w'] 		= "11110010100";
	$charset['x'] 		= "11110010010";
	$charset['y'] 		= "11011011110";
	$charset['z'] 		= "11011110110";
	$charset['{'] 		= "11110110110";
	$charset['|'] 		= "10101111000";
	$charset['}'] 		= "10100011110";
	$charset['~'] 		= "10001011110";
	
	$function['start'] 	= "11010010000";
	$function['end']	= "1100011101011";


	$value[' '] 		= 0;
	$value['!'] 		= 1;
	$value['"'] 		= 2;
	$value['#'] 		= 3;
	$value['$'] 		= 4;
	$value['%'] 		= 5;
	$value['&'] 		= 6;
	$value['\''] 		= 7;
	$value['('] 		= 8;
	$value[')'] 		= 9;
	$value['*'] 		= 10;
	$value['+'] 		= 11;
	$value[','] 		= 12;
	$value['-'] 		= 13;
	$value['.'] 		= 14;
	$value['/'] 		= 15;
	$value['0'] 		= 16;
	$value['1'] 		= 17;
	$value['2'] 		= 18;
	$value['3'] 		= 19;
	$value['4'] 		= 20;
	$value['5'] 		= 21;
	$value['6'] 		= 22;
	$value['7'] 		= 23;
	$value['8'] 		= 24;
	$value['9'] 		= 25;
	$value[':'] 		= 26;
	$value[';'] 		= 27;
	$value['<'] 		= 28;
	$value['='] 		= 29;
	$value['>'] 		= 30;
	$value['?'] 		= 31;
	$value['@'] 		= 32;
	$value['A'] 		= 33;
	$value['B'] 		= 34;
	$value['C'] 		= 35;
	$value['D'] 		= 36;
	$value['E'] 		= 37;
	$value['F'] 		= 38;
	$value['G'] 		= 39;
	$value['H'] 		= 40;
	$value['I'] 		= 41;
	$value['J'] 		= 42;
	$value['K'] 		= 43;
	$value['L'] 		= 44;
	$value['M'] 		= 45;
	$value['N'] 		= 46;
	$value['O'] 		= 47;
	$value['P'] 		= 48;
	$value['Q'] 		= 49;
	$value['R'] 		= 50;
	$value['S'] 		= 51;
	$value['T'] 		= 52;
	$value['U'] 		= 53;
	$value['V'] 		= 54;
	$value['W'] 		= 55;
	$value['X'] 		= 56;
	$value['Y'] 		= 57;
	$value['Z'] 		= 58;
	$value['['] 		= 59;
	$value['\\'] 		= 60;
	$value[']'] 		= 61;
	$value['^'] 		= 62;
	$value['_'] 		= 63;
	$value['`'] 		= 64;
	$value['a'] 		= 65;
	$value['b'] 		= 66;
	$value['c'] 		= 67;
	$value['d'] 		= 68;
	$value['e'] 		= 69;
	$value['f'] 		= 70;
	$value['g'] 		= 71;
	$value['h'] 		= 72;
	$value['i'] 		= 73;
	$value['j'] 		= 74;
	$value['k'] 		= 75;
	$value['l'] 		= 76;
	$value['m'] 		= 77;
	$value['n'] 		= 78;
	$value['o'] 		= 79;
	$value['p'] 		= 80;
	$value['q'] 		= 81;
	$value['r'] 		= 82;
	$value['s'] 		= 83;
	$value['t'] 		= 84;
	$value['u'] 		= 85;
	$value['v'] 		= 86;
	$value['w'] 		= 87;
	$value['x'] 		= 88;
	$value['y'] 		= 89;
	$value['z'] 		= 90;
	$value['{'] 		= 91;
	$value['|'] 		= 92;
	$value['}'] 		= 93;
	$value['~'] 		= 94;

	if ($_GET['string'])
	{
		$string = $_GET['string'];
		
		$length = strlen($string);
		
		$barcode = $function['start'];
		
		$checksum = 104;
		
		for($i=0;$i<=$length-1;$i++)
		{
			$barcode .= $charset[$string{$i}];			
			$checksum = $checksum + (($i+1) * $value[$string{$i}]);
		}
		
		$checksum = $checksum % 103;
		
		foreach ($value as $key => $value)
		{
			if ($value == $checksum)
			{
				$checksum_key = $key;
			}
		}
		
		$barcode .= $charset[$checksum_key];
		
		$barcode .= $function['end'];
				
		header("Content-Type: image/png");
		
		$barcode_length = strlen($barcode);
		
		$bar_width = 2;

		$image_length = $bar_width*$barcode_length;
		$image_height = 100;
		
		$image 			= imagecreatetruecolor($image_length, $image_height);
		
		$background 	= imagecolorallocate($image, 255, 255, 255);
		
		$black 			= imagecolorallocate($image, 0, 0, 0);
		$white 			= imagecolorallocate($image, 255, 255, 255);
		
		imagefill($image, 0, 0, $background);

		for ($i=0;$i<=$barcode_length-1;$i++)
		{
			if ($barcode{$i} == "0")
			{
				imagefilledrectangle($image, ($i*$bar_width)-($bar_width-1)+1, 0, $i*$bar_width+1, $image_height, $white);
			}
			else
			{
				imagefilledrectangle($image, ($i*$bar_width)-($bar_width-1)+1, 0, $i*$bar_width+1, $image_height, $black);
			}
			
		}
		imagepng($image);
		imagedestroy($image);
	} 

?>
