package csv;

import java.util.HashMap;

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
		rewrite(csv_reader, csv_writer, order, null);
	}
	
	public void rewrite_csv_with_deletes(CSVReader csv_reader, CSVWriter csv_writer, String[] deletes)
	{	
		rewrite(csv_reader, csv_writer, null, deletes);
	}
	
	public void rewrite_csv_with_new_order_and_deletes(CSVReader csv_reader, CSVWriter csv_writer, String[] order, String[] deletes)
	{
		rewrite(csv_reader, csv_writer, order, deletes);
	}
	
	private void rewrite(CSVReader csv_reader, CSVWriter csv_writer, String[] order, String[] deletes)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = get_real_indices_to_desired_column_indices(csv_reader, csv_writer, order, deletes);
		
		rewrite_header(csv_reader, csv_writer, real_indices_to_desired_column_indices);
		
		rewrite_rows(csv_reader, csv_writer, real_indices_to_desired_column_indices);
		
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
			for (int i = 0; i < header.length; i++) {
				real_indices_to_desired_column_indices.put(i, i);
			}
		}
		else if(order == null)
		{
			new_column_count = header.length - deletes.length;
			
			real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(new_column_count);
			for (int i = 0; i < header.length; i++) {
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) {
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
			for (int i = 0; i < header.length; i++) {
				real_indices_to_desired_column_indices.put(i, i);
			}
			
			for (int i = 0; i < deletes.length; i++) {
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
		for (int i = 0; i < new_header.length; i++) {
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
	
	private void rewrite_rows(CSVReader reader, CSVWriter writer, HashMap<Integer, Integer> real_indices_to_desired_column_indices)
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
					if(desired_index < new_column_count - 1)
					{
						new_columns[desired_index] = columns[i] + writer.get_delimiter();
					}
					else
					{
						new_columns[desired_index] = columns[i];
					}
				}
			}
			String row = "";
			for (int i = 0; i < new_columns.length; i++) 
			{
				row += new_columns[i];
			}
			row += writer.get_line_break();
			writer.writeLine(row);
		}
	}
	
	
	
	//quelle->ziel mit tresholds
	
	
}
