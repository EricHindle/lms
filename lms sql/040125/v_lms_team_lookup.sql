CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl`@`localhost` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_team_lookup` AS
    SELECT 
        `a`.`lms_team_abbr_abbr` AS `lms_team_abbr_abbr`,
        `a`.`lms_team_abbr_team_id` AS `lms_team_abbr_team_id`,
        `t`.`lms_team_id` AS `lms_team_id`,
        `t`.`lms_team_name` AS `lms_team_name`,
        `t`.`lms_team_active` AS `lms_team_active`,
        `t`.`lms_team_wins` AS `lms_team_wins`,
        `t`.`lms_team_abbr` AS `lms_team_abbr`
    FROM
        (`lastmanl_lms`.`lms_team_abbr` `a`
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`a`.`lms_team_abbr_team_id` = `t`.`lms_team_id`))