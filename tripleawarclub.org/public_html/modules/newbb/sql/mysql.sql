-- phpMyAdmin SQL Dump
-- version 2.6.4-pl4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 07, 2006 at 01:26 PM
-- Server version: 5.0.16
-- PHP Version: 5.1.0
-- 
-- NewBB 4.02
-- 
-- 
-- Database: `cbb`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_archive`
-- 

CREATE TABLE `bb_archive` (
  `topic_id`  INT(8)      UNSIGNED  NOT NULL DEFAULT '0',
  `post_id`   INT(10)      UNSIGNED NOT NULL DEFAULT '0',
  `post_text` TEXT                  NOT NULL
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_attachments`
-- 

CREATE TABLE `bb_attachments` (
  `attach_id`   INT(8)      UNSIGNED   NOT NULL AUTO_INCREMENT,
  `post_id`     INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `name_saved`  VARCHAR(255)           NOT NULL DEFAULT '',
  `name_disp`   VARCHAR(255)           NOT NULL DEFAULT '',
  `mimetype`    VARCHAR(255)           NOT NULL DEFAULT '',
  `online`      TINYINT(1)    UNSIGNED NOT NULL DEFAULT '1',
  `attach_time` INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `download`    INT(10)    UNSIGNED    NOT NULL DEFAULT '0',

  PRIMARY KEY (`attach_id`),
  KEY `post_id`    (`post_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_categories`
-- 

CREATE TABLE `bb_categories` (
  `cat_id`          SMALLINT(3)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_image`       VARCHAR(50)           NOT NULL DEFAULT '',
  `cat_title`       VARCHAR(100)          NOT NULL DEFAULT '',
  `cat_description` TEXT                  NOT NULL,
  `cat_order`       SMALLINT(3)  UNSIGNED NOT NULL DEFAULT '99',
  `cat_url`         VARCHAR(255)          NOT NULL DEFAULT '',

  PRIMARY KEY (`cat_id`),
  KEY `cat_order`  (`cat_order`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_digest`
-- 

CREATE TABLE `bb_digest` (
  `digest_id`      INT(8)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `digest_time`    INT(10)    UNSIGNED  NOT NULL DEFAULT '0',
  `digest_content` TEXT,

  PRIMARY KEY (`digest_id`),
  KEY `digest_time` (`digest_time`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_forums`
-- 

CREATE TABLE `bb_forums` (
  `forum_id`           SMALLINT(4)  UNSIGNED  NOT NULL AUTO_INCREMENT,
  `forum_name`         VARCHAR(150)           NOT NULL DEFAULT '',
  `forum_desc`         TEXT,
  `parent_forum`       SMALLINT(4)  UNSIGNED  NOT NULL DEFAULT '0',
  `forum_moderator`    VARCHAR(255)           NOT NULL DEFAULT '',
  `forum_topics`       INT(8)      UNSIGNED   NOT NULL DEFAULT '0',
  `forum_posts`        INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `forum_last_post_id` INT(10)      UNSIGNED  NOT NULL DEFAULT '0',
  `cat_id`             SMALLINT(3)  UNSIGNED  NOT NULL DEFAULT '0',
  `hot_threshold`      TINYINT(3)    UNSIGNED NOT NULL DEFAULT '10',
  `forum_order`        SMALLINT(4)  UNSIGNED  NOT NULL DEFAULT '99',
  `attach_maxkb`       SMALLINT(3)  UNSIGNED  NOT NULL DEFAULT '1000',
  `attach_ext`         VARCHAR(255)           NOT NULL DEFAULT '',
  `allow_polls`        TINYINT(1)    UNSIGNED NOT NULL DEFAULT '0',

  PRIMARY KEY (`forum_id`),
  KEY `forum_last_post_id`  (`forum_last_post_id`),
  KEY `cat_forum`      (`cat_id`, `forum_order`),
  KEY `forum_order`    (`forum_order`),
  KEY `cat_id`        (`cat_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_moderates`
-- 

CREATE TABLE `bb_moderates` (
  `mod_id`    INT(10)    UNSIGNED   NOT NULL AUTO_INCREMENT,
  `mod_start` INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `mod_end`   INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `mod_desc`  VARCHAR(255)          NOT NULL DEFAULT '',
  `uid`       INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `ip`        VARCHAR(32)           NOT NULL DEFAULT '',
  `forum_id`  SMALLINT(4)  UNSIGNED NOT NULL DEFAULT '0',

  PRIMARY KEY (`mod_id`),
  KEY `uid`    (`uid`),
  KEY `mod_end`  (`mod_end`),
  KEY `forum_id`  (`forum_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_online`
-- 

CREATE TABLE `bb_online` (
  `online_forum`   INT(10)    UNSIGNED  NOT NULL DEFAULT '0',
  `online_topic`   INT(8)      UNSIGNED NOT NULL DEFAULT '0',
  `online_uid`     INT(10)    UNSIGNED  NOT NULL DEFAULT '0',
  `online_uname`   VARCHAR(255)         NOT NULL DEFAULT '',
  `online_ip`      VARCHAR(32)          NOT NULL DEFAULT '',
  `online_updated` INT(10)    UNSIGNED  NOT NULL DEFAULT '0',

  KEY `online_forum`  (`online_forum`),
  KEY `online_topic`  (`online_topic`),
  KEY `online_updated`  (`online_updated`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_posts`
-- 

CREATE TABLE `bb_posts` (
  `post_id`       INT(10) UNSIGNED       NOT NULL AUTO_INCREMENT,
  `pid`           INT(10) UNSIGNED       NOT NULL DEFAULT '0',
  `topic_id`      INT(8) UNSIGNED        NOT NULL DEFAULT '0',
  `forum_id`      SMALLINT(4)  UNSIGNED  NOT NULL DEFAULT '0',
  `post_time`     INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `uid`           INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `poster_name`   VARCHAR(255)           NOT NULL DEFAULT '',
  `poster_ip`     VARCHAR(45)            NOT NULL DEFAULT '',
  `subject`       VARCHAR(255)           NOT NULL DEFAULT '',
  `icon`          VARCHAR(25)            NOT NULL DEFAULT '',
  `attachsig`     TINYINT(1) UNSIGNED    NOT NULL DEFAULT '0',
  `approved`      SMALLINT(2)            NOT NULL DEFAULT '1',
  `post_karma`    INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `attachment`    TEXT,
  `require_reply` TINYINT(1)    UNSIGNED NOT NULL DEFAULT '0',

  PRIMARY KEY (`post_id`),
  KEY `uid`          (`uid`),
  KEY `pid`          (`pid`),
  KEY `forum_id`        (`forum_id`),
  KEY `topic_id`        (`topic_id`),
  KEY `subject`        (`subject`(40)),
  KEY `forumid_uid`      (`forum_id`, `uid`),
  KEY `topicid_uid`      (`topic_id`, `uid`),
  KEY `post_time`        (`post_time`),
  KEY `approved`        (`approved`),
  KEY `forumid_approved_postid`  (`forum_id`, `approved`, `post_id`),
  FULLTEXT KEY `search`    (`subject`(64))
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_posts_text`
-- 

CREATE TABLE `bb_posts_text` (
  `post_id`   INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `post_text` TEXT,
  `post_edit` TEXT,

  `dohtml`    TINYINT(1)    UNSIGNED NOT NULL DEFAULT '0',
  `dosmiley`  TINYINT(1)    UNSIGNED NOT NULL DEFAULT '1',
  `doxcode`   TINYINT(1)    UNSIGNED NOT NULL DEFAULT '1',
  `doimage`   TINYINT(1)    UNSIGNED NOT NULL DEFAULT '1',
  `dobr`      TINYINT(1)    UNSIGNED NOT NULL DEFAULT '1',

  PRIMARY KEY (`post_id`),
  FULLTEXT KEY `search` (`post_text`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_reads_forum`
-- 

CREATE TABLE `bb_reads_forum` (
  `read_id`   INT(10)    UNSIGNED   NOT NULL AUTO_INCREMENT,
  `uid`       INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `read_time` INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `read_item` SMALLINT(4)  UNSIGNED NOT NULL DEFAULT '0',
  `post_id`   INT(10)    UNSIGNED   NOT NULL DEFAULT '0',

  PRIMARY KEY (`read_id`),
  KEY `uid`      (`uid`),
  KEY `read_item`    (`read_item`),
  KEY `post_id`    (`post_id`),
  KEY `read_item_uid`    (`read_item`, `uid`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_reads_topic`
-- 

CREATE TABLE `bb_reads_topic` (
  `read_id`   INT(10)    UNSIGNED  NOT NULL AUTO_INCREMENT,
  `uid`       INT(10)    UNSIGNED  NOT NULL DEFAULT '0',
  `read_time` INT(10)    UNSIGNED  NOT NULL DEFAULT '0',
  `read_item` INT(8)      UNSIGNED NOT NULL DEFAULT '0',
  `post_id`   INT(10)    UNSIGNED  NOT NULL DEFAULT '0',

  PRIMARY KEY (`read_id`),
  KEY `uid`      (`uid`),
  KEY `read_item`    (`read_item`),
  KEY `post_id`    (`post_id`),
  KEY `read_item_uid`    (`read_item`, `uid`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_report`
-- 

CREATE TABLE `bb_report` (
  `report_id`     INT(8)      UNSIGNED   NOT NULL AUTO_INCREMENT,
  `post_id`       INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `reporter_uid`  INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `reporter_ip`   INT(11)                NOT NULL DEFAULT '0',
  `report_time`   INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `report_text`   VARCHAR(255)           NOT NULL DEFAULT '',
  `report_result` TINYINT(1)    UNSIGNED NOT NULL DEFAULT '0',
  `report_memo`   VARCHAR(255)           NOT NULL DEFAULT '',

  PRIMARY KEY (`report_id`),
  KEY `post_id`    (`post_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_topics`
-- 

CREATE TABLE `bb_topics` (
  `topic_id`           INT(8)        UNSIGNED   NOT NULL AUTO_INCREMENT,
  `topic_title`        VARCHAR(255)             NOT NULL DEFAULT '',
  `topic_poster`       INT(10)      UNSIGNED    NOT NULL DEFAULT '0',
  `topic_time`         INT(10)      UNSIGNED    NOT NULL DEFAULT '0',
  `topic_views`        INT(10)      UNSIGNED    NOT NULL DEFAULT '0',
  `topic_replies`      MEDIUMINT(8)    UNSIGNED NOT NULL DEFAULT '0',
  `topic_last_post_id` INT(8)        UNSIGNED   NOT NULL DEFAULT '0',
  `forum_id`           SMALLINT(4)    UNSIGNED  NOT NULL DEFAULT '0',
  `topic_status`       TINYINT(1)      UNSIGNED NOT NULL DEFAULT '0',
  `type_id`            SMALLINT(4)    UNSIGNED  NOT NULL DEFAULT '0',
  `topic_sticky`       TINYINT(1)      UNSIGNED NOT NULL DEFAULT '0',
  `topic_digest`       TINYINT(1)      UNSIGNED NOT NULL DEFAULT '0',
  `digest_time`        INT(10)      UNSIGNED    NOT NULL DEFAULT '0',
  `approved`           TINYINT(2)               NOT NULL DEFAULT '1',
  `poster_name`        VARCHAR(255)             NOT NULL DEFAULT '',
  `rating`             DOUBLE(6, 4)             NOT NULL DEFAULT '0.0000',
  `votes`              INT(11)      UNSIGNED    NOT NULL DEFAULT '0',
  `topic_haspoll`      TINYINT(1)      UNSIGNED NOT NULL DEFAULT '0',
  `poll_id`            MEDIUMINT(8)    UNSIGNED NOT NULL DEFAULT '0',
  `topic_tags`         VARCHAR(255)             NOT NULL DEFAULT '',

  PRIMARY KEY (`topic_id`),
  KEY `forum_id`    (`forum_id`),
  KEY `topic_last_post_id`  (`topic_last_post_id`),
  KEY `topic_poster`  (`topic_poster`),
  KEY `topic_forum`  (`topic_id`, `forum_id`),
  KEY `topic_sticky`  (`topic_sticky`),
  KEY `topic_digest`  (`topic_digest`),
  KEY `digest_time`  (`digest_time`),
  KEY `topic_time`    (`topic_time`),
  KEY `approved`    (`approved`),
  KEY `type_id`    (`type_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_votedata`
-- 

CREATE TABLE `bb_votedata` (
  `ratingid`        INT(11)      UNSIGNED    NOT NULL AUTO_INCREMENT,
  `topic_id`        INT(8)        UNSIGNED   NOT NULL DEFAULT '0',
  `ratinguser`      INT(10)      UNSIGNED    NOT NULL DEFAULT '0',
  `rating`          TINYINT(3)      UNSIGNED NOT NULL DEFAULT '0',
  `ratinghostname`  VARCHAR(60)              NOT NULL DEFAULT '',
  `ratingtimestamp` INT(10)      UNSIGNED    NOT NULL DEFAULT '0',

  PRIMARY KEY (`ratingid`),
  KEY `ratinguser`    (`ratinguser`),
  KEY `ratinghostname`  (`ratinghostname`),
  KEY `topic_id`    (`topic_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_type`
-- 

CREATE TABLE `bb_type` (
  `type_id`          SMALLINT(4)    UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name`        VARCHAR(64)             NOT NULL DEFAULT '',
  `type_color`       VARCHAR(10)             NOT NULL DEFAULT '',
  `type_description` VARCHAR(255)            NOT NULL DEFAULT '',

  PRIMARY KEY (`type_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `bb_type_forum`
-- 

CREATE TABLE `bb_type_forum` (
  `tf_id`      MEDIUMINT(4)    UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id`    SMALLINT(4)    UNSIGNED  NOT NULL DEFAULT '0',
  `forum_id`   SMALLINT(4)    UNSIGNED  NOT NULL DEFAULT '0',
  `type_order` SMALLINT(4)    UNSIGNED  NOT NULL DEFAULT '99',

  PRIMARY KEY (`tf_id`),
  KEY `forum_id`    (`forum_id`),
  KEY `type_order`    (`type_order`)
)
  ENGINE = MyISAM;

CREATE TABLE `bb_user_stats` (
  `uid`           MEDIUMINT(8)  UNSIGNED NOT NULL DEFAULT '0',
  `user_topics`   INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `user_digests`  INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `user_posts`    INT(10)    UNSIGNED    NOT NULL DEFAULT '0',
  `user_lastpost` INT(10)    UNSIGNED    NOT NULL DEFAULT '0',

  UNIQUE KEY (`uid`)
)
  ENGINE = MyISAM;

CREATE TABLE `bb_stats` (
  `stats_id`     SMALLINT(4)           NOT NULL DEFAULT '0',
  `stats_value`  INT(10)    UNSIGNED   NOT NULL DEFAULT '0',
  `stats_type`   SMALLINT(2)  UNSIGNED NOT NULL DEFAULT '0',
  `stats_period` SMALLINT(2)  UNSIGNED NOT NULL DEFAULT '0',

  `time_update`  DATE                           DEFAULT NULL,
  `time_format`  VARCHAR(32)           NOT NULL DEFAULT '',

  KEY `stats_id`    (`stats_id`),
  KEY `stats_type`    (`stats_type`, `stats_period`)
)
  ENGINE = MyISAM;
