package jobs.access;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.util.ArrayList;

import base.DBLink;

public class JobsAccess 
{
	
	public static Integer[] check_for_new_jobs()
	{
		String sql = "SELECT id FROM core_jobs WHERE status=0";
		ResultSet rs = DBLink.executeQuery(sql);
		
		ArrayList<Integer> new_jobs = new ArrayList<Integer>();
		
		try 
		{
			while (rs.next()) 
			{
				int id = rs.getInt("id");
				new_jobs.add(id);
			}
		}
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		Integer[] array = new Integer[new_jobs.size()];
		return new_jobs.toArray(array);
	}
	
	public static void set_job_status(int job_id, int status)
	{
		String sql = "UPDATE core_jobs SET status="+status+" WHERE id="+job_id;
		DBLink.updateQuery(sql);
	}
	
	public static void set_job_started(int job_id) 
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql = "UPDATE core_jobs SET status=2, start_datetime=TIMESTAMP '"+timestamp+"', last_lifesign=TIMESTAMP '"+timestamp+"' WHERE id="+job_id+"";
		DBLink.updateQuery(sql);
	}
	
	public static void set_job_ended(int job_id, boolean error) 
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql;
		if(error)
		{
			sql = "UPDATE core_jobs SET status=4, end_datetime=TIMESTAMP '"+timestamp+"' WHERE id="+job_id+"";
		}
		else
		{
			sql = "UPDATE core_jobs SET status=3, end_datetime=TIMESTAMP '"+timestamp+"' WHERE id="+job_id+"";
		}
		DBLink.updateQuery(sql);
	}
	
	public static void set_lifesign(int job_id)
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql = "UPDATE core_jobs SET last_lifesign=TIMESTAMP '"+timestamp+"' WHERE id="+job_id+"";
		DBLink.updateQuery(sql);
	}
	
	public static int get_binary_id(int job_id)
	{
		String sql = "SELECT binary_id FROM core_jobs WHERE id="+job_id+"";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while(rs.next())
			{
				return rs.getInt("binary_id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return -1;
	}
	
	public static Integer[] get_pending_jobs()
	{
		String sql = "SELECT * FROM core_jobs WHERE status=1";
		ResultSet rs = DBLink.executeQuery(sql);
		ArrayList<Integer> pending_jobs = new ArrayList<Integer>();
		try 
		{
			while(rs.next())
			{
				pending_jobs.add(rs.getInt("id"));
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		Integer[] array = new Integer[pending_jobs.size()];
		return pending_jobs.toArray(array);
	}
	
	public static int get_job_status(int job_id)
	{
		String sql = "SELECT * FROM core_jobs WHERE id="+job_id;
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
}
