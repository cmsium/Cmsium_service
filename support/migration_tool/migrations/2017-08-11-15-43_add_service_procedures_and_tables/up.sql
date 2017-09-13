create table file_servers
(
	server_id varchar(32) not null
		primary key,
	server_name varchar(255) not null,
	path varchar(255) not null,
	status tinyint null,
	disk_space bigint null,
	constraint file_servers_server_id_uindex
		unique (server_id),
	constraint file_servers_path_uindex
		unique (path)
);

CREATE PROCEDURE getFileServerData()
  BEGIN
    SELECT * FROM file_servers WHERE status = 1;
  END;

CREATE PROCEDURE changeFileServerData(IN ServerId VARCHAR(32), IN ServerStatus TINYINT, IN FreeSpace FLOAT)
  BEGIN
    UPDATE file_servers SET status=ServerStatus, disk_space=FreeSpace WHERE server_id=ServerId;
  END;
