package jobs.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class BinaryAccess {

	public static String get_binary_path(int id)
	{
		String sql = "SELECT path FROM core_binaries WHERE id="+id;
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while(rs.next())
			{
				return rs.getString("path");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		
		return null;
	}
	
	public static String get_binary_file(int id)
	{
		String sql = "SELECT file FROM core_binaries WHERE id="+id;
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while(rs.next())
			{
				return rs.getString("file");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		
		return null;
	}
	
}
