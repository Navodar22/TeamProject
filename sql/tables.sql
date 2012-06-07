SET NAMES utf8;

CREATE TABLE `gui_acl_privileges` (
  `id` int(11) NOT NULL auto_increment,
  `key_name` varchar(64) collate utf8_czech_ci NOT NULL,
  `name` varchar(64) collate utf8_czech_ci NOT NULL,
  `comment` varchar(250) collate utf8_czech_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;


CREATE TABLE `gui_acl_resources` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `key_name` varchar(64) collate utf8_czech_ci NOT NULL,
  `name` varchar(64) collate utf8_czech_ci NOT NULL,
  `comment` varchar(250) collate utf8_czech_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;


ALTER TABLE `gui_acl_resources`
ADD CONSTRAINT `gui_acl_resources_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `gui_acl_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------


CREATE TABLE `gui_acl_roles` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `key_name` varchar(64) collate utf8_czech_ci NOT NULL,
  `name` varchar(64) collate utf8_czech_ci NOT NULL,
  `comment` varchar(250) collate utf8_czech_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=3 ;


ALTER TABLE `gui_acl_roles`
ADD CONSTRAINT `gui_acl_roles_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `gui_acl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------


CREATE TABLE `gui_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) collate utf8_czech_ci NOT NULL,
  `password` varchar(250) collate utf8_czech_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;


-- -----------------------------------------------------------------------


CREATE TABLE `gui_users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


ALTER TABLE `gui_users_roles`
ADD CONSTRAINT `gui_users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `gui_acl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gui_users_roles`
ADD CONSTRAINT `gui_users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `gui_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------

CREATE TABLE `gui_acl` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL,
  `privilege_id` int(11) default NULL,
  `resource_id` int(11) default NULL,
  `access` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;

ALTER TABLE `gui_acl`
ADD CONSTRAINT `gui_acl_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `gui_acl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gui_acl`
ADD CONSTRAINT `gui_acl_ibfk_1` FOREIGN KEY (`privilege_id`) REFERENCES `gui_acl_privileges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gui_acl`
ADD CONSTRAINT `gui_acl_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `gui_acl_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;





drop table if exists state;
create table state(
	id int not null auto_increment primary key,
	name varchar(100) not null
); 





drop table if exists faculty;
create table faculty(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	acronym varchar(10) not null
);





drop table if exists insitute;
create table institute(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	acronym varchar(10) not null,
	faculty_id int not null,
	money float,
	students int,

	foreign key (faculty_id) references faculty(id) on delete cascade on update cascade
);





drop table if exists project;
create table project(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	description mediumtext not null,
	user_id int not null,

	-- project data
	cost float default NULL,		/* money */
	approved_cost float default NULL,
	hr int default NULL,			/* human resource */
	approved_hr int default NULL,
	participation float default NULL,
	approved_participation float default NULL,

	start timestamp,
	approved_start timestamp NULL default NULL,
	end timestamp,
	approved_end timestamp NULL default NULL,

	foreign key (user_id) references gui_users(id) on delete restrict on update cascade	
);





drop table if exists project_institute;
create table project_institute(
	id int not null auto_increment primary key,

	-- reference variables
	project_id int not null,
	institute_id int not null,
	state_id int not null default 1,

	-- cost variables
	cost float not null,		/* money */
	hr int not null,			/* human resource */
	participation float not null,
	fonds varchar(300),

	-- time variables
	start timestamp,
	end timestamp,

	foreign key (project_id) references project(id) on delete cascade on update cascade,
	foreign key (institute_id) references institute(id) on delete cascade on update cascade,
	foreign key (state_id) references state(id) on delete restrict on update cascade
);





drop table if exists school;
create table school(
	id int not null auto_increment primary key,
	money float,
	students int
);




drop table if exists project_institute_date;
create table project_institute_date(
	id int not null auto_increment primary key,
	participation float,
	hr int,
	start timestamp,
	end timestamp,
	project_institute_id int not null,

	foreign key (project_institute_id) references project_institute(id) on delete cascade on update cascade
);


drop table if exists user_faculty;
create table user_faculty(
	id int not null auto_increment primary key,
	user_id int,
	faculty_id int,

	foreign key (user_id) references gui_users(id) on delete cascade on update cascade,
	foreign key (faculty_id) references faculty(id) on delete cascade on update cascade
);


drop table if exists user_institute;
create table user_institute(
	id int not null auto_increment primary key,
	user_id int,
	institute_id int,

	foreign key (user_id) references gui_users(id) on delete cascade on update cascade,
	foreign key (institute_id) references institute(id) on delete cascade on update cascade
);
