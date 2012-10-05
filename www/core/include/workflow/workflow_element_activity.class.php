<?php
/**
 * @package workflow
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
// require_once("interfaces/workflow.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{

}

/**
 * Workflow Element Status Class
 * @package workflow
 */
class WorkflowElementActivity extends WorkflowElement // implements WorkflowInterface
{
	private $id;
	
	function __construct($id)
	{
		parent::__construct();
		$this->id = $id;
	}
	
	public function get_id()
	{
		return $this->id;
	}
}
?>