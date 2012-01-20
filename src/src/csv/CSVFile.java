package csv;

import java.util.HashMap;


public class CSVFile {

	protected String[] header;
	protected HashMap<String, Integer> column_indices;
	
	private String delimiter;
	private String line_break;
	
	protected String data_begin = null;
	protected String data_end = null;
	
	protected Integer current_line_num = 0;
	
	private int new_column_count = 0;

	public CSVFile(String delimiter, String line_break)
	{
		this.delimiter = delimiter;
		this.line_break = line_break;
	}

	public String[] get_header() 
	{
		return header;
	}

	public String get_delimiter() 
	{
		return delimiter;
	}
	
	public String get_line_break()
	{
		return line_break;
	}
	
	public int get_column_index(String column)
	{
		return column_indices.get(column);
	}

	public void set_new_column_count(int new_column_count) {
		this.new_column_count = new_column_count;
	}

	public int get_new_column_count() {
		return new_column_count;
	}
		
}
