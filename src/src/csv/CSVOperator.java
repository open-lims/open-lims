package csv;

import java.util.HashMap;
import java.util.List;

import csv.interfaces.Treshold;

public class CSVOperator {

	public void write_plain_csv(CSVReader csv_reader, CSVWriter csv_writer)
	{
		String[] columns;
		while((columns = csv_reader.readLine()) != null)
		{
			String row = "";
			for (int i = 0; i < columns.length; i++) {
				row += columns[i];
				if(i < columns.length-1)
				{
					row += csv_writer.get_delimiter();
				}
			}
			row += csv_writer.get_line_break();
			csv_writer.writeLine(row);
		}
		csv_writer.close();
	}
	
	public void rewrite_csv_in_new_order(CSVReader csv_reader, CSVWriter csv_writer, String[] order)
	{
		rewrite(csv_reader, csv_writer, order, null, null);
	}
	
	public void rewrite_csv_in_new_order(CSVReader csv_reader, CSVWriter csv_writer, Integer[] order)
	{
		rewrite(csv_reader, csv_writer, order, null, null);
	}
	
	public void rewrite_csv_with_deletes(CSVReader csv_reader, CSVWriter csv_writer, String[] deletes)
	{	
		rewrite(csv_reader, csv_writer, null, deletes, null);
	}
	
	public void rewrite_csv_with_deletes(CSVReader csv_reader, CSVWriter csv_writer, Integer[] deletes)
	{	
		rewrite(csv_reader, csv_writer, null, deletes, null);
	}
	
	public void rewrite_csv_with_new_order_and_deletes(CSVReader csv_reader, CSVWriter csv_writer, String[] order, String[] deletes)
	{
		rewrite(csv_reader, csv_writer, order, deletes, null);
	}
	
	public void rewrite_csv_with_new_order_and_deletes(CSVReader csv_reader, CSVWriter csv_writer, Integer[] order, Integer[] deletes)
	{
		rewrite(csv_reader, csv_writer, order, deletes, null);
	}
	
	public void rewrite_csv_with_tresholds(CSVReader csv_reader, CSVWriter csv_writer, String[] order, String[] deletes, List<Treshold> tresholds)
	{
		rewrite(csv_reader, csv_writer, order, deletes, tresholds);
	}
	
	public void rewrite_csv_with_tresholds(CSVReader csv_reader, CSVWriter csv_writer, Integer[] order, Integer[] deletes, List<Treshold> tresholds)
	{
		rewrite(csv_reader, csv_writer, order, deletes, tresholds);
	}
	
	private void rewrite(CSVReader csv_reader, CSVWriter csv_writer, String[] order, String[] deletes, List<Treshold> tresholds)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = get_real_indices_to_desired_column_indices(csv_reader, csv_writer, order, deletes);
		
		rewrite_header(csv_reader, csv_writer, real_indices_to_desired_column_indices);
		
		rewrite_rows(csv_reader, csv_writer, real_indices_to_desired_column_indices, tresholds);
		
		csv_writer.close();
	}
	
	private void rewrite(CSVReader csv_reader, CSVWriter csv_writer, Integer[] order, Integer[] deletes, List<Treshold> tresholds)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = get_real_indices_to_desired_column_indices(csv_reader, csv_writer, order, deletes);
		
		rewrite_header(csv_reader, csv_writer, real_indices_to_desired_column_indices);
		
		rewrite_rows(csv_reader, csv_writer, real_indices_to_desired_column_indices, tresholds);
		
		csv_writer.close();
	}
	
	private HashMap<Integer, Integer> get_real_indices_to_desired_column_indices(CSVReader reader, CSVWriter writer, String[] order, String[] deletes)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = null;
		
		String[] header = reader.get_header();
		
		int new_column_count = header.length;
		
		if(order == null && deletes == null)
		{
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(header.length);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
		}
		else if(order == null)
		{
			new_column_count = header.length - deletes.length;
			
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(new_column_count);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) 
			{
				int original_index = reader.get_column_index(deletes[i]);
				real_indices_to_desired_column_indices.put(original_index, -1);
			}
		}
		else if(deletes == null)
		{
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(order.length);
			
			for (int i = 0; i < order.length; i++) 
			{
				int original_index = reader.get_column_index(order[i]);
				real_indices_to_desired_column_indices.put(original_index, i);
			}
		}
		else
		{
			new_column_count = header.length - deletes.length;
			
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(new_column_count);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) 
			{
				int original_index = reader.get_column_index(deletes[i]);
				real_indices_to_desired_column_indices.put(original_index, -1);
			}
			
			for (int i = 0; i < order.length; i++) 
			{
				int original_index = reader.get_column_index(order[i]);
				real_indices_to_desired_column_indices.put(original_index, i);
			}
		}
		writer.set_new_column_count(new_column_count);
		return real_indices_to_desired_column_indices;
	}
	
	private HashMap<Integer, Integer> get_real_indices_to_desired_column_indices(CSVReader reader, CSVWriter writer, Integer[] order, Integer[] deletes)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = null;
		
		String[] header = reader.get_header();
		
		int new_column_count = header.length;
		
		if(order == null && deletes == null)
		{
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(header.length);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
		}
		else if(order == null)
		{
			new_column_count = header.length - deletes.length;
			
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(new_column_count);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) 
			{
				real_indices_to_desired_column_indices.put(deletes[i], -1);
			}
		}
		else if(deletes == null)
		{
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(order.length);
			
			for (int i = 0; i < order.length; i++) 
			{
				real_indices_to_desired_column_indices.put(order[i], i);
			}
		}
		else
		{
			new_column_count = header.length - deletes.length;
			
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(new_column_count);
			for (int i = 0; i < header.length; i++) 
			{
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) 
			{
				real_indices_to_desired_column_indices.put(deletes[i], -1);
			}
			
			for (int i = 0; i < order.length; i++) 
			{
				real_indices_to_desired_column_indices.put(order[i], i);
			}
		}
		writer.set_new_column_count(new_column_count);
		return real_indices_to_desired_column_indices;
	}
	
	private void rewrite_header(CSVReader reader, CSVWriter writer, HashMap<Integer, Integer> real_indices_to_desired_column_indices)
	{
		int new_column_count = writer.get_new_column_count();
		String[] header = reader.get_header();
		String[] new_header = new String[new_column_count];
		
		for (int i = 0; i < header.length; i++) 
		{	
			int desired_index = real_indices_to_desired_column_indices.get(i);
			if(desired_index != -1)
			{
				new_header[desired_index] = header[i];
			}
		}
		
		String header_row = "";
		for (int i = 0; i < new_header.length; i++) 
		{
			if(i < new_header.length - 1)
			{
				header_row += new_header[i] + writer.get_delimiter();
			}
			else
			{
				header_row += new_header[i];
			}
		}
		header_row += writer.get_line_break();
		writer.writeLine(header_row);
	}
	
	private void rewrite_rows(CSVReader reader, CSVWriter writer, HashMap<Integer, Integer> real_indices_to_desired_column_indices, List<Treshold> tresholds)
	{
		String[] columns;
		String[] new_columns;
		int new_column_count = writer.get_new_column_count();
		while((columns = reader.readLine()) != null)
		{
			new_columns = new String[new_column_count];
			for (int i = 0; i < columns.length; i++) 
			{
				int desired_index = real_indices_to_desired_column_indices.get(i);
				if(desired_index != -1)
				{
					new_columns[desired_index] = columns[i];
				}
			}
			
			if(tresholds != null)
			{
				for (int i = 0; i < new_columns.length; i++) {
					
					boolean treshold_reached = false;
					
					for (Treshold treshold : tresholds) {
						treshold_reached = treshold.check_field(columns[i]);
						if(treshold_reached)
						{
							String[] new_row = treshold.apply_changes(new_columns, i);
							new_columns = new_row;
							break;
						}
					}
					
					if(treshold_reached)
					{
						break;
					}
				}
			}
			
			String row = "";
			for (int i = 0; i < new_columns.length; i++) 
			{	
				if(i < new_columns.length - 1)
				{
					row += new_columns[i] + writer.get_delimiter();
				}
				else
				{
					row += new_columns[i];
				}
			}
			row += writer.get_line_break();
			writer.writeLine(row);
		}
	}
		
}
