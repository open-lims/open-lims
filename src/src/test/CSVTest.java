package test;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;

import csv.CSVOperator;
import csv.CSVReader;
import csv.CSVWriter;
import data.DataResource;

import static org.junit.Assert.*;


public class CSVTest {

	public static void test()
	{
		testCSVReader();
	}
	
	private static void testCSVReader() 
	{
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
		
		
		BufferedWriter bw = DataResource.getWriter(new File("./new.csv"));
		
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		br = DataResource.getResourceFromFileIdAsStream(2973);
		r = new CSVReader(br, ";","\r\n");
		CSVOperator op = new CSVOperator();
		op.writePlainCSV(r, w);
		
		
		
//		//read test.csv and swap columns
//		br = DataResource.getResourceFromFileIdAsStream(2973);
//		r = new CSVReader(br);
//		String[] order = {"value","id","comment"};
//		columns = r.readLine(order);
//		assertTrue("Value should be the first column now.",columns[0].equals("asd"));
//		assertTrue("ID should be the first second now.",columns[1].equals("1"));
//		assertTrue("Comment should be the third column now.",columns[2].equals("donk"));
//		
//		//read test.csv, swap columns and ignore 1 column
//		br = DataResource.getResourceFromFileIdAsStream(2973);
//		r = new CSVReader(br);
//		String[] ignored = {"comment"};
//		columns = r.readLine(order, ignored);
//		for (String string : columns) {
//			System.out.println(string);
//		}
//		assertTrue("Number of columns should be 2.", columns.length == 2);
//		assertTrue("Value should be the first column now.",columns[0].equals("asd"));
//		assertTrue("ID should be the second column now.",columns[1].equals("1"));
	}

}
