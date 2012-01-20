package jobs;

import io.JobConfig;

import java.io.IOException;
import java.io.InputStream;
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

	@Override
	public Integer call() throws Exception 
	{

//		JobsAccess.set_job_started(job_id);
		
		execute();
		
		return 0;
	}
	
	private void execute()
	{
		String file_path = get_file_path();
		try 
		{
			Process process = Runtime.getRuntime().exec(new String[]{"java","-jar",file_path,""+job_id+""});
			process.waitFor();
			System.out.println("job "+job_id+" finished!");
			//get standard and error output to prevent blocking
			InputStream in = process.getInputStream();
			InputStream err = process.getErrorStream();

		} 
		catch (Exception e) 
		{
			e.printStackTrace();
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
