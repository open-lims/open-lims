package metadata;

import metadata.interfaces.MetadataReader;

/**
 * Implementation of a metadata reader for use in biochemistry.
 * Checks which channel was used during sequencing.
 * @author Roman Quiring
 */
public class BiochemistryMetadataReader implements MetadataReader{

	private int channel;
	
	public String get_metadata_begin() 
	{
		return "BEGIN IMAGE INFO";
	}

	public String get_metadata_end() 
	{
		return "END IMAGE INFO";
	}

	public void read_metadata(String[] metadata) 
	{
		if(metadata[1].contains("Cyanine 5"))
		{
			channel = 1;
		}
		else
		{
			channel = 2;
		}
	}
	
	public int get_channel()
	{
		return channel;
	}

}
