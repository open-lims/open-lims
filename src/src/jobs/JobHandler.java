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
	
	private Map<Integer, Future<Integer>> threadPoolMap = new HashMap<Integer,Future<Integer>>();
	
	private boolean running = true;
	
	private int id;
	
	public JobHandler(int id) 
	{
		this.id = id;
		init_configs();
		
		ServicesAccess.set_status(id, 1);
		
		while(running)
		{
			int status = ServicesAccess.get_status(id);
			System.out.println("status: "+status);
			if(status == 1)
			{ //alles ok
				clear_finished_jobs();
				add_new_jobs();
				give_lifesigns();
			}
			else if(status == 2)
			{ //soft shutdown
				System.out.println("soft shutdown");
				//alle unbearbeiteten jobs entfernen
				Integer[] pending_jobs = JobsAccess.get_pending_jobs();
				for (int i = 0; i < pending_jobs.length; i++) {
					System.out.println("removing job "+pending_jobs[i]+" from queue");
					JobsAccess.set_job_status(pending_jobs[i], 5);
				}
				//pool shutdown sobald alle fertig
				threadPool.shutdown();
				break;
			}
			else if(status == 3)
			{ //hard shutdown
				System.out.println("hard shutdown");
				//alle threads killen, pool shutdown	
				threadPool.shutdownNow();
				break;
			}
			else if(status == 4)
			{ //fehler
				System.out.println("error!");
				break;
			}			
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
			threadPoolMap.put(job_id, future);
			JobsAccess.set_job_status(job_id, 1);
		}
	}
	
	private void clear_finished_jobs()
	{
		LinkedList<Integer> to_remove = new LinkedList<Integer>();
		
		for (Integer job_id : threadPoolMap.keySet()) 
		{
			Future<Integer> future = threadPoolMap.get(job_id);
			
			try 
			{
				int result = future.get();
				System.out.println("job with id "+job_id+" terminated with code "+result);
				JobsAccess.set_job_ended(job_id, false);
				to_remove.add(job_id);
			} 
			catch (Exception e) 
			{
				JobsAccess.set_job_ended(job_id, true);
				e.printStackTrace();
			}
		}
		
		for (Integer job_id : to_remove) 
		{
			threadPoolMap.remove(job_id);
		}
	}
	
	private void give_lifesigns() 
	{
		//give lifesigns for open jobs
		for (Integer job_id : threadPoolMap.keySet()) 
		{
			JobsAccess.set_lifesign(job_id);
		}
		
		//give lifesign for job handler
		ServicesAccess.set_lifesign(id);
	}
	
	private void init_configs() 
	{
		new DBConfig();
		new JobConfig();
	}
	
	public static void main(String[] args) {
		new JobHandler(1);
	}
}

