package test;

import static org.junit.Assert.assertFalse;

import java.io.BufferedReader;

import data.DataResource;

public class DataResourceTest {
	
	public static void test()
	{
		testFileIdStream();
		testDataEntityIdSream();
		testItemIdStream();
	}
	
	private static void testFileIdStream()
	{
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		assertFalse("BufferedReader may not be null", br == null);
	}
	
	private static void testDataEntityIdSream()
	{
		BufferedReader br = DataResource.getResourceFromDataEntityIdAsStream(12162);
		assertFalse("BufferedReader may not be null", br == null);
	}
	
	private static void testItemIdStream()
	{
		BufferedReader br = DataResource.getResourceFromItemIdAsStream(4129);
		assertFalse("BufferedReader may not be null", br == null);
	}

}
