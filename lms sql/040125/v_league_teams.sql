CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`80.7.51.177` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_league_teams` AS
    SELECT 
        `l`.`lms_league_id` AS `lms_league_id`,
        `l`.`lms_league_name` AS `lms_league_name`,
        `l`.`lms_league_abbr` AS `lms_league_abbr`,
        `l`.`lms_league_supported` AS `lms_league_supported`,
        `l`.`lms_league_api_id` AS `lms_league_api_id`,
        `l`.`lms_league_current_calendar` AS `lms_league_current_calendar`,
        `t`.`lms_team_id` AS `lms_team_id`,
        `t`.`lms_team_name` AS `lms_team_name`,
        `t`.`lms_team_active` AS `lms_team_active`,
        `t`.`lms_team_abbr` AS `lms_team_abbr`,
        `t`.`lms_team_api_id` AS `lms_team_api_id`
    FROM
        ((`lastmanl_lms`.`lms_league` `l`
        JOIN `lastmanl_lms`.`lms_league_team` `lt` ON (`l`.`lms_league_id` = `lt`.`lms_league_team_league_id`))
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`lt`.`lms_league_team_team_id` = `t`.`lms_team_id`))