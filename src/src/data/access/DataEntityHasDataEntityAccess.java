package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class DataEntityHasDataEntityAccess {

	private int data_entity_pid;
	private int data_entity_cid;
	
	public DataEntityHasDataEntityAccess(int data_entity_pid, int data_entity_cid)
	{
		String sql = "SELECT * FROM core_data_entity_has_data_entities WHERE data_entity_cid='"+data_entity_cid+"' AND data_entity_pid='"+data_entity_pid+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				this.data_entity_pid = rs.getInt("data_entity_pid");
				this.data_entity_cid = rs.getInt("data_entity_cid");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
	}
	
	public static ResultSet list_pid_by_cid(int data_entity_cid)
	{
		String sql = "SELECT data_entity_pid FROM core_data_entity_has_data_entities WHERE data_entity_cid='"+data_entity_cid+"'";
		return DBLink.executeQuery(sql);
	}
	
}
