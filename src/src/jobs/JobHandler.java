package jobs;

import io.DBConfig;

import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.Future;

public class JobHandler 
{

	private ExecutorService threadPool = Executors.newFixedThreadPool(4);
	
	private Map<Integer, Future<Integer>> map = new HashMap<Integer,Future<Integer>>();
	
	private boolean running = true;
	
	public JobHandler() 
	{
		init_configs();
		
		while(running)
		{
			clear_finished_jobs();
			add_new_jobs();
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
			JobsAccess.set_job_status(job_id, 1);
		}
	}
	
	private void clear_finished_jobs()
	{
		for (Integer job_id : map.keySet()) 
		{
			Future<Integer> future = map.get(job_id);
			
			try 
			{
				int result = future.get();
				System.out.println("job with id "+job_id+" terminated");
				JobsAccess.set_job_ended(job_id, false);
				
			} 
			catch (Exception e) 
			{
				JobsAccess.set_job_ended(job_id, true);
				e.printStackTrace();
			}
		}
	}
	
	private void init_configs() 
	{
		new DBConfig();
	}
	
	private void shutDown() 
	{
		threadPool.shutdown();
	}


	public static void main(String[] args) {
		JobHandler handler = new JobHandler();
		handler.shutDown();
	}
}

