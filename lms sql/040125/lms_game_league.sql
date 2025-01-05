CREATE TABLE `lms_game_league` (
  `lms_game_league_game_id` int(11) NOT NULL,
  `lms_game_league_league_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_game_league_game_id`,`lms_game_league_league_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
