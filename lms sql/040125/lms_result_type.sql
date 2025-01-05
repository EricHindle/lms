CREATE TABLE `lms_result_type` (
  `lms_result_type` varchar(1) NOT NULL DEFAULT 'n',
  `lms_result_type_desc` varchar(45) NOT NULL DEFAULT 'not played',
  `lms_result_type_wl` varchar(1) NOT NULL DEFAULT '-',
  `lms_result_type_noresult` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_result_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
