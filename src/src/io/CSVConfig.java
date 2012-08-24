package io;

import java.util.HashMap;

/**
 * CSV config IO.
 * @author Roman Quiring
 */
public class CSVConfig extends AbstractConfig{

	static String[] allowedKeys = {"csvSeparatorSource",
									"csvNewlineSource",
									"csvSeparatorTarget",
									"csvNewlineTarget",
									"csvDataBegin",
									"csvDataEnd"};
	
	private static HashMap<String, String> config = new HashMap<String, String>(allowedKeys.length);
	
	/**
	 * Constructor.
	 */
	public CSVConfig() 
	{

		super(allowedKeys, "CSVConfig.conf", config);
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
