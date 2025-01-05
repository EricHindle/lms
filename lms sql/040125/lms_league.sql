CREATE TABLE `lms_league` (
  `lms_league_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_league_name` varchar(255) NOT NULL DEFAULT '',
  `lms_league_abbr` varchar(4) NOT NULL DEFAULT '',
  `lms_league_supported` tinyint(1) DEFAULT 0,
  `lms_league_api_id` int(11) NOT NULL DEFAULT 0,
  `lms_league_current_calendar` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_league_id`),
  UNIQUE KEY `lms_league_id_UNIQUE` (`lms_league_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
