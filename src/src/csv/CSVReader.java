package csv;

import java.io.BufferedReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.StringTokenizer;

import interfaces.MetadataReader;

public class CSVReader extends CSVFile{

	private BufferedReader reader;
	
	private String[] current_line;

	private MetadataReader[] metadata_readers = null;
	
	public CSVReader(BufferedReader reader, String delimiter, String line_break, String data_begin_tag, String data_end_tag)
	{
		super(delimiter, line_break);
		this.reader = reader;
		this.data_begin = data_begin_tag;
		this.data_end = data_end_tag;
		
		if(data_begin != null)
		{
			read_header();
		}
	}
	
	public CSVReader(BufferedReader reader, String delimiter, String line_break, String data_begin_tag, String data_end_tag, MetadataReader[] metadata_readers)
	{
		super(delimiter, line_break);
		this.reader = reader;
		this.data_begin = data_begin_tag;
		this.data_end = data_end_tag;
		
		this.metadata_readers = metadata_readers;
		
		if(data_begin != null)
		{
			read_header();
		}
	}
	
	public String[] read_line()
	{
		try 
		{
			LinkedList<String> row = new LinkedList<String>();
			String line = "";
			if((line = reader.readLine()) == null)
			{
				reader.close();
				return null;
			}
			else if(data_end != null)
			{
				if(line.equals(data_end))
				{
					reader.close();
					return null;
				}
			}
			current_line_num++;
			StringTokenizer tokenizer = new StringTokenizer(line, get_delimiter());
			while(tokenizer.hasMoreTokens())
			{
				row.add(tokenizer.nextToken());
			}
			return row.toArray(new String[row.size()]);
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		return null;
	}
	
	public String[] read_line(int index)
	{
		while(current_line_num < index)
		{
			current_line = read_line();
		}
		return current_line;
	}
	
	private void read_header()
	{
		String line = "";
		try 
		{
			while((line = reader.readLine()) != null)
			{
				if(line.equals(data_begin))
				{
					break;
				}
				
				if(metadata_readers != null)
				{
					for (int i = 0; i < metadata_readers.length; i++) 
					{
						if(line.equals(metadata_readers[i].get_metadata_begin()))
						{
							LinkedList<String> metadata = new LinkedList<String>();
							while((line = reader.readLine()) != null && !line.equals(metadata_readers[i].get_metadata_end()))
							{
								metadata.add(line);
							}
							String[] data = new String[metadata.size()];
							int count = 0;
							for (String string : metadata) 
							{
								data[count] = string;
								count++;
							}
							metadata_readers[i].read_metadata(data);
							break;
						}
					}
				}
			}
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		
		header = read_line();
		current_line_num--; //header is not considered a row

		column_indices = new HashMap<String, Integer>(header.length);
		for (int i = 0; i < header.length; i++) 
		{
			column_indices.put(header[i], i);
		}
	}
	
	public void close()
	{
		try 
		{
			reader.close();
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
	}
	
}
