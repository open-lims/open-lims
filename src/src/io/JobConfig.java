package io;

import java.util.HashMap;

/**
 * Job config IO.
 * @author Roman Quiring
 */
public class JobConfig extends AbstractConfig {
	
	static String[] allowedKeys = {"binaryRoot", "logDir"};
	
	private static HashMap<String, String> config = new HashMap<String, String>(allowedKeys.length);
	
	/**
	* Constructor.
	*/
	public JobConfig() 
	{
		super(allowedKeys, "JobConfig.conf", config);
	}
	
	/**
	* Returns the configuration value for a specific parameter.
	* @param parameter the parameter.
	* @return the value of the parameter.
	*/
	public static String get_config(String parameter)
	{
		return config.get(parameter);
	}	
}
