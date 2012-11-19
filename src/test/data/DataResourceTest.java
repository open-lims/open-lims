package data;

import static org.junit.Assert.assertFalse;

import java.io.BufferedReader;

import org.junit.Test;


public class DataResourceTest {
	
	@Test
	public void testFileIdStream()
	{
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		assertFalse("BufferedReader may not be null", br == null);
	}
	
	@Test
	public void testDataEntityIdSream()
	{
		BufferedReader br = DataResource.getResourceFromDataEntityIdAsStream(12162);
		assertFalse("BufferedReader may not be null", br == null);
	}
	
	@Test
	public void testItemIdStream()
	{
		BufferedReader br = DataResource.getResourceFromItemIdAsStream(4129);
		assertFalse("BufferedReader may not be null", br == null);
	}

}
