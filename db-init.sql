CREATE DATABASE g2ldb;
CONNECT g2ldb;

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
	`id` int UNSIGNED NOT NULL PRIMARY KEY auto_increment,
	`language_name_string` varchar(20) NOT NULL, 
	`alternate_name` varchar(20),
	UNIQUE id (id), KEY id_2 (id));

-- The systems expects lower case names. 
-- If a language has an uppercase name, it won't be possible to request it.
INSERT INTO languages (`language_name_string`) VALUES ('hindi');
INSERT INTO languages (`language_name_string`, `alternate_name`) VALUES ('spanish', 'espanol');
INSERT INTO languages (`language_name_string`) VALUES ('na\'vi');

DROP TABLE IF EXISTS `interpreters`;
CREATE TABLE `interpreters` (
	`id` int UNSIGNED NOT NULL PRIMARY KEY auto_increment,
	`first` varchar(15) NOT NULL,
	`last` varchar(15) NOT NULL,
	`g2lphone` varchar(15) NOT NULL,
	`altphone` varchar(15),
	`email` varchar(30),
	`language1` varchar(15) NOT NULL, -- TODO: Make foreign key
	`language2` varchar(15),
	`active` bool NOT NULL,
	UNIQUE `id` (`id`));

INSERT INTO interpreters (`first`, `last`, `g2lphone`, `language1`, `active`)
	VALUES ('Gold', 'Star', '12066979562', 'Spanish', true);

INSERT INTO interpreters (`first`, `last`, `g2lphone`, `language1`, `active`)
	VALUES ('Other', 'Phone', '12064847264', 'Hindi', true);

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
	`id` int UNSIGNED NOT NULL PRIMARY KEY auto_increment,
	`requester_phone` varchar(15) NOT NULL,
	`language` varchar(15) NOT NULL,
	`filled_by` int UNSIGNED, -- These will be null if the request is not filled
	`call_command_sent` DATETIME default NULL,
	`finish_recieved` DATETIME default NULL, 
	`call_duration` int UNSIGNED, -- In milliseconds
	FOREIGN KEY (`filled_by`) REFERENCES interpreters(`id`)); -- Should I do something like CASCADE for ON UPDATE or ON DELETE?

DROP TABLE IF EXISTS `requests_sent`;
CREATE TABLE `requests_sent` (
	`request_id` int UNSIGNED NOT NULL,
	`interpreter_id` int UNSIGNED NOT NULL,
	`time-stamp` TIMESTAMP default NOW(),
	INDEX(`request_id`),
	FOREIGN KEY (`interpreter_id`) REFERENCES interpreters(`id`),
	FOREIGN KEY (`request_id`) REFERENCES requests(`id`) ON UPDATE CASCADE ON DELETE CASCADE);

-- This table might need to change if clients can request time-slots
DROP TABLE IF EXISTS `events_rec`;
CREATE TABLE `events_rec` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) NOT NULL,
  `rec_type` varchar(64) NOT NULL,
  `event_pid` int(11) NOT NULL,
  `event_length` int(11) NOT NULL,
  `interpreter_id` int UNSIGNED NOT NULL,
  `language_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`interpreter_id`) REFERENCES interpreters(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`interpreter_id`) REFERENCES langauges(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Something required by mate:
-- Create this table in your database if you want to use show/hide columns or order columns.
--
CREATE TABLE IF NOT EXISTS `mate_columns` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `mate_user_id` varchar(75) collate utf8_unicode_ci NOT NULL,
  `mate_var_prefix` varchar(100) collate utf8_unicode_ci NOT NULL,
  `mate_column` varchar(75) collate utf8_unicode_ci NOT NULL,
  `hidden` varchar(3) collate utf8_unicode_ci NOT NULL default 'No',
  `order_num` mediumint(4) unsigned NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mate_user_id` (`mate_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
