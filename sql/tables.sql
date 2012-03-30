drop table if exists user;
create table user(
	id int not null auto_increment primary key,
	ais_number int,
	first_name varchar(100),
	last_name varchar(100)
);

drop table if exists state;
create table state(
	id int not null auto_increment primary key,
	name varchar(100) not null
); 

insert into state values ('1', 'nový');

drop table if exists faculty;
create table faculty(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	del boolean default false
);

drop table if exists insitute;
create table institute(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	faculty_id int not null,
	del boolean default false,

	foreign key (faculty_id) references faculty(id) on delete cascade on update cascade
);


drop table if exists project;
create table project(
	id int not null auto_increment primary key,
	name varchar(200) not null,
	description mediumtext not null,
	note varchar(300),
	cost float not null,
	user_id int not null,	
	state_id int not null default 1,
	faculty_id int not null,
	institute_id int not null,
	start timestamp,
	end timestamp,

	foreign key (user_id) references user(id) on delete restrict on update cascade,
	foreign key (state_id) references state(id) on delete restrict on update cascade,
	foreign key (faculty_id) references faculty(id) on delete restrict on update cascade,
	foreign key (institute_id) references institute(id) on delete restrict on update cascade
	
);