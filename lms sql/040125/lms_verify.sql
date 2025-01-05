CREATE TABLE `lms_verify` (
  `lms_verify_code` varchar(50) NOT NULL,
  `lms_verify_player` int(11) NOT NULL,
  `lms_verify_email` varchar(100) NOT NULL DEFAULT '',
  `lms_verify_date` datetime NOT NULL,
  `lms_verify_ok` tinyint(4) NOT NULL DEFAULT 0,
  `lms_create_date` datetime NOT NULL,
  PRIMARY KEY (`lms_verify_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
