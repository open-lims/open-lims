package application;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;

import csv.CSVOperator;
import csv.CSVReader;
import csv.CSVWriter;
import tresholds.SwapRowsTreshold;
import tresholds.interfaces.Treshold;
import data.DataResource;


public class Application {

	
	private int[] csv_file_ids = {2995};
	
	private void convert_csv(int file_id)
	{
		BufferedWriter bw = DataResource.getWriter(new File("./app.csv"));
		CSVWriter w = new CSVWriter(bw, ",", "\r\n");
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(file_id);
		CSVReader r = new CSVReader(br, ",", "\r\n");
		
		int[] associated_files = {2996, 2997}; //has to be set somehow
		
		Treshold t = new SwapRowsTreshold(associated_files);
		
		String[] deletes = {};
		String[] order = {};
		
		Treshold[] tresholds = {t};
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_tresholds(r, w, order, deletes, tresholds);
		
	}
	
	public static void main(String[] args) {
		Application app = new Application();
		
		for (int i = 0; i < app.csv_file_ids.length; i++) {
			app.convert_csv(app.csv_file_ids[i]);
		}
	}

}
