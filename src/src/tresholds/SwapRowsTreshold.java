package tresholds;

import java.io.BufferedReader;
import java.util.HashMap;
import java.util.LinkedList;

import csv.CSVReader;
import data.DataResource;
import tresholds.interfaces.Treshold;

public class SwapRowsTreshold implements Treshold {

	private int current_associated_file_index = 0;
	private HashMap<Integer,CSVReader> open_readers = new HashMap<Integer,CSVReader>();
	
	private int[] associated_files = null;
	
	private BufferedReader br = null;
	private CSVReader reader =  null;
	
	
	public SwapRowsTreshold(int[] associated_files)
	{
		this.associated_files = associated_files;

		br = DataResource.getResourceFromFileIdAsStream(associated_files[current_associated_file_index]);
		reader =  new CSVReader(br, ",","\r\n");
		
		open_readers.put(associated_files[current_associated_file_index],reader);
	}

	public boolean check_field(String field, int column_index) 
	{
		try
		{
			int num = Integer.parseInt(field);
			if(num == 65535)
			{
				return true;
			}
		}
		catch(Exception e){}
		return false;
	}

	public String[] apply_changes(String[] row, int column_index, int row_index) 
	{
		int current_associated_file_index_backup = current_associated_file_index;

		String[] row_to_switch = reader.readLine(row_index);
		
//		boolean recursive_call_occurred = false;
		
		for (int i = 0; i < row_to_switch.length; i++) 
		{
			if(check_field(row_to_switch[i], i))
			{
				if(current_associated_file_index < associated_files.length -1)
				{
					current_associated_file_index++;
					
					CSVReader r = open_readers.get(associated_files[current_associated_file_index]);
					if(r == null)
					{
						br = DataResource.getResourceFromFileIdAsStream(associated_files[current_associated_file_index]);
						r =  new CSVReader(br, ",","\r\n");
						open_readers.put(associated_files[current_associated_file_index], r);
						reader = r;
					}
					
//					if(current_associated_file_index == current_associated_file_index_backup) 
//					{
						System.out.println(current_associated_file_index+"swapping row "+row_index+" (treshold reached in column "+column_index+") - reading file "+associated_files[current_associated_file_index]);
//					}
					

					
					row_to_switch = apply_changes(row, column_index, row_index);
					
					recursive_call_occurred = true;
					
				}
				else
				{
					if(recursive_call_occurred) 
					{
						System.out.println(current_associated_file_index+"correct value for row "+row_index+", column "+column_index+" could not be found in associated files!");
					}
				}
			}
		}
		
		current_associated_file_index = current_associated_file_index_backup;

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
