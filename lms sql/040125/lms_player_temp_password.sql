CREATE TABLE `lms_player_temp_password` (
  `lms_player_id` int(11) NOT NULL,
  `lms_player_temp_password` varchar(100) NOT NULL,
  PRIMARY KEY (`lms_player_id`),
  UNIQUE KEY `idlms_player_id_UNIQUE` (`lms_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
