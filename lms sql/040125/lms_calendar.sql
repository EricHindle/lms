CREATE TABLE `lms_calendar` (
  `lms_calendar_id` int(11) NOT NULL,
  `lms_calendar_name` varchar(45) NOT NULL,
  `lms_calendar_season` int(11) NOT NULL,
  `lms_calendar_current_week` int(11) NOT NULL DEFAULT 0,
  `lms_calendar_select_week` int(11) NOT NULL DEFAULT 0,
  `lms_calendar_api_season` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`lms_calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
