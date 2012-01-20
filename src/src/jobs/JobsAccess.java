package jobs;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.util.ArrayList;

import base.DBLink;

public class JobsAccess 
{
	
	static Integer[] check_for_new_jobs()
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
	
	static void set_job_status(int job_id, int status)
	{
		String sql = "UPDATE core_jobs SET status="+status+" WHERE id="+job_id;
		DBLink.executeQuery(sql);
	}
	
	static void set_job_started(int job_id) 
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql = "UPDATE core_jobs SET status=2, start_datetime="+timestamp+" last_lifesign="+timestamp+" WHERE id="+job_id+"";
		DBLink.executeQuery(sql);
	}
	
	static void set_job_ended(int job_id, boolean error) 
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql;
		if(error)
		{
			sql = "UPDATE core_jobs SET status=4, end_datetime="+timestamp+" WHERE id="+job_id+"";
		}
		else
		{
			sql = "UPDATE core_jobs SET status=3, end_datetime="+timestamp+" WHERE id="+job_id+"";
		}
		DBLink.executeQuery(sql);
	}
	
	static void set_lifesign(int job_id)
	{
		Timestamp timestamp = new Timestamp(System.currentTimeMillis()); 
		String sql = "UPDATE core_jobs SET last_lifesign="+timestamp+" WHERE id="+job_id+"";
		DBLink.executeQuery(sql);
	}
}
