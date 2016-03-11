<?php
$table_setup = <<<EOD
CREATE TABLE categories (
	id smallint(6) unsigned NOT NULL,
	name varchar(100) unique,
	type varchar(30),
	date date,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE pages (
	id smallint(6) unsigned NOT NULL auto_increment,
	categoryname varchar(100) NULL,
	page varchar(75) NULL, 
	headline varchar(150) NULL,
	pagetype varchar(30) NULL,
	template varchar(40),
	meta text(100) NULL,
	priority decimal(3,2),
	articleids varchar(50),
	positions text(2000),
	controller varchar(30),
	published tinyint(1),
	cache tinyint(1) NULL,	
	minify tinyint(1) NULL,
	date timestamp NULL,
	lastupdate timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE content (
	id smallint(6) unsigned NOT NULL auto_increment,
	articlename varchar(50) NULL,
	#headline varchar(150) NULL,
	description varchar(500) NULL,	
	#position varchar(50),	
	published tinyint(1),
	type varchar(30) NULL,	
	user char(100) NULL,
	content varchar(50000) NULL,		
	date timestamp NULL,
	lastupdate timestamp NULL DEFAULT CURRENT_TIMESTAMP, # ON UPDATE CURRENT_TIMESTAMP
	link text(200) NULL,
	menu varchar(50) NULL,
	menutype varchar(50) NULL,
	image varchar(250) NULL,
	category varchar(100) NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB;


ALTER TABLE pages 
ADD CONSTRAINT FK_pages
FOREIGN KEY (categoryname) REFERENCES categories(name) 
ON UPDATE CASCADE
ON DELETE SET NULL;

#ALTER TABLE pages 
#DROP FOREIGN KEY FK_pages

CREATE TABLE searchindex (
	word varchar(50) unique,
	pagelist varchar(1000) NULL,
	articlelist varchar(1000) NULL,
	#type varchar(25),
	#weight int(5),
	PRIMARY KEY (word)
) ENGINE=InnoDB;


CREATE TABLE menus (
	id smallint(6) unsigned NOT NULL auto_increment,
	name varchar(50) NULL,
	data varchar(5000) NULL,
	dependencies varchar(500) NULL,	
	type varchar(50) NULL,	
	PRIMARY KEY (id)
    );

#user table 

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    userType ENUM('public','author','admin'),
    username VARCHAR(40) NOT NULL,
    email VARCHAR(50) NOT NULL,
    pass CHAR(40) NOT NULL,
	urls VARCHAR(1000) NOT NULL,
	image VARCHAR(100) NOT NULL,
    dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	status tinyint(1) NULL,
    PRIMARY KEY (id),
    UNIQUE (username),
    UNIQUE (email),
    INDEX login(email,pass)
    );
	
EOD;
?>