package jobs;

import io.DBConfig;
import io.JobConfig;

import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.Future;

import jobs.access.JobsAccess;
import jobs.access.ServicesAccess;

public class JobHandler 
{

	private ExecutorService threadPool = Executors.newFixedThreadPool(2);
	
	private Map<Integer, Future<Integer>> map = new HashMap<Integer,Future<Integer>>();
	
	private boolean running = true;
	
	private int id;
	
	public JobHandler(int id) 
	{
		this.id = id;
		init_configs();
		
//		ServicesAccess.set_status(id, 1);
		
		while(running)
		{
			clear_finished_jobs();
			add_new_jobs();
			give_lifesigns();
			
			try {
				Thread.sleep(5000);
			} 
			catch (InterruptedException e) 
			{
				e.printStackTrace();
			}
		}
	}
	
	private void add_new_jobs() 
	{
		Integer[] new_jobs = JobsAccess.check_for_new_jobs();
		
		System.out.println(new_jobs.length+" new jobs found");
		
		for (int i = 0; i < new_jobs.length; i++) 
		{
			int job_id = new_jobs[i];
			
			System.out.println("adding job with id "+job_id+" to thread pool");

			Job job = new Job(job_id);
			Future<Integer> future = threadPool.submit(job);
			map.put(job_id, future);
//			JobsAccess.set_job_status(job_id, 1);
		}
	}
	
	private void clear_finished_jobs()
	{
		LinkedList<Integer> to_remove = new LinkedList<Integer>();
		
		for (Integer job_id : map.keySet()) 
		{
			Future<Integer> future = map.get(job_id);
			
			try 
			{
				int result = future.get();
				System.out.println("job with id "+job_id+" terminated with code "+result);
//				JobsAccess.set_job_ended(job_id, false);
				to_remove.add(job_id);
			} 
			catch (Exception e) 
			{
//				JobsAccess.set_job_ended(job_id, true);
				e.printStackTrace();
			}
		}
		
		for (Integer job_id : to_remove) 
		{
			map.remove(job_id);
		}
	}
	
	private void give_lifesigns() 
	{
		//give lifesigns for open jobs
		for (Integer job_id : map.keySet()) 
		{
			JobsAccess.set_lifesign(job_id);
		}
		
		//give lifesign for job handler
		ServicesAccess.set_lifesign(id);
	}
	
	private void shutdown_if_needed()
	{
		int status = ServicesAccess.get_status(id);
		
		if(status == 1)
		{ //alles ok
			
		}
		else if(status == 2)
		{ //soft shutdown
			//alle unbearbeiteten jobs entfernen
			//pool shutdown sobald alle fertig
		}
		else if(status == 3)
		{ //hard shutdown
			//alle threads killen, pool shutdown	
		}
		else if(status == 4)
		{ //fehler
			//?
		}
	}
	
	private void init_configs() 
	{
		new DBConfig();
		new JobConfig();
	}
	
	private void shutDown() 
	{
		threadPool.shutdown();
	}


	public static void main(String[] args) {
		JobHandler handler = new JobHandler(1);
		handler.shutDown();
	}
}

