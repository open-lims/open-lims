package io;

import java.io.BufferedReader;
import java.io.DataInputStream;
import java.io.FileInputStream;
import java.io.InputStreamReader;
import java.util.HashMap;

/**
 * Abstract config IO.
 * @author Roman Quiring
 */
public class AbstractConfig {
	
	protected AbstractConfig(String[]allowedKeys, String configFile, HashMap<String, String> config)
	{		
		for (int i = 0; i < allowedKeys.length; i++) 
		{
			config.put(allowedKeys[i], null);
		}
		parse_config(config, configFile);
	}
	
	/**
	 * Parses the config file to a HashMap.
	 * Only the keys that are already present in the HashMap will be assigned a value.
	 */
	private void parse_config(HashMap<String, String> config, String config_file)
	{
		try
		{
			FileInputStream fstream = new FileInputStream(config_file);
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
				if(value.isEmpty())
				{
					value = null;
				}
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
	
}
