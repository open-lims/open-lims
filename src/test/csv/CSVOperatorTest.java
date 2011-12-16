package csv;


import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.util.LinkedList;
import java.util.List;

import org.junit.Test;

import csv.interfaces.Treshold;

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
	
	@Test
	public void testRewrite_csv_with_tresholds()
	{
		//read test.csv, set some tresholds and write to new5.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new5.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
		CSVReader r = new CSVReader(br, ";","\r\n");
		
		Treshold t = new Treshold() {
			public boolean check_field(String field) {
				if(field.equals("1"))
					return true;
				return false;
			}

			public String[] apply_changes(String[] row, int column_index) {
				row[column_index] = "TRESHOLD REACHED";
				return row;
			}
		};
		Treshold t2 = new Treshold() {
			public boolean check_field(String field) {
				if(field.equals("bonk"))
					return true;
				return false;
			}

			public String[] apply_changes(String[] row, int column_index) {
				
				BufferedReader br = DataResource.getResourceFromFileIdAsStream(2973);
				CSVReader r = new CSVReader(br, ";","\r\n");
				
				String[] switched_row = null;
				while(r.current_line < 3)
				{
					switched_row = r.readLine();
				}
				return switched_row;
			}
		};
		
		List<Treshold> treshold_list = new LinkedList<Treshold>();
		treshold_list.add(t);
		treshold_list.add(t2);
		
		String[] new_deletes = {};
		String[] new_order = {};
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_tresholds(r, w, new_order, new_deletes, treshold_list);
	}

}
