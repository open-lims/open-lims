package csv;

import java.io.BufferedReader;
import java.io.IOException;
import java.util.LinkedList;
import java.util.StringTokenizer;

public class CSVReader extends CSVFile{

	private BufferedReader reader;
	
	public CSVReader(BufferedReader reader, String delimiter, String line_break)
	{
		super(delimiter, line_break);
		this.reader = reader;
		read_header(readLine());
	}
	
	public String[] readLine()
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
			current_line++;
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
		String[] row = null;
		while(current_line <= index)
		{
			row = readLine();
		}
		return row;
	}
	
}
