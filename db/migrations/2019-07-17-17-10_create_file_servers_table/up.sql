create table file_servers
(
    id       int auto_increment
        primary key,
    name     varchar(64)       null,
    ip       varchar(16)       null,
    port     int(6)            null,
    url      varchar(255)      not null,
    status   tinyint default 0 not null,
    space    bigint            null,
    workload int               null,
    constraint file_servers_name_uindex
        unique (name),
    constraint file_servers_url_uindex
        unique (url)
);