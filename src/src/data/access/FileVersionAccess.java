package data.access;

import java.sql.ResultSet;
import java.sql.SQLException;

import base.DBLink;

public class FileVersionAccess {

	private int id;
	private int toid;
	private String name;
	private int version;
	private int size;
	private String checksum;
	private String datetime;
	private String comment;
	private int previous_version_id;
	private int internal_revision;
	private boolean current;
	private String file_extension;
	private int owner_id;
	
	public FileVersionAccess(int file_version_id, int file_id)
	{
		String sql = "SELECT * FROM core_file_versions WHERE id='"+file_version_id+"' AND toid='"+file_id+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				this.id = rs.getInt("id");
				this.toid = rs.getInt("toid");
				this.name = rs.getString("name");
				this.version = rs.getInt("version");
				this.size = rs.getInt("size");
				this.checksum = rs.getString("checksum");
				this.datetime = rs.getString("datetime");
				this.comment = rs.getString("comment");
				this.previous_version_id = rs.getInt("previous_version_id");
				this.internal_revision = rs.getInt("internal_revision");
				this.current = rs.getBoolean("current");
				this.file_extension = rs.getString("file_extension");
				this.owner_id = rs.getInt("owner_id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
	}
	
	public int get_id()
	{
		return this.id;
	}
	
	public int get_toid()
	{
		return this.toid;
	}
	
	public String get_name()
	{
		return this.name;
	}
	
	public int get_version()
	{
		return this.version;
	}
	
	public int get_size()
	{
		return this.size;
	}
	
	public String get_checksum()
	{
		return this.checksum;
	}
	
	public String get_datetime() 
	{
		return this.datetime;
	}
	
	public String get_comment()
	{
		return this.comment;
	}
	
	public int get_previous_version_id()
	{
		return this.previous_version_id;
	}
	
	public int get_internal_revision()
	{
		return this.internal_revision;
	}
	
	public boolean get_current()
	{
		return this.current;
	}
	
	public String get_file_extension()
	{
		return this.file_extension;
	}
	
	public int get_owner_id()
	{
		return this.owner_id;
	}
	
	
	public static int get_current_file_version_id_by_file_id(int file_id)
	{
		String sql = "SELECT id FROM core_file_versions WHERE toid='"+file_id+"' AND current='t'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				return rs.getInt("id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return -1;
	}
	
	public static int get_file_version_id_by_file_id_and_internal_revision(int file_id, int internal_revision)
	{
		String sql = "SELECT id FROM core_file_versions WHERE toid='"+file_id+"' AND internal_revision='"+internal_revision+"'";
		ResultSet rs = DBLink.executeQuery(sql);
		try 
		{
			while (rs.next()) 
			{
				return rs.getInt("id");
			}
		} 
		catch (SQLException e) 
		{
			e.printStackTrace();
		}
		return -1;
	}
}
