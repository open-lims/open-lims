package jobs;

import java.util.concurrent.Callable;


public class Job implements Callable<Integer> {

	private int job_id;
	
	public Job(int id) 
	{
		this.job_id = id;
	}

	@Override
	public Integer call() throws Exception {
		
		JobsAccess.set_job_started(job_id);
		
		return job_id;
	}
	

	

}
