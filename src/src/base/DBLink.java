package base;

import io.DBConfig;

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
	private static String dbms = DBConfig.get_config("dbms");;
	private static String sqlDriver = DBConfig.get_config("sqlDriver");;
	private static String dbServer = DBConfig.get_config("dbServer");
	private static String dbPort = DBConfig.get_config("dbPort");
	private static String dbName = DBConfig.get_config("dbName");;
	private static String dbUser = DBConfig.get_config("dbUser");;
	private static String dbPassword = DBConfig.get_config("dbPassword");;
	
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
		try 
		{
			Statement statement = connection.createStatement();
			results = statement.executeQuery(sql);
			connection.close();
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		
		return results;
	}
	
	/**
	 * Executes a SQL Update on the database.
	 * @param sql the SQL statement.
	 */
	public static void updateQuery(String sql)
	{
		Connection connection = getDBConnection();
		try 
		{
			Statement statement = connection.createStatement();
			statement.executeUpdate(sql);
			connection.close();
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
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
						
			PreparedStatement ps = connection.prepareStatement(sql,type_constant,concur_constant);  
			
			results = ps.executeQuery();
			
			connection.close();
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
