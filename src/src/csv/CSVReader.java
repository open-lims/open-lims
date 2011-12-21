package csv;

import java.io.BufferedReader;
import java.io.IOException;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.StringTokenizer;

public class CSVReader extends CSVFile{

	private BufferedReader reader;
	
	private String[] current_line;
	
	public int channel;
	
	
	public CSVReader(BufferedReader reader, String delimiter, String line_break)
	{
		super(delimiter, line_break);
		this.reader = reader;
		read_header();
	}
	
	public String[] readLine()
	{
		try 
		{
			LinkedList<String> row = new LinkedList<String>();
			String line = "";
			if((line = reader.readLine()) == null || line.equals("END DATA"))
			{
				reader.close();
				return null;
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
	
	public String[] readLine(int index)
	{
		while(current_line_num < index)
		{
			current_line = readLine();
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
				if(line.equals("BEGIN IMAGE INFO"))
				{
					String ch1 = reader.readLine();
					ch1 = reader.readLine();
					String[] ch1_split = ch1.split(",");
					ch1 = ch1_split[3];
					if(ch1.equals("Cyanine 5"))
					{
						channel = 1;
					}
					else
					{
						channel = 2;
					}
					String ch2 = reader.readLine();
//					String[] ch2_split = ch2.split(",");
//					ch2 = ch2_split[3];
				}
				else if(line.equals("BEGIN DATA"))
				{
					break;
				}
			}
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		
		header = readLine();
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
