CREATE TABLE `lms_team` (
  `lms_team_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_team_name` varchar(45) NOT NULL,
  `lms_team_active` tinyint(1) DEFAULT 1,
  `lms_team_wins` int(2) DEFAULT 0,
  `lms_team_abbr` char(3) NOT NULL DEFAULT '',
  `lms_team_api_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
