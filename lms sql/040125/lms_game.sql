CREATE TABLE `lms_game` (
  `lms_game_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_game_start_wkno` varchar(6) NOT NULL,
  `lms_game_name` varchar(45) NOT NULL,
  `lms_game_status` int(1) NOT NULL DEFAULT 1,
  `lms_game_week_count` int(11) NOT NULL DEFAULT 0,
  `lms_game_total_players` int(11) NOT NULL DEFAULT 0,
  `lms_game_still_active` int(11) NOT NULL DEFAULT 0,
  `lms_game_manager` int(11) NOT NULL,
  `lms_game_code` varchar(6) NOT NULL,
  `lms_game_calendar` int(11) NOT NULL,
  `lms_game_pick_count` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lms_game_id`),
  UNIQUE KEY `lms_game_id_UNIQUE` (`lms_game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
