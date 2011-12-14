package csv;


import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;

import org.junit.Test;

import data.DataResource;

public class CSVOperatorTest {

	@Test
	public void testWrite_plain_csv() {
		//read test.csv and write to new.csv
		File file = new File("./new.csv");
		
		BufferedWriter bw = DataResource.getWriter(file);
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		
		CSVOperator op = new CSVOperator();
		op.write_plain_csv(r, w);

	}

	@Test
	public void testRewrite_csv_in_new_order() {

		//read test.csv, swap columns and write to new2.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new2.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		
		String[] order = {"comment", "value", "id"};
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_in_new_order(r, w, order);
	}

	@Test
	public void testRewrite_csv_with_deletes() {
		//read test.csv, delete comment column and write to new3.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new3.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		
		String[] deletes = {"comment"};
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_deletes(r, w, deletes);
	}

	@Test
	public void testRewrite_csv_with_new_order_and_deletes() {
		//read test.csv, delete comment column and swap the others, write to new4.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new4.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		
		String[] new_deletes = {"comment"};
		String[] new_order = {"value", "id"};
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_new_order_and_deletes(r, w, new_order, new_deletes);
	}

}
