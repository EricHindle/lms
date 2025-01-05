CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl`@`localhost` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_fixture` AS
    SELECT 
        `m`.`lms_match_id` AS `lms_match_id`,
        `m`.`lms_match_weekno` AS `lms_match_weekno`,
        `m`.`lms_match_team` AS `lms_match_team`,
        `m`.`lms_match_date` AS `lms_match_date`,
        `m`.`lms_match_result` AS `lms_match_result`,
        `m`.`lms_match_league` AS `lms_match_league`,
        `m`.`lms_match_opp` AS `lms_match_opp`,
        `t`.`lms_team_id` AS `lms_team_id`,
        `t`.`lms_team_name` AS `lms_team_name`,
        `t`.`lms_team_active` AS `lms_team_active`,
        `t`.`lms_team_wins` AS `lms_team_wins`,
        `t`.`lms_team_abbr` AS `lms_team_abbr`,
        `o`.`lms_team_abbr` AS `lms_opp_abbr`
    FROM
        ((`lastmanl_lms`.`lms_match` `m`
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`m`.`lms_match_team` = `t`.`lms_team_id`))
        LEFT JOIN `lastmanl_lms`.`lms_team` `o` ON (`m`.`lms_match_opp` = `o`.`lms_team_id`))