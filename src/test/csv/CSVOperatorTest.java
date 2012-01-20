package csv;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;

import org.junit.Test;

import interfaces.Treshold;

import data.DataResource;

public class CSVOperatorTest {

	@Test
	public void testWrite_plain_csv() {
		//read csv and write to new.csv
		File file = new File("./new.csv");
		
		BufferedWriter bw = DataResource.getWriter(file);
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",","\r\n", "BEGIN DATA", "END DATA");
		
		CSVOperator op = new CSVOperator();
		op.write_plain_csv(r, w);

	}

	@Test
	public void testRewrite_csv_in_new_order() {

		//read csv, swap columns and write to new2.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new2.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",", "\r\n", "BEGIN DATA", "END DATA");
		
		Integer[] order = {61,60,59,58,57,56,55,54,53,52,51,50,49,48,47,46,45,44,43,42,41,40,39,38,37,36,35,34,33,32,31,30,29,28,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1,0};
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_in_new_order(r, w, order);
	}

	@Test
	public void testRewrite_csv_with_deletes() {
		//read csv, delete columns and write to new3.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new3.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",", "\r\n", "BEGIN DATA", "END DATA");
		
		String[] deletes = {"Array Row","Array Column","Spot Row", "Name"};
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_deletes(r, w, deletes);
	}

	@Test
	public void testRewrite_csv_with_new_order_and_deletes() {
		//read csv, delete columns and swap the others, write to new4.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new4.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",","\r\n", "BEGIN DATA", "END DATA");
		
		Integer[] deletes = {11,12,13,14};
		Integer[] order = {10,9,8,7,6,5,4,3,2,1,0};
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_new_order_and_deletes(r, w, order, deletes);
	}
	
	@Test
	public void testRewrite_csv_with_tresholds()
	{
		//read csv, set some tresholds and write to new5.csv
		BufferedWriter bw = DataResource.getWriter(new File("./new5.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(3017);
		CSVReader r = new CSVReader(br, ",", "\r\n", "BEGIN DATA", "END DATA");
		
		Treshold t1 = new Treshold() {
			public boolean check_field(String field, int column_index) {
				if(column_index == 10) {
					try{
						int num = Integer.parseInt(field);
						if(num >= 10191)
						{
							return true;
						}
					}
					catch(Exception e){}
				}
				return false;
			}

			public String[] apply_changes(String[] row, int column_index, int row_index) {
				row[column_index] = "TRESHOLD REACHED";
				return row;
			}

			public void destroy() {}
		};
		
		Treshold t2 = new Treshold() {
			
			private BufferedReader br = DataResource.getResourceFromFileIdAsStream(3018);
			private CSVReader r = new CSVReader(br, ",","\r\n", "BEGIN DATA", "END DATA");
			
			public boolean check_field(String field, int column_index) {
				if(column_index == 6) {
					if(field.equals("\"emp\""))
						return true;
				}
				return false;
			}

			public String[] apply_changes(String[] row, int column_index, int row_index) {
				String[] row_to_switch = r.read_line(row_index);
				row_to_switch[row_to_switch.length-1] = "THIS ENTIRE ROW HAS BEEN CHANGED";
				return row_to_switch;
			}

			public void destroy() {
				r.close();
			}
		};

		Treshold[] tresholds = {t1, t2};
		
		String[] new_deletes = {};
		String[] new_order = {};
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_tresholds(r, w, new_order, new_deletes, tresholds);
	}

}
