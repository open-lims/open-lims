package csv;

import static org.junit.Assert.*;

import java.io.BufferedReader;

import org.junit.Test;

import data.DataResource;

public class CSVReaderTest {

	@Test
	public void testCSVReader() {
		
		//read test.csv
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		String[] columns;
		int row_count = 0;
		while((columns = r.readLine()) != null) //read file linewise
		{
			row_count++;
			assertTrue("Number of columns should be 3.", columns.length == 3);
		}
		assertTrue("Number of rows should be 4 (without header).", row_count == 4);
		
		
	}

}
