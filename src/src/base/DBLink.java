package base;

import io.Config;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/**
 * Basic Database IO.
 * @author Roman Quiring
 */
public class DBLink 
{
	private static String dbms = Config.getConfig("dbms");;
	private static String sqlDriver = Config.getConfig("sqlDriver");;
	private static String dbServer = Config.getConfig("dbServer");
	private static String dbPort = Config.getConfig("dbPort");
	private static String dbName = Config.getConfig("dbName");;
	private static String dbUser = Config.getConfig("dbUser");;
	private static String dbPassword = Config.getConfig("dbPassword");;
	
	/**
	 * Static constructor.
	 */
	static
	{
		initDB();
	}
	
	/**
	 * Initialises the connection to the Database.
	 */
	private static void initDB() {
		try 
		{
			Class.forName(sqlDriver);
		} 
		catch (ClassNotFoundException e) 
		{
			System.err.println("JDBC driver could not be found!");
			e.printStackTrace();
			return;
		}
		Connection connection = getDBConnection();
		try 
		{
			connection.close();
		} 
		catch (SQLException e) 
		{
			System.err.println("An error occurred while establishing a connection to the database!");
			e.printStackTrace();
		}
	}
	
	/**
	 * Returns a Connection object.
	 * @return Connection the connection object.
	 */
	private static Connection getDBConnection() {	
		Connection connection = null;
		try 
		{
			connection = DriverManager.getConnection("jdbc:"+dbms+"://"+dbServer+":"+dbPort+"/"+dbName, dbUser, dbPassword);
		} 
		catch (SQLException e) 
		{
			System.err.println("Connection to database failed! Make sure the specifications in the config file are correct.");
			e.printStackTrace();
		}
		return connection;
	}
	
	/**
	 * Executes a SQL Statement on the database.
	 * @param sql the SQL statement.
	 * @return a ResultSet containing the results.
	 */
	public static ResultSet executeQuery(String sql)
	{
		ResultSet results = null;
		Connection connection = getDBConnection();
		try {
			Statement statement = connection.createStatement();
			results = statement.executeQuery(sql);
		} catch (SQLException e) {
			e.printStackTrace();
		}
		return results;
	}
	
	/**
	 * Executes a SQL Statement on the database and accepts additional parameters-
	 * @param sql the SQL statement.
	 * @param type FORWARD_ONLY, SCROLL_SENSITIVE or SCROLL_INSENSITIVE
	 * @param concur READ_ONLY or UPDATABLE
	 * @returna ResultSet containing the results.
	 *
	 */ //TODO remove? 
	public static ResultSet executeQuery(String sql, String type, String concur)
	{
		ResultSet results = null;
		Connection connection = getDBConnection();
		try {
			int type_constant = -1;
			if(type.equals("FORWARD_ONLY")) 
			{
				type_constant = ResultSet.TYPE_FORWARD_ONLY;
			}
			else if(type.equals("SCROLL_SENSITIVE")) 
			{
				type_constant = ResultSet.TYPE_SCROLL_SENSITIVE;
			}
			else if(type.equals("SCROLL_INSENSITIVE")) 
			{
				type_constant = ResultSet.TYPE_SCROLL_INSENSITIVE;
			}
			
			int concur_constant = -1;
			if(concur.equals("READ_ONLY")) 
			{
				concur_constant = ResultSet.CONCUR_READ_ONLY;
			}
			else if(concur.equals("UPDATABLE")) 
			{
				concur_constant = ResultSet.CONCUR_UPDATABLE;
			}
			
			System.out.println(type_constant+" "+concur_constant);
			
			PreparedStatement ps = connection.prepareStatement(sql,type_constant,concur_constant);  
			
			results = ps.executeQuery(
					);
		} catch (SQLException e) {
			e.printStackTrace();
		}
		return results;
	}
	
	/**
	 * Streams a resource from the database.
	 * @param sql The SQL statement to execute.
	 * @param columnLabel the name of the column to select.
	 * @param binary indicates whether the stream should be binary. 
	 * @return an InputStream.
	 */ //TODO remove?
	public static InputStream getResourceAsStream(String sql, String columnLabel, boolean binary)
	{
		InputStream stream = null;
		try 
		{
			ResultSet results = DBLink.executeQuery(sql);
			while(results.next())
			{
				if(binary)
				{
					stream = results.getBinaryStream(columnLabel);
				}
				else
				{
					stream = results.getAsciiStream(columnLabel);
				}			
			}
		}
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return stream;
	}

}
