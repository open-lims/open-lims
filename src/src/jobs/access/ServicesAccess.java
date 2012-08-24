package jobs.access;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;

import base.DBLink;

public class ServicesAccess {

	public static int get_status(int id)
	{
		String sql = "SELECT status FROM core_services WHERE id="+id;
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while(rs.next())
			{
				return rs.getInt("status");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		
		return -1;
	}
	
	public static void set_status(int id, int status)
	{
		String sql = "UPDATE core_services SET status="+status+" WHERE id="+id;
		DBLink.updateQuery(sql);
	}
	
	public static void set_lifesign(int id)
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql = "UPDATE core_services SET last_lifesign=TIMESTAMP '"+timestamp+"' WHERE id="+id+"";
		DBLink.updateQuery(sql);
	}
	
}
