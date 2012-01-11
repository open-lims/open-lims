package tresholds;

import tresholds.interfaces.Treshold;

/**
 * Treshold implementation that replaces a threshold String with NULL.
 * @author Roman Quiring
 */
public class NullifyTreshold implements Treshold {

	private String treshold;
	
	/**
	 * Constructor
	 * @param treshold the String to be replaced by NULL.
	 */
	public NullifyTreshold(String treshold) 
	{
		this.treshold = treshold;
	}
	
	public boolean check_field(String field, int column_index) 
	{
		if(field.equals(treshold))
		{
			return true;
		}
		return false;
	}

	public String[] apply_changes(String[] row, int column_index, int row_index) 
	{
		row[column_index] = "NULL";
		return row;
	}

	public void destroy() {}

}
