package base;

import static org.junit.Assert.assertTrue;

import java.sql.ResultSet;
import java.sql.SQLException;

import org.junit.Test;


public class DBLinkTest {

	@Test
	public void testStatement() 
	{
		ResultSet rs = DBLink.executeQuery("SELECT * FROM core_files WHERE id='2103'");
		try 
		{
			while (rs.next()) 
			{
				assertTrue("Data Entity ID should be '10080'",rs.getInt("data_entity_id") == 10080);
				assertTrue("Flag should be '0'",rs.getInt("flag") == 0);
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
	}
}
