package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class DataEntityIsItemAccess {

	private int data_entity_id;
	private int item_id;
	
	public DataEntityIsItemAccess(int item_id)
	{
		String sql = "SELECT * FROM core_data_entity_is_item WHERE item_id='"+item_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				this.data_entity_id = rs.getInt("data_entity_id");
				this.item_id = rs.getInt("item_id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
	}
	
	public int get_data_entity_id()
	{
		return this.data_entity_id;
	}
	
	public int get_item_id()
	{
		return this.item_id;
	}
}
