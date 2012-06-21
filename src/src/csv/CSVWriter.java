package csv;

import java.io.BufferedWriter;
import java.io.IOException;

public class CSVWriter extends CSVFile{

private BufferedWriter writer;
	
	public CSVWriter(BufferedWriter writer, String delimiter, String line_break)
	{
		super(delimiter, line_break);
		this.writer = writer;
	}
	
	public boolean writeLine(String line)
	{
		try 
		{
			writer.write(line);
			current_line_num++;
			return true;
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		return false;
	}
	
	public void close()
	{
		try 
		{
			writer.close();
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
	}
	
}
