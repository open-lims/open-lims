package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class FileAccess {

	private int id;
	private int data_entity_id;
	private int flag;
	
	public FileAccess(int file_id)
	{
		String sql = "SELECT * FROM core_files WHERE id='"+file_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try {
			while (rs.next()) 
			{
				this.id = rs.getInt("id");
				this.data_entity_id = rs.getInt("data_entity_id");
				this.flag = rs.getInt("flag");
			}
		} catch (SQLException e) {
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
	
	public int get_flag()
	{
		return this.flag;
	}
	
	
	public static int get_file_id_by_data_entity_id(int data_entity_id)
	{
		String sql = "SELECT id FROM core_files WHERE data_entity_id='"+data_entity_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next())
			{
				return rs.getInt("id");
			}
		} 
		catch (NumberFormatException e) 
		{
			e.printStackTrace();
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return -1;
	}
}
