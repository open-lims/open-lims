package application;
import io.CSVConfig;
import io.DBConfig;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.DataInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStreamReader;

import metadata.BiochemistryMetadataReader;
import metadata.interfaces.MetadataReader;

import csv.CSVOperator;
import csv.CSVReader;
import csv.CSVWriter;
import tresholds.NullifyTreshold;
import tresholds.SwapRowsTreshold;
import tresholds.interfaces.Treshold;
import data.DataResource;


public class Application {

	/**
	 * Constructor.
	 */
	public Application() 
	{
		new CSVConfig();
		new DBConfig();		
	}
	
	private void convert_csv(int file_id, File destination, Treshold[] tresholds, String[] allowed_cols)
	{		
		BiochemistryMetadataReader biodata_reader = new BiochemistryMetadataReader();
		MetadataReader[] metadata_readers = new MetadataReader[]{biodata_reader};
		
		BufferedReader br = DataResource.getResourceFromFileIdAsStream(file_id);
		CSVReader r = new CSVReader(br, CSVConfig.get_config("csvSeparatorSource"), CSVConfig.get_config("csvNewlineSource").replace("\\r", "\r").replace("\\n", "\n"),metadata_readers);
		
		BufferedWriter bw = DataResource.getWriter(destination);
		CSVWriter w = new CSVWriter(bw, CSVConfig.get_config("csvSeparatorTarget"), CSVConfig.get_config("csvNewlineTarget").replace("\\r", "\r").replace("\\n", "\n"));
		
		Integer[] deletes = calculate_deleted_indices(r, allowed_cols);
		
		String[] order = {};
			
		
		if(biodata_reader.get_channel() == 1)
		{ //change order
			System.out.println("channel is 1");
		}
		
		CSVOperator op = new CSVOperator();
		op.rewrite_csv_with_tresholds_new(r, w, order, tresholds);
		
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
				
		//file id to convert
		int file_id = 3017;
		
		//tresholds to check
		int[] associated_files = {3018, 3019, 3021, 3022};
		Treshold t1 = new SwapRowsTreshold(associated_files, "65535");
		Treshold t2 = new NullifyTreshold("\"emp\"");
		Treshold[] tresholds = {t1, t2};
		
		//desired output columns
		String[] allowed_cols = {"Index","Array Row","Array Column","Spot Row","Spot Column","Ch1 Median","Ch1 F % Sat.","Ch2 Median","Ch2 F % Sat."};
		
		//desired output location
		File destination = new File("./app.csv");
		
		app.convert_csv(file_id, destination, tresholds, allowed_cols);
	}

}
