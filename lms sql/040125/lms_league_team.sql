CREATE TABLE `lms_league_team` (
  `lms_league_team_league_id` int(11) NOT NULL,
  `lms_league_team_team_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_league_team_league_id`,`lms_league_team_team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
