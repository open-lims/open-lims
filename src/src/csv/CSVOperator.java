package csv;

import java.util.HashMap;

public class CSVOperator {

	
	
	public CSVOperator()
	{
		
	}
	
	public void writePlainCSV(CSVReader csv_reader, CSVWriter csv_writer)
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
	
	public void rewriteCSV(CSVReader csv_reader, CSVWriter csv_writer, String[] order)
	{
		HashMap<Integer, Integer> real_indices_to_desired_column_indices = new HashMap<Integer, Integer>(order.length);
		for (int i = 0; i < order.length; i++) {
//			int original_index = csv_
//			desired_column_indices.put(order[i], i);
		}
		
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
	
	
	//formatangabe quelle, ziel
	
	//quelle->ziel
	
	//quelle-> ziel mit angabe von deletes 
	
	//quelle->ziel mit tresholds
	
	//
	
}
