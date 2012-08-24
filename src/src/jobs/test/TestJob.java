package jobs.test;

import java.io.FileWriter;
import java.io.PrintWriter;

public class TestJob {

	public TestJob(String num)
	{
		try {
			FileWriter logFile = new FileWriter("D:/open-lims/bin/test"+num+".txt");
			PrintWriter log = new PrintWriter(logFile);	
			log.println("test job "+num+" succeeded!");
			log.close();
			Thread.currentThread();
			Thread.sleep(5000);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void main(String[] args) {
		new TestJob(args[0]);
	}
}


