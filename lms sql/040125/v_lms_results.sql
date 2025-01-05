CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`80.7.51.177` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_results` AS
    SELECT 
        `m`.`lms_match_id` AS `lms_match_id`,
        `m`.`lms_match_weekno` AS `lms_match_weekno`,
        `m`.`lms_match_date` AS `lms_match_date`,
        `m`.`lms_match_league` AS `lms_match_league`,
        `m`.`lms_match_ha` AS `lms_match_ha`,
        `m`.`lms_match_calendar` AS `lms_match_calendar`,
        `t`.`lms_team_id` AS `home_team_id`,
        `t`.`lms_team_name` AS `home_team_name`,
        `t`.`lms_team_abbr` AS `home_team_abbr`,
        `o`.`lms_team_id` AS `away_team_id`,
        `o`.`lms_team_name` AS `away_team_name`,
        `o`.`lms_team_abbr` AS `away_team_abbr`,
        `rh`.`lms_match_team_score` AS `home_score`,
        `ra`.`lms_match_team_score` AS `away_score`,
        `rh`.`lms_match_team_wl` AS `home_result`,
        `ra`.`lms_match_team_wl` AS `away_result`,
        `rt`.`lms_result_type_desc` AS `home_result_type`,
        `rt`.`lms_result_type_noresult` AS `no_result`,
        `rh`.`lms_match_status` AS `match_status`
    FROM
        (((((`lastmanl_lms`.`lms_match` `m`
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`m`.`lms_match_team` = `t`.`lms_team_id`))
        JOIN `lastmanl_lms`.`lms_team` `o` ON (`m`.`lms_match_opp` = `o`.`lms_team_id`))
        LEFT JOIN `lastmanl_lms`.`lms_results` `rh` ON (`m`.`lms_match_date` = `rh`.`lms_match_date`
            AND `t`.`lms_team_id` = `rh`.`lms_match_team`))
        LEFT JOIN `lastmanl_lms`.`lms_results` `ra` ON (`m`.`lms_match_date` = `ra`.`lms_match_date`
            AND `o`.`lms_team_id` = `ra`.`lms_match_team`))
        LEFT JOIN `lastmanl_lms`.`lms_result_type` `rt` ON (`rh`.`lms_match_team_wl` = `rt`.`lms_result_type`))
    ORDER BY `m`.`lms_match_weekno` , `m`.`lms_match_date` , `t`.`lms_team_name`