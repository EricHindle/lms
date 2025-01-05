CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`82.9.248.136` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_player_picks` AS
    SELECT 
        `p`.`lms_pick_player_id` AS `lms_pick_player_id`,
        `p`.`lms_pick_game_id` AS `lms_pick_game_id`,
        `p`.`lms_pick_match_id` AS `lms_pick_match_id`,
        `p`.`lms_pick_wl` AS `lms_pick_wl`,
        `pl`.`lms_player_screen_name` AS `lms_player_screen_name`,
        `g`.`lms_game_name` AS `lms_game_name`,
        `m`.`lms_match_weekno` AS `lms_match_weekno`,
        `m`.`lms_match_ha` AS `lms_match_ha`,
        `m`.`lms_match_league` AS `lms_match_league`,
        `t`.`lms_team_id` AS `lms_team_id`,
        `t`.`lms_team_name` AS `lms_team_name`,
        `g`.`lms_game_status` AS `lms_game_status`,
        `g`.`lms_game_status_text` AS `lms_game_status_text`,
        `g`.`lms_game_calendar` AS `lms_game_calendar`,
        `w`.`lms_week` AS `lms_week`,
        `w`.`lms_year` AS `lms_year`,
        `m`.`lms_match_date` AS `lms_match_date`,
        `m`.`lms_match_result` AS `lms_match_result`,
        `o`.`lms_team_name` AS `lms_match_opp`
    FROM
        ((((((`lastmanl_lms`.`lms_pick` `p`
        JOIN `lastmanl_lms`.`lms_player` `pl` ON (`p`.`lms_pick_player_id` = `pl`.`lms_player_id`))
        JOIN `lastmanl_lms`.`v_lms_game` `g` ON (`p`.`lms_pick_game_id` = `g`.`lms_game_id`))
        JOIN `lastmanl_lms`.`lms_match` `m` ON (`p`.`lms_pick_match_id` = `m`.`lms_match_id`))
        JOIN `lastmanl_lms`.`lms_team` `t` ON (`m`.`lms_match_team` = `t`.`lms_team_id`))
        JOIN `lastmanl_lms`.`lms_week` `w` ON (`w`.`lms_week_no` = `m`.`lms_match_weekno`
            AND `g`.`lms_game_calendar` = `w`.`lms_week_calendar`))
        JOIN `lastmanl_lms`.`lms_team` `o` ON (`m`.`lms_match_opp` = `o`.`lms_team_id`))