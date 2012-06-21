package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class DataEntityAccess {

	private int id;
	private String datetime;
	private int owner_id;
	private int owner_group_id;
	private int permission;
	private boolean automatic;
	
	public DataEntityAccess(int data_entity_id)
	{
		String sql = "SELECT * FROM core_data_entities WHERE id='"+data_entity_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				this.id = rs.getInt("id");
				this.datetime = rs.getString("datetime");
				this.owner_id = rs.getInt("owner_id");
				this.owner_group_id = rs.getInt("owner_group_id");
				this.permission = rs.getInt("permission");
				this.automatic = rs.getBoolean("automatic");
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
	
	public String get_datetime()
	{
		return this.datetime;
	}
	
	public int get_owner_id()
	{
		return this.owner_id;
	}
	
	public int get_owner_group_id()
	{
		return this.owner_group_id;
	}
	
	public int get_permission()
	{
		return this.permission;
	}
	
	public boolean get_automatic()
	{
		return this.automatic;
	}
	
}
