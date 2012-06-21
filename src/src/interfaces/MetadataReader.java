package interfaces;

/**
 * Interface for reading metadata from a csv header.
 * @author Roman Quiring
 */
public interface MetadataReader {

	/**
	 * Returns a String that indicates the start of a metadata section.
	 * @return String
	 */
	public String get_metadata_begin();
	
	/**
	 * Returns a String that indicates the end of a metadata section.
	 * @return String
	 */
	public String get_metadata_end();
	
	/**
	 * Evaluates the metadata section.
	 */
	public void read_metadata(String[] metadata);
	
}
