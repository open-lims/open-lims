<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * DB Transaction Management Interface
 * @package base
 */
interface TransactionInterface
{
	function __construct();
	
	function __destruct();
	
	/**
     * Starts a new transaction
     * @return string
     */
	public function begin();
	
	/**
     * Commits a transaction
     * @param string $unique_id
     * @return bool
     */
	public function commit($unique_id);
	
	/**
     * Undo all DB-Changes since begin
     * @param string $unique_id
     * @return bool
     */
	public function rollback($unique_id);
	
	/**
     * Undo all DB-Changes since begin (expected in difference to rollback())
     * @param string $unique_id
     * @return bool
     */
	public function expected_rollback($unique_id);
	
	/**
     * Rollbacks current transaction during an database error
     * @return bool
     */
	public function force_rollback();
	
	/**
	 * Checks if a transaction is active
     * @return bool
     */
    public function is_in_transction();
}
?>