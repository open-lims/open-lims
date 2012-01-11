package csv;

import static org.junit.Assert.*;

import java.io.BufferedReader;

import org.junit.Test;

import data.DataResource;

public class CSVReaderTest {

	@Test
	public void testCSVReader() {
		
		//read test.csv
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",","\r\n");
		String[] columns;
		int row_count = 0;
		while((columns = r.read_line()) != null) //read file linewise
		{
			row_count++;
			assertTrue("Number of columns should be 62.", columns.length == 62);
		}
		
		assertTrue("Number of rows should be 4 (without header).", row_count == 15552);
		
		
	}

}
