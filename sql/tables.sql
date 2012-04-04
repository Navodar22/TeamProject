drop table if exists user;
create table user(
	id int not null auto_increment primary key,
	ais_number int,
	first_name varchar(100),
	last_name varchar(100)
);

insert into user values ('1', '50774', 'Samuel', 'Kelemen');

drop table if exists state;
create table state(
	id int not null auto_increment primary key,
	name varchar(100) not null
); 

insert into state values ('1', 'Nový');
insert into state values ('2', 'Schválený');
insert into state values ('3', 'Zamietnutý');
insert into state values ('4', 'Pripravený na realizáciu');
insert into state values ('5', 'V realizácii');
insert into state values ('6', 'Úspešne ukončený');
insert into state values ('7', 'Neúspešne ukončený');
insert into state values ('8', 'Zrušený');


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
	user_id int not null,	

	foreign key (user_id) references user(id) on delete restrict on update cascade	
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
	participation int not null,
	fonds varchar(300),

	-- time variables
	start timestamp,
	end timestamp,

	foreign key (project_id) references project(id) on delete restrict on update cascade,
	foreign key (institute_id) references institute(id) on delete restrict on update cascade,
	foreign key (state_id) references state(id) on delete restrict on update cascade
);