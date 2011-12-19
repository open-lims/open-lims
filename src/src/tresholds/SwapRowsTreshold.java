package tresholds;

import java.io.BufferedReader;
import java.util.LinkedList;

import csv.CSVReader;
import data.DataResource;
import tresholds.interfaces.Treshold;

public class SwapRowsTreshold implements Treshold {

	private int current_associated_file_index = 0;
	private LinkedList<CSVReader> open_readers = new LinkedList<CSVReader>();
	
	private int[] associated_files = null;
	
	private BufferedReader br = null;
	private CSVReader reader =  null;
	
	
	public SwapRowsTreshold(int[] associated_files)
	{
		this.associated_files = associated_files;

		br = DataResource.getResourceFromFileIdAsStream(associated_files[current_associated_file_index]);
		reader =  new CSVReader(br, ",","\r\n");
		
		open_readers.add(reader);
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

		String[] row_to_switch = reader.readLine(row_index);
		
		for (int i = 0; i < row_to_switch.length; i++) 
		{
			if(check_field(row_to_switch[i], i))
			{
				System.out.println("correct value could not be found in reader "+current_associated_file_index);
				
				
				if(current_associated_file_index < associated_files.length -1)
				{
					current_associated_file_index++;
					br = DataResource.getResourceFromFileIdAsStream(associated_files[current_associated_file_index]);
					reader =  new CSVReader(br, ",","\r\n");
					row_to_switch = apply_changes(row, column_index, row_index);
				}
			}
		}
		
		row_to_switch[row_to_switch.length-1] = "THIS ENTIRE ROW HAS BEEN CHANGED";
		return row_to_switch;
	}
	
	public void destroy() 
	{
		for (CSVReader reader : open_readers) 
		{
			reader.close();
		}
	}
}
