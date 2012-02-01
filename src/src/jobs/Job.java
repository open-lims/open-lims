package jobs;

import io.JobConfig;

import java.util.concurrent.Callable;

import jobs.access.BinaryAccess;
import jobs.access.JobsAccess;


public class Job implements Callable<Integer> 
{

	private int job_id;
	
	public Job(int id) 
	{
		this.job_id = id;
	}

	public Integer call() throws Exception 
	{
		if(JobsAccess.get_job_status(job_id) == 0)
		{
			return 0;
		}
		
		JobsAccess.set_job_started(job_id);
		
		return execute();
	}
	
	private int execute()
	{
		String file_path = get_file_path();
		try 
		{
			Process process = Runtime.getRuntime().exec(new String[]{"java","-jar",file_path,""+job_id+""});
			process.waitFor();
			
			//get the return value of the job TODO use for error indication
			int result = process.exitValue();
			if(result == 0)
			{
				return 3;
			}
			else
			{
				return 4;
			}
		} 
		catch (Exception e) 
		{
			//when shutting down hard we get here
			return 4;
		}
	}
	
	private String get_file_path()
	{
		int binary_id = JobsAccess.get_binary_id(job_id);
		
		String root = JobConfig.get_config("binaryRoot");
		if(root.charAt(root.length()-1) != '/')
		{
			root += "/";
		}
		
		String path = BinaryAccess.get_binary_path(binary_id);
		if(path == null)
		{
			path = "";
		}
		else 
		{
			if(path.charAt(0) == '/') 
			{
				path = path.substring(1);
			}
			if(path.charAt(path.length()-1) != '/')
			{
				path += "/";
			}
		}
		
		String file = BinaryAccess.get_binary_file(binary_id);
		if(file.charAt(0) == '/') 
		{
			file = file.substring(1);
		}
		
		return root+path+file;
	}
	
}
