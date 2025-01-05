CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`80.7.51.177` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_available_picks` AS
    SELECT 
        `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`,
        `a`.`lms_available_picks_game` AS `lms_available_picks_game`,
        `a`.`lms_available_picks_team` AS `lms_available_picks_team`,
        `a`.`lms_available_picks_count` AS `lms_available_picks_count`,
        `t`.`lms_team_name` AS `lms_team_name`
    FROM
        (`lastmanl_lms`.`lms_available_picks` `a`
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`a`.`lms_available_picks_team` = `t`.`lms_team_id`))