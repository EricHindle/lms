CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`82.9.248.136` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_league_calendar` AS
    SELECT 
        `l`.`lms_league_id` AS `lms_league_id`,
        `l`.`lms_league_name` AS `lms_league_name`,
        `l`.`lms_league_abbr` AS `lms_league_abbr`,
        `l`.`lms_league_supported` AS `lms_league_supported`,
        `l`.`lms_league_api_id` AS `lms_league_api_id`,
        `l`.`lms_league_current_calendar` AS `lms_league_current_calendar`,
        `c`.`lms_calendar_id` AS `lms_calendar_id`,
        `c`.`lms_calendar_name` AS `lms_calendar_name`,
        `c`.`lms_calendar_season` AS `lms_calendar_season`,
        `c`.`lms_calendar_current_week` AS `lms_calendar_current_week`,
        `c`.`lms_calendar_select_week` AS `lms_calendar_select_week`,
        `c`.`lms_calendar_api_season` AS `lms_calendar_api_season`,
        `s`.`lms_week_deadline` AS `lms_select_deadline`
    FROM
        ((`lastmanl_lms`.`lms_league` `l`
        JOIN `lastmanl_lms`.`lms_calendar` `c` ON (`l`.`lms_league_current_calendar` = `c`.`lms_calendar_id`))
        LEFT JOIN `lastmanl_lms`.`lms_week` `s` ON (`s`.`lms_week` = `c`.`lms_calendar_select_week`
            AND `s`.`lms_week_calendar` = `c`.`lms_calendar_id`))