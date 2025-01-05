CREATE TABLE `lms_week` (
  `lms_week_no` varchar(6) NOT NULL,
  `lms_week_calendar` int(11) NOT NULL DEFAULT 0,
  `lms_week` int(2) NOT NULL,
  `lms_year` int(4) NOT NULL,
  `lms_week_start` datetime NOT NULL,
  `lms_week_end` datetime NOT NULL,
  `lms_week_deadline` datetime NOT NULL,
  `lms_week_state` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_week_no`,`lms_week_calendar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
