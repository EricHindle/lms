CREATE TABLE `lms_results` (
  `lms_match_date` datetime NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_team_score` int(11) DEFAULT NULL,
  `lms_match_team_wl` char(1) DEFAULT NULL,
  `lms_match_status` varchar(4) DEFAULT '',
  PRIMARY KEY (`lms_match_date`,`lms_match_team`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
