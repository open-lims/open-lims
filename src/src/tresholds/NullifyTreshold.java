package tresholds;

import tresholds.interfaces.Treshold;

public class NullifyTreshold implements Treshold {

	@Override
	public boolean check_field(String field, int column_index) 
	{
		if(field.equals("\"emp\""))
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
