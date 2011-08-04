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
 * Common IO Class
 * @package base
 */
class Common_IO
{	
	private static $in_container = false;
	
	public static function get_in_container()
	{
		return self::$in_container;
	}
	
	public static function container_begin($title, $class)
	{
		self::$in_container = true;
		if (!$class)
		{
			$class = "boxRound";
		}

		return "<div class='".$class."'>" .
				"<table class='".$class."Begin'>" .
				"<tr>" .
				"<td class='".$class."BeginLeft'><img src='images/corners/corner_top_left.png' alt='' /></td>" .
				"<td class='".$class."BeginTitle'>".$title."</td>" .
				"<td class='".$class."BeginRight'><img src='images/corners/corner_top_right.png' alt='' /></td>" .
				"</tr>" .
				"</table>" .
				"<div class='".$class."Main'>";
	}

	public static function container_end($class)
	{
		self::$in_container = false;
		if (!$class)
		{
			$class = "boxRound";
		}

		return "</div>" .
				"<table class='".$class."End'>" .
				"<tr>" .
				"<td class='".$class."EndLeft'><img src='images/corners/corner_bottom_left.png' alt='' /></td>" .
				"<td class='".$class."EndBorder'>&nbsp;</td>" .
				"<td class='".$class."EndRight'><img src='images/corners/corner_bottom_right.png' alt='' /></td>" .
				"</tr>" .
				"</table></div>";
	}
		
	public static function page_bar($page, $max_page, $paramquery) {
		
			$previous_paramquery = $paramquery;
			$previous_paramquery[page] = $page-1;
			unset($previous_paramquery[show]);
			$prevLink = http_build_query($previous_paramquery,'','&#38;');
		
			$next_paramquery = $paramquery;
			$next_paramquery[page] = $page+1;
			unset($next_paramquery[show]);
			$nextLink = http_build_query($next_paramquery,'','&#38;');
		
			$return = "<div class='ResultNextPageBar'>";

			$return .= "<table style='display: inline;'><tr><td><span class='smallTextBlack'>Page ".$page." of ".$max_page."</span></td>";

			// Previous
			if ($page == 1) {
				$return .= "<td><img src='images/icons/previous_d.png' alt='Previous' border='0' /></td>";		
			}else{
				$return .= "<td><a href='index.php?".$prevLink."'><img src='images/icons/previous.png' alt='Previous' border='0' /></a></td>";
				
			}	
			
				$displayed = false;
							
				for ($i=1;$i<=$max_page;$i++) {
					
					$page_paramquery = $paramquery;
					$page_paramquery[page] = $i;
					$pageLink = http_build_query($page_paramquery,'','&#38;');
					
					$display = false;
					
					if ($max_page < 5) {

						$display = true;
					
					}else{

						if ($i <= 2) {
							$display = true;
						}
						
						if ($i > $max_page-2) {
							$display = true;
						}
						
						if ($display == false and $page+1 == $i) {
							$display = true;
						}
						
						if ($display == false and $page-1 == $i) {
							$display = true;
						}
						
						if ($display == false and $page == $i) {
							$display = true;
						}
						if ($i == $page+10 and $display == false) {
							$display = true;
						}
						
						if ($i == $page-10 and $display == false) {
							$display = true;
						}
	
					}
					
					if ($display == true) {
						if ($page == $i) {
							$return .= "<td><span class='bold'><a href='index.php?".$pageLink."'>".$i."</a></span></td>";	
						}else{
							$return .= "<td><a href='index.php?".$pageLink."'>".$i."</a></td>";	
						}						
						$displayed = true;
					}elseif ($displayed == true) {
						$return .= "<td>..</td>";
					}
					
					if ($display == false) {
						$displayed = false;
					}

				}

			// Next
			if($page == $max_page){
				$return .= "<td><img src='images/icons/next_d.png' alt='Next' border='0' /></td>";		
			}else{
				$return .= "<td><a href='index.php?".$nextLink."'><img src='images/icons/next.png' alt='Next' border='0' /></a></td>";	
			}
			
			$return .= "</tr></table>";
			
			$return .= "</div>";
		
			return $return;
		
	}
	
	public static function results_on_page($results, $pages) {
		
		if ($results > 1) {

			if ($pages > 1) {
				return $results." Results on ".$pages." Pages";
			}else{
				return $results." Results on ".$pages." Page";
			}
			
		}else{
			
			if ($results == 0) {
				return "0 Results on 1 Page";
			}else{
				return $results." Result on 1 Page";
			}
			
		}
		
	}
	
	public static function step_proceed($target, $title, $text, $css_class)
	{
		$template = new Template("template/base/step_proceed.html");
		
		$template->set_var("target",$target);
		$template->set_var("title",$title);
		$template->set_var("text",$text);
		
		if ($css_class == null)
		{
			$template->set_var("css_class","boxRound");
		}
		else
		{
			$template->set_var("css_class","\"".$css_class."\"");
		}

		$template->output();
	}
	
}

?>
