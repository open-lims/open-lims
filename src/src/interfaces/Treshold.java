package interfaces;

/**
 * Interface for implementing tresholds.
 * Looks at a field of a csv table and applies changes to the field or the entire row if necessary.
 * @author Roman Quiring
 */
public interface Treshold {

	/**
	 * Looks at a field and determines whether it applies to a defined rule. 
	 * @param field a string containing the value of the field to check against.
	 * @return false if the value is within the threshold limits, true if the threshold has been reached.
	 */
	public boolean check_field(String field, int column_index);
	
	/**
	 * Applies all operations implicated by the threshold.
	 * @param row a String array containing the entire current row.
	 * @param column_index the column index in the row array where the threshold has been reached.
	 * @return a new String array, representing the row and all applied changes.
	 */
	public String[] apply_changes(String[] row, int column_index, int row_index);
	
	/**
	 * Destroys all treshold-specific assets.
	 */
	public void destroy();
}
