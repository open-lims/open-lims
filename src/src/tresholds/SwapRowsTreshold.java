package tresholds;

import java.io.BufferedReader;
import java.util.HashMap;

import csv.CSVReader;
import data.DataResource;
import tresholds.interfaces.Treshold;

/**
 * Treshold implementation that replaces an entire row where a treshold value was found with a row from a set of associated files.
 * The first valid row found in the associated files will be used.
 * @author Roman Quiring
 */
public class SwapRowsTreshold implements Treshold {

	private String treshold;
	
	private int[] associated_files = null;
	private int current_associated_file_index = 0;
	
	private CSVReader reader =  null;
	private HashMap<Integer,CSVReader> open_readers = new HashMap<Integer,CSVReader>();
	
	/**
	 * Constructor
	 * @param associated_files an Array of associated file ids, ordered by priority.
	 * @param treshold the value to look for.
	 */
	public SwapRowsTreshold(int[] associated_files, String treshold, String delimiter, String line_break, String data_begin_tag, String data_end_tag)
	{
		this.treshold = treshold;
		this.associated_files = associated_files;
		for (int i = 0; i < associated_files.length; i++) 
		{
			BufferedReader br = DataResource.getResourceFromFileIdAsStream(associated_files[i]);
			CSVReader r =  new CSVReader(br, delimiter, line_break, data_begin_tag, data_end_tag);
			
			open_readers.put(associated_files[i],r);
		}
		
		reader = open_readers.get(associated_files[0]);
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
//		System.out.println("applying changes ("+row_index+" - "+column_index+") checking file id "+associated_files[current_associated_file_index]);
		
		String[] row_to_switch = reader.read_line(row_index);

		//check ALL columns! replace row if one of the columns is invalid
		for (int i = 0; i < row_to_switch.length; i++) 
		{
			if(check_field(row_to_switch[i], i))
			{ //threshold reached in new row as well
//				System.out.println("treshold ("+row_index+" - "+column_index+") reached again in file "+associated_files[current_associated_file_index]+" ("+i+")");
								
				//check next file if possible
				if(current_associated_file_index +1 < associated_files.length)
				{
					//set reader to next associated file
					current_associated_file_index++;
					reader = open_readers.get(associated_files[current_associated_file_index]);
					
					//check next associated file recursively
					row_to_switch = apply_changes(row, column_index, row_index);
					
					//set reader back to current associated file
					current_associated_file_index--;
					reader = open_readers.get(associated_files[current_associated_file_index]);
				}
				else
				{ //indicate failure if no more files
					System.err.println("failed to replace ("+row_index+" - "+column_index+") with all associated files!");
					row_to_switch = row;
				}
				break;
			}
		}
		return row_to_switch;
	}

	public void destroy()
	{
		for (CSVReader reader : open_readers.values()) 
		{
			reader.close();
		}
	}

}
