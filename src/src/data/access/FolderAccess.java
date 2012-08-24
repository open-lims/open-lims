package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class FolderAccess {
	private int id;
	private int data_entity_id;
	private String name;
	private String path;
	private boolean deleted;
	private boolean blob;
	private int flag;
	
	public FolderAccess(int folder_id)
	{
		String sql = "SELECT * FROM core_folders WHERE id='"+folder_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				this.id = rs.getInt("id");
				this.data_entity_id = rs.getInt("data_entity_id");
				this.name = rs.getString("name");
				this.path = rs.getString("path");
				this.deleted = rs.getBoolean("deleted");
				this.blob = rs.getBoolean("blob");
				this.flag = rs.getInt("flag");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
	}
	
	public int get_id()
	{
		return this.id;
	}
	
	public int get_data_entity_id()
	{
		return this.data_entity_id;
	}
	
	public String get_name()
	{
		return this.name;
	}
	
	public String get_path()
	{
		return this.path;
	}
	
	public boolean get_deleted()
	{
		return this.deleted;
	}
	
	public boolean get_blob()
	{
		return this.blob;
	}
	
	public int get_flag()
	{
		return this.flag;
	}
	
	
	public static int get_folder_id_by_data_entity_id(int data_entity_id)
	{
		String sql = "SELECT id FROM core_folders WHERE data_entity_id='"+data_entity_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				return rs.getInt("id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return -1;
	}
	
}
