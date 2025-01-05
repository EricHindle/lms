CREATE TABLE `lms_match` (
  `lms_match_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_match_weekno` varchar(6) NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_date` datetime NOT NULL,
  `lms_match_result` char(1) NOT NULL DEFAULT '',
  `lms_match_league` int(11) NOT NULL DEFAULT 0,
  `lms_match_opp` int(11) DEFAULT NULL,
  `lms_match_ha` char(1) NOT NULL DEFAULT '',
  `lms_match_calendar` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_match_id`),
  KEY `fk_weekno_idx` (`lms_match_weekno`),
  KEY `fk_team_idx` (`lms_match_team`),
  CONSTRAINT `fk_lms_match_lms_team1` FOREIGN KEY (`lms_match_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=53198 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
