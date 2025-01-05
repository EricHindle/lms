CREATE TABLE `lms_info` (
  `lms_info_id` varchar(24) NOT NULL,
  `lms_info_value` varchar(256) DEFAULT '',
  `lms_info_enc` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`lms_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
