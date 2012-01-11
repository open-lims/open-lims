package data;

import io.DBConfig;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.sql.ResultSet;
import java.sql.SQLException;

import data.access.DataEntityHasDataEntityAccess;
import data.access.DataEntityIsItemAccess;
import data.access.FileAccess;
import data.access.FileVersionAccess;
import data.access.FolderAccess;


/**
 * Basic Data IO.
 * @author Roman Quiring
 */
public class DataResource {
	
	public static BufferedReader getResourceFromFileIdAsStream(int file_id)
	{
		int file_version_id = FileVersionAccess.get_current_file_version_id_by_file_id(file_id);
		FileVersionAccess access = new FileVersionAccess(file_version_id, file_id);
		return getResourceFromFileIdAsStream(file_id, access.get_version());
	}
	
	public static BufferedReader getResourceFromFileIdAsStream(int file_id, int version) 
	{
		FileAccess access = new FileAccess(file_id);
		return get_resource_stream_helper(file_id, access.get_data_entity_id(), version);
	}
	
	public static BufferedReader getResourceFromDataEntityIdAsStream(int data_entity_id) 
	{
		int file_id = FileAccess.get_file_id_by_data_entity_id(data_entity_id);
		int file_version_id = FileVersionAccess.get_current_file_version_id_by_file_id(file_id);
		FileVersionAccess file_version_access = new FileVersionAccess(file_version_id, file_id);
		return get_resource_stream_helper(file_id, data_entity_id,  file_version_access.get_internal_revision());
	}
	
	public static BufferedReader getResourceFromDataEntityIdAsStream(int data_entity_id, int version) 
	{
		int file_id = FileAccess.get_file_id_by_data_entity_id(data_entity_id);
		return get_resource_stream_helper(file_id, data_entity_id, version);
	}
	
	public static BufferedReader getResourceFromItemIdAsStream(int item_id) 
	{
		DataEntityIsItemAccess access = new DataEntityIsItemAccess(item_id);
		int file_id = FileAccess.get_file_id_by_data_entity_id(access.get_data_entity_id());
		int file_version_id = FileVersionAccess.get_current_file_version_id_by_file_id(file_id);
		FileVersionAccess file_version_access = new FileVersionAccess(file_version_id, file_id);
		return getResourceFromDataEntityIdAsStream(access.get_data_entity_id(), file_version_access.get_internal_revision());
	}
	
	public static BufferedReader getResourceFromItemIdAsStream(int item_id, int version) 
	{
		DataEntityIsItemAccess access = new DataEntityIsItemAccess(item_id);		
		return getResourceFromDataEntityIdAsStream(access.get_data_entity_id(), version);
	}
	
	private static BufferedReader get_resource_stream_helper(int file_id, int data_entity_id, int version)
	{		
		int file_version_id = FileVersionAccess.get_file_version_id_by_file_id_and_internal_revision(file_id, version);
		if(file_version_id == -1)
		{
			return null;
		}
		
		FileVersionAccess file_version_access = new FileVersionAccess(file_version_id, file_id);
		String file_extension = file_version_access.get_file_extension();

		ResultSet rs = DataEntityHasDataEntityAccess.list_pid_by_cid(data_entity_id);
		
		int folder_data_entity = -1;
		try 
		{
			while (rs.next()) 
			{
				folder_data_entity = rs.getInt(1);
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}

		int folder_id = FolderAccess.get_folder_id_by_data_entity_id(folder_data_entity);
		FolderAccess folder_access = new FolderAccess(folder_id);
		String path = folder_access.get_path();
		
		String absolute_path = "";

		if(file_extension == "")
		{
			absolute_path = DBConfig.get_config("baseFolder")+"/"+path+"/"+data_entity_id+"-"+version;
		}
		else
		{
			absolute_path = DBConfig.get_config("baseFolder")+"/"+path+"/"+data_entity_id+"-"+version+"."+file_extension;
		}

		BufferedReader br = null;

		try 
		{
			FileReader fr = new FileReader(absolute_path);
			br = new BufferedReader(fr);
		}
		catch (FileNotFoundException e) 
		{
			e.printStackTrace();
		}
		return br;
	}
	
	public static BufferedWriter getWriter(File file)
	{
		try {
			FileWriter fw = new FileWriter(file);
			return new BufferedWriter(fw);
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}
	
}
