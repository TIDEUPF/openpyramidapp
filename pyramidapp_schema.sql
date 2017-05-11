/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `data` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(50) COLLATE utf8_bin DEFAULT '0',
  `page` varchar(50) COLLATE utf8_bin DEFAULT '0',
  `sname` varchar(50) COLLATE utf8_bin DEFAULT '0',
  `type` varchar(50) COLLATE utf8_bin DEFAULT '0',
  `level` int(11) DEFAULT '0',
  `group_id` int(11) DEFAULT '0',
  `data` text COLLATE utf8_bin NOT NULL,
  `date` datetime NOT NULL,
  `origin` char(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=698391 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '1',
  `pid` int(11) NOT NULL DEFAULT '1',
  `sid` char(50) COLLATE utf8_bin DEFAULT '0',
  `room` char(50) COLLATE utf8_bin DEFAULT '0',
  `message` varchar(8192) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '0',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2047 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `sid` varchar(100) COLLATE utf8_bin NOT NULL,
  `feedback` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fid_sid` (`fid`,`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flow` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `fdes` text NOT NULL,
  `fcname` varchar(255) NOT NULL,
  `estudent` varchar(255) NOT NULL,
  `nostupergrp` int(11) NOT NULL,
  `levels` int(11) NOT NULL,
  `pyramid_size` int(11) NOT NULL,
  `pyramid_minsize` int(11) NOT NULL,
  `expected_students` int(11) NOT NULL DEFAULT '120',
  `noresponse` int(11) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `question_timeout` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_timeout` int(10) unsigned NOT NULL DEFAULT '0',
  `hardtimer_question` int(11) NOT NULL DEFAULT '240',
  `hardtimer_rating` int(11) NOT NULL DEFAULT '120',
  `answer_submit_required_percentage` int(11) NOT NULL DEFAULT '60',
  `rating_required_percentage` int(11) NOT NULL DEFAULT '60',
  `start_timestamp` int(11) NOT NULL DEFAULT '0',
  `question` varchar(16384) NOT NULL DEFAULT 'Please, write a question',
  `ch` int(11) NOT NULL DEFAULT '1',
  `sync` int(11) NOT NULL DEFAULT '1',
  `multi_py` int(11) NOT NULL DEFAULT '1',
  `n_selected_answers` int(11) NOT NULL DEFAULT '1',
  `random_selection` int(11) NOT NULL DEFAULT '1',
  `json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB AUTO_INCREMENT=15096 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flow_available_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `sid` char(255) COLLATE utf8_bin NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fid_sid` (`fid`,`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=264071 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flow_student` (
  `fs_id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '1',
  `sid` varchar(255) NOT NULL,
  `fs_answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `skip` tinyint(3) unsigned DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fs_id`),
  UNIQUE KEY `fid_sid` (`fid`,`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=1467 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flow_student_rating` (
  `flow_student_rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `fsr_fid` int(11) NOT NULL,
  `fsr_pid` int(11) NOT NULL DEFAULT '1',
  `fsr_sid` varchar(255) NOT NULL,
  `fsr_level` int(11) NOT NULL,
  `fsr_group_id` varchar(255) NOT NULL,
  `fsr_rating` double NOT NULL,
  `fsr_to_whom_rated_id` varchar(255) NOT NULL,
  `fsr_datetime` datetime NOT NULL,
  `skip` int(10) unsigned NOT NULL DEFAULT '0',
  `check` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`flow_student_rating_id`),
  UNIQUE KEY `fsr_fid` (`fsr_fid`,`fsr_sid`,`fsr_level`,`fsr_group_id`,`skip`,`check`,`fsr_pid`)
) ENGINE=InnoDB AUTO_INCREMENT=7453 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ldshake_editor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `sectoken` varchar(256) NOT NULL,
  `json` mediumtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_id` (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1196 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pyramid_groups` (
  `pg_fid` int(11) NOT NULL,
  `pg_pid` int(11) NOT NULL,
  `pg_group` varchar(8192) NOT NULL,
  `pg_level` int(11) NOT NULL,
  `pg_group_id` varchar(255) NOT NULL,
  `pg_combined_group_ids` varchar(255) NOT NULL,
  `pg_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_start_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_latecomers` varchar(4096) NOT NULL DEFAULT '',
  `pg_started` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `pg_fid_pg_pid_pg_group_pg_level` (`pg_fid`,`pg_pid`,`pg_level`,`pg_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pyramid_groups_og` (
  `pg_fid` int(11) NOT NULL,
  `pg_pid` int(11) NOT NULL,
  `pg_group` varchar(8192) NOT NULL,
  `pg_level` int(11) NOT NULL,
  `pg_group_id` varchar(255) NOT NULL,
  `pg_combined_group_ids` varchar(255) NOT NULL,
  `pg_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_start_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_latecomers` varchar(4096) NOT NULL DEFAULT '',
  `pg_started` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `pg_fid_pg_pid_pg_group_pg_level` (`pg_fid`,`pg_pid`,`pg_level`,`pg_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pyramid_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `sid` char(100) COLLATE utf8_bin NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_fid_pid_sid` (`id`,`fid`,`pid`,`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=2093 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `selected_answers` (
  `sa_fid` int(11) NOT NULL,
  `sa_pid` int(11) NOT NULL DEFAULT '1',
  `sa_level` int(11) NOT NULL,
  `sa_group_id` int(11) NOT NULL,
  `sa_selected_id` varchar(255) NOT NULL,
  `sa_rating_sum` double NOT NULL,
  `skip` int(10) unsigned NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`sa_fid`,`sa_level`,`sa_group_id`,`sa_pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `studentexcel` (
  `se_sid` varchar(100) NOT NULL,
  `se_sname` varchar(255) NOT NULL,
  `se_batch` varchar(100) NOT NULL,
  PRIMARY KEY (`se_sid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `sid` varchar(100) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `stime` datetime NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher` (
  `uname` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  PRIMARY KEY (`uname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
