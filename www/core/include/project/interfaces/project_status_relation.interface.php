<?php
/**
 * @package project
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
 * Project Status Relation Interface
 * @package project
 */ 	
interface ProjectStatusRelationInterface
{
	/**
	 * @param integer $project_id
	 * @param integer $status_id
	 */
    function __construct($project_id, $status_id);
    
    /**
     * Checks, if the class-defined status is lesser than the method one
     * @param integer $status_id
     * @return bool
     */
    public function is_less($status_id);
    
    /**
     * Checks, if the class-defined status is bigger than the method one
     * @param integer $status_id
     * @return bool
     */
    public function is_more($status_id);
    
    /**
     * Returns the next status of the given project
     * @return integer
     */
 	public function get_next();
 	
 	/**
     * Returns the previous status of the given project
     * @return integer
     */
 	public function get_previous();
}
?>
