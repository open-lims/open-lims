package io;

import java.util.HashMap;

/**
 * Database config IO.
 * @author Roman Quiring
 */
public class DBConfig extends AbstractConfig
{
	static String[] allowedKeys = {"dbms", 
			   "sqlDriver", 
			   "dbServer", 
			   "dbPort", 
			   "dbName", 
			   "dbUser", 
			   "dbPassword", 
			   "baseFolder"};
	
	private static HashMap<String, String> config = new HashMap<String, String>(allowedKeys.length);
	
	/**
	 * Constructor.
	 */
	public DBConfig() 
	{
		super(allowedKeys, "DBConfig.conf", config);
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
