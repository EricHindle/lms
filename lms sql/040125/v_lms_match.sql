CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`82.9.248.136` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_match` AS
    SELECT 
        `m`.`lms_match_id` AS `lms_match_id`,
        `m`.`lms_match_weekno` AS `lms_match_weekno`,
        `m`.`lms_match_ha` AS `lms_match_ha`,
        `m`.`lms_match_calendar` AS `lms_match_calendar`,
        `w`.`lms_week` AS `lms_week`,
        `w`.`lms_year` AS `lms_year`,
        `w`.`lms_week_calendar` AS `lms_week_calendar`,
        `m`.`lms_match_team` AS `lms_match_team`,
        `m`.`lms_match_date` AS `lms_match_date`,
        `m`.`lms_match_result` AS `lms_match_result`,
        `t`.`lms_team_name` AS `lms_team_name`,
        `o`.`lms_team_name` AS `lms_match_opp`,
        `w`.`lms_week_start` AS `lms_week_start`
    FROM
        (((`lastmanl_lms`.`lms_match` `m`
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`m`.`lms_match_team` = `t`.`lms_team_id`))
        JOIN `lastmanl_lms`.`lms_week` `w` ON (`m`.`lms_match_weekno` = `w`.`lms_week_no`
            AND `m`.`lms_match_calendar` = `w`.`lms_week_calendar`))
        JOIN `lastmanl_lms`.`lms_team` `o` ON (`m`.`lms_match_opp` = `o`.`lms_team_id`))