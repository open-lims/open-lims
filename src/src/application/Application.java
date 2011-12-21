package application;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;

import csv.CSVOperator;
import csv.CSVReader;
import csv.CSVWriter;
import tresholds.NullifyTreshold;
import tresholds.SwapRowsTreshold;
import tresholds.interfaces.Treshold;
import data.DataResource;


public class Application {

	
	private void convert_csv(int file_id, int[] associated_files)
	{
		BufferedWriter bw = DataResource.getWriter(new File("./app.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(file_id);
		CSVReader r = new CSVReader(br, ",", "\r\n");
		
		Treshold t1 = new SwapRowsTreshold(associated_files);
		Treshold t2 = new NullifyTreshold();
		
		String[] allowed_cols = {"Index","Array Row","Array Column","Spot Row","Spot Column","Ch1 Median","Ch1 F % Sat.","Ch2 Median","Ch2 F % Sat."};
		
		Integer[] deletes = calculate_deleted_indices(r, allowed_cols);
		Integer[] order = {};
		
		Treshold[] tresholds = {t1, t2};
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_tresholds(r, w, order, deletes, tresholds);
	}
	
	
	private Integer[] calculate_deleted_indices(CSVReader reader, String[] allowed_cols)
	{
		int[] allowed_indices = new int[allowed_cols.length];
		
		for (int i = 0; i < allowed_cols.length; i++) 
		{
			int index = reader.get_column_index(allowed_cols[i]);
			allowed_indices[i] = index;
		}
		
		int header_length = reader.get_header().length;
		
		Integer[] deletes = new Integer[header_length - allowed_cols.length];
		
		int num_deletes = 0;
		
		for (int i = 0; i < reader.get_header().length; i++) 
		{
			boolean delete = true;
	
			for (int j = 0; j < allowed_indices.length; j++) 
			{
				if(i == allowed_indices[j])
				{
					delete = false;
					num_deletes++;
					break;
				}
			}
			if(delete)
			{
				deletes[i - num_deletes] = i;
			}
		}
		return deletes;
	}
	
	
	public static void main(String[] args) 
	{
		Application app = new Application();
		
		int file_id = 3017;
		int[] associated_files = {3019, 3018, 3021, 3022}; //has to be set somehow
		
		app.convert_csv(file_id, associated_files);
	}

}
