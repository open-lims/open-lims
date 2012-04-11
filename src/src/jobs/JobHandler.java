package jobs;

import io.DBConfig;
import io.JobConfig;

import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
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
		
	private PrintWriter log;
	
	private int id;
	
	public JobHandler(int id) 
	{
		this.id = id;
		
		init_configs();
		
		if(init_log())
		{
			ServicesAccess.set_status(id, 1);

			write_to_log("Job Handler started.");
			
			handle_jobs();
		
			write_to_log("Job Handler terminated.");
			log.close();
			
			ServicesAccess.set_status(id, 0);
		}
		else
		{
			ServicesAccess.set_status(id, 4);
		}
	
		System.exit(0);
	}
	
	private void handle_jobs()
	{
		for(;;)
		{
			int status = ServicesAccess.get_status(id);
			if(status == 1)
			{ //everything ok
				clear_finished_jobs();
				add_new_jobs();
				give_lifesigns();
			}
			else if(status == 2)
			{ //soft shutdown
				write_to_log("attempting to shut down softly.");
								
				//finish all jobs in queue and shutdown pool
				threadPool.shutdown();
				
				//reset status of pending jobs to "new"
				Integer[] pending_jobs = JobsAccess.get_pending_jobs();
				for (int i = 0; i < pending_jobs.length; i++) 
				{
					int pending_job_id = pending_jobs[i];
					JobsAccess.set_job_status(pending_job_id, 0);
				}
				
				//wait for running jobs to terminate
				while(threadPoolMap.size() > 0) 
				{
					clear_finished_jobs();
				}

				break;
			}
			else if(status == 3)
			{ //hard shutdown
				write_to_log("attempting to shut down hard.");
				
				//kill threads, shutdown pool 	
				threadPool.shutdownNow();
				
				for (Integer job_id : threadPoolMap.keySet()) 
				{
					if(JobsAccess.get_job_status(job_id) == 1)
					{ //reset status of pending jobs to "new"
						JobsAccess.set_job_status(job_id, 0); 
					}
					else if(JobsAccess.get_job_status(job_id) == 3)
					{ //set status of finished jobs to "finished"
						JobsAccess.set_job_ended(job_id, false);
					}
					else
					{ //set status of running and faulty jobs to "error"
						JobsAccess.set_job_ended(job_id, true);
					}
				}
				break;
			}		
			try {
				Thread.sleep(5000);
			} 
			catch (InterruptedException e) 
			{
				write_to_log("the handler thread was interrupted!");
				e.printStackTrace(log);
				ServicesAccess.set_status(id, 4);
				return;
			}
		}
	}
	
	private void add_new_jobs() 
	{
		Integer[] new_jobs = JobsAccess.check_for_new_jobs();
		
		for (int i = 0; i < new_jobs.length; i++) 
		{
			int job_id = new_jobs[i];
			
			write_to_log("adding job "+job_id+" to thread pool.");

			Job job = new Job(job_id);
			JobsAccess.set_job_status(job_id, 1);
			Future<Integer> future = threadPool.submit(job);
			threadPoolMap.put(job_id, future);
		}
	}
	
	private void clear_finished_jobs()
	{
		LinkedList<Integer> to_remove = new LinkedList<Integer>();
		
		for (Integer job_id : threadPoolMap.keySet()) 
		{
			//if the status is 0, a soft shutdown was requested. delete job from queue.
			if(JobsAccess.get_job_status(job_id) == 0) 
			{
				write_to_log("removing job "+job_id+" from queue.");
				to_remove.add(job_id);
			}
			
			Future<Integer> future = threadPoolMap.get(job_id);
			
			//prevent blocking
			if(!future.isDone()) 
			{
				continue;
			}
			
			try 
			{
				int result = future.get();
				if(result == 3)
				{
					write_to_log("job "+job_id+" terminated successfully.");
					JobsAccess.set_job_ended(job_id, false);
				}
				else if(result == 4)
				{
					write_to_log("job "+job_id+" terminated with an error.");
					JobsAccess.set_job_ended(job_id, true);
				}
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
	
	private boolean init_log()
	{
		try 
		{
			FileWriter logFile = new FileWriter(JobConfig.get_config("logDir")+"/job_log.txt");
			log = new PrintWriter(logFile);
			return true;
		} 
		catch (IOException e1) 
		{
			e1.printStackTrace();
		}
		return false;
	}
	
	private void write_to_log(String text) 
	{
		DateFormat dateFormat = new SimpleDateFormat("yyyy/MM/dd HH:mm:ss");
		Date date = new Date();
		log.println(dateFormat.format(date)+": "+text);
//		System.out.println(dateFormat.format(date)+": "+text);
	}
	
	public static void main(String[] args) 
	{
		new JobHandler(1); //TODO assign id => args 1
	}
}

