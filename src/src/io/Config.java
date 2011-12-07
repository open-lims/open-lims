package io;

import java.io.BufferedReader;
import java.io.DataInputStream;
import java.io.FileInputStream;
import java.io.InputStreamReader;
import java.util.HashMap;

/**
 * Basic config IO.
 * @author Roman Quiring
 */
public class Config 
{
	
	private static String[] allowedKeys = {"dbms", 
										   "sqlDriver", 
										   "dbServer", 
										   "dbPort", 
										   "dbName", 
										   "dbUser", 
										   "dbPassword", 
										   "csvSeparator", 
										   "csvNewline",
										   "baseFolder"};
	
	private static HashMap<String, String> config = new HashMap<String, String>(allowedKeys.length);
	
	/**
	 * Static constructor.
	 */
	static
	{
		for (int i = 0; i < allowedKeys.length; i++) 
		{
			config.put(allowedKeys[i], null);
		}
		parseConfig(config);
	}
	
	/**
	 * Parses the config file to a HashMap.
	 * Only the keys that are already present in the HashMap will be assigned a value.
	 */
	private static void parseConfig(HashMap<String, String> config)
	{
		try
		{
			FileInputStream fstream = new FileInputStream("config.conf");
			DataInputStream in = new DataInputStream(fstream);
			InputStreamReader isr = new InputStreamReader(in);
			BufferedReader br = new BufferedReader(isr);
			String line;
			while ((line = br.readLine()) != null)
			{
				if(line.startsWith("#") || line.isEmpty())
				{
					continue;
				}
				
				String[] pair = line.split(":", 2);
				if(pair.length != 2)
				{
					continue;
				}
				String key = pair[0].trim();
				String value = pair[1].trim();
				if(config.containsKey(key))
				{
					config.put(key, value);
				}
			}
			br.close();
			isr.close();
			in.close();
			fstream.close();
		}
		catch (Exception e)
		{
			System.err.println("Error parsing config file!");
			e.printStackTrace();
		}
	}
	
	/**
	 * Returns the configuration value for a specific parameter.
	 * @param parameter the parameter.
	 * @return the value of the parameter.
	 */
	public static String getConfig(String parameter)
	{
		return config.get(parameter);
	}
		
}
