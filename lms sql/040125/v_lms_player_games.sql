CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `lastmanl_lmsadmin`@`82.9.248.136` 
    SQL SECURITY DEFINER
VIEW `lastmanl_lms`.`v_lms_player_games` AS
    SELECT 
        `gp`.`lms_player_id` AS `lms_player_id`,
        `gp`.`lms_game_id` AS `lms_game_id`,
        `gp`.`lms_game_player_status` AS `lms_game_player_status`,
        `gp`.`lms_game_player_elimination_week` AS `lms_game_player_elimination_week`,
        `o`.`lml_game_outcome_text` AS `lml_game_outcome_text`,
        `g`.`lms_game_name` AS `lms_game_name`,
        `g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,
        `g`.`lms_game_status` AS `lms_game_status`,
        `g`.`lms_game_total_players` AS `lms_game_total_players`,
        `g`.`lms_game_still_active` AS `lms_game_still_active`,
        `g`.`lms_game_week_count` AS `lms_game_week_count`,
        `g`.`lms_game_code` AS `lms_game_code`,
        `g`.`lms_game_calendar` AS `lms_game_calendar`,
        `p`.`lms_player_screen_name` AS `lms_player_screen_name`,
        `p`.`lms_active` AS `lms_active`,
        `pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`,
        `gl`.`lms_game_status_text` AS `lms_game_status_text`,
        `w`.`lms_week` AS `lms_week`,
        `w`.`lms_year` AS `lms_year`,
        `c`.`lms_calendar_season` AS `lms_calendar_season`,
        `c`.`lms_calendar_current_week` AS `lms_calendar_current_week`,
        `c`.`lms_calendar_select_week` AS `lms_calendar_select_week`,
        `s`.`lms_week_deadline` AS `lms_select_deadline`
    FROM
        ((((((((`lastmanl_lms`.`lms_game_player` `gp`
        JOIN `lastmanl_lms`.`lms_game` `g` ON (`gp`.`lms_game_id` = `g`.`lms_game_id`))
        JOIN `lastmanl_lms`.`lms_player` `p` ON (`gp`.`lms_player_id` = `p`.`lms_player_id`))
        JOIN `lastmanl_lms`.`lms_game_player_status` `pl` ON (`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`))
        JOIN `lastmanl_lms`.`lms_game_status` `gl` ON (`g`.`lms_game_status` = `gl`.`lms_game_status_id`))
        JOIN `lastmanl_lms`.`lms_week` `w` ON (`w`.`lms_week_no` = `g`.`lms_game_start_wkno`
            AND `w`.`lms_week_calendar` = `g`.`lms_game_calendar`))
        JOIN `lastmanl_lms`.`lms_game_outcome` `o` ON (`gp`.`lms_game_player_outcome` = `o`.`lml_game_outcome_id`))
        JOIN `lastmanl_lms`.`lms_calendar` `c` ON (`g`.`lms_game_calendar` = `c`.`lms_calendar_id`))
        JOIN `lastmanl_lms`.`lms_week` `s` ON (`s`.`lms_week` = `c`.`lms_calendar_select_week`
            AND `s`.`lms_week_calendar` = `c`.`lms_calendar_id`))