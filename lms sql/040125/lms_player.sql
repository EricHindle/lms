CREATE TABLE `lms_player` (
  `lms_player_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_player_login` varchar(255) NOT NULL,
  `lms_player_password` varchar(100) NOT NULL,
  `lms_player_forename` varchar(45) NOT NULL DEFAULT '',
  `lms_player_surname` varchar(45) NOT NULL DEFAULT '',
  `lms_player_screen_name` varchar(100) NOT NULL DEFAULT '',
  `lms_player_email` varchar(250) NOT NULL DEFAULT '',
  `lms_access` int(11) NOT NULL DEFAULT 0,
  `lms_active` tinyint(1) DEFAULT 1,
  `lms_player_send_email` tinyint(1) DEFAULT 1,
  `lms_player_created` datetime DEFAULT NULL,
  `lms_player_mobile` varchar(45) DEFAULT '0',
  `lms_player_email_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_player_id`),
  UNIQUE KEY `lms_player_id_UNIQUE` (`lms_player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1819 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
