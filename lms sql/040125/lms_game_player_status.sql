CREATE TABLE `lms_game_player_status` (
  `lms_game_player_status_id` int(11) NOT NULL,
  `lms_game_player_status_text` varchar(20) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`lms_game_player_status_id`),
  UNIQUE KEY `lms_game_player_status_id_UNIQUE` (`lms_game_player_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
