CREATE TABLE `lms_game_outcome` (
  `lml_game_outcome_id` int(11) NOT NULL,
  `lml_game_outcome_text` varchar(45) NOT NULL,
  PRIMARY KEY (`lml_game_outcome_id`),
  UNIQUE KEY `lml_game_outcome_id_UNIQUE` (`lml_game_outcome_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
