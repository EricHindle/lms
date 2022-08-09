CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_available_picks` AS select `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`,`a`.`lms_available_picks_game` AS `lms_available_picks_game`,`a`.`lms_available_picks_team` AS `lms_available_picks_team`,`t`.`lms_team_name` AS `lms_team_name` from (`lastmanl_lms`.`lms_available_picks` `a` join `lastmanl_lms`.`lms_team` `t` on(`a`.`lms_available_picks_team` = `t`.`lms_team_id`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_fixture` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_team` AS `lms_match_team`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`m`.`lms_match_league` AS `lms_match_league`,`m`.`lms_match_opp` AS `lms_match_opp`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`t`.`lms_team_active` AS `lms_team_active`,`t`.`lms_team_wins` AS `lms_team_wins`,`t`.`lms_team_abbr` AS `lms_team_abbr`,`o`.`lms_team_abbr` AS `lms_opp_abbr` from ((`lastmanl_lms`.`lms_match` `m` join `lastmanl_lms`.`lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) left join `lastmanl_lms`.`lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_game` AS select `g`.`lms_game_id` AS `lms_game_id`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_manager` AS `lms_game_manager`,`g`.`lms_game_code` AS `lms_game_code`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`w`.`lms_week_start` AS `lms_week_start`,`gl`.`lms_game_status_text` AS `lms_game_status_text` from (((`lastmanl_lms`.`lms_game` `g` join `lastmanl_lms`.`lms_player` `p` on(`g`.`lms_game_manager` = `p`.`lms_player_id`)) join `lastmanl_lms`.`lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno`)) join `lastmanl_lms`.`lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_match` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_ha` AS `lms_match_ha`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`m`.`lms_match_team` AS `lms_match_team`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`t`.`lms_team_name` AS `lms_team_name`,`o`.`lms_team_name` AS `lms_match_opp`,`w`.`lms_week_start` AS `lms_week_start` from (((`lastmanl_lms`.`lms_match` `m` join `lastmanl_lms`.`lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lastmanl_lms`.`lms_week` `w` on(`m`.`lms_match_weekno` = `w`.`lms_week_no`)) join `lastmanl_lms`.`lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_player_games` AS select `gp`.`lms_player_id` AS `lms_player_id`,`gp`.`lms_game_id` AS `lms_game_id`,`gp`.`lms_game_player_status` AS `lms_game_player_status`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`p`.`lms_active` AS `lms_active`,`pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`,`gl`.`lms_game_status_text` AS `lms_game_status_text`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year` from (((((`lastmanl_lms`.`lms_game_player` `gp` join `lastmanl_lms`.`lms_game` `g` on(`gp`.`lms_game_id` = `g`.`lms_game_id`)) join `lastmanl_lms`.`lms_player` `p` on(`gp`.`lms_player_id` = `p`.`lms_player_id`)) join `lastmanl_lms`.`lms_game_player_status` `pl` on(`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`)) join `lastmanl_lms`.`lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`)) join `lastmanl_lms`.`lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_player_picks` AS select `p`.`lms_pick_player_id` AS `lms_pick_player_id`,`p`.`lms_pick_game_id` AS `lms_pick_game_id`,`p`.`lms_pick_match_id` AS `lms_pick_match_id`,`p`.`lms_pick_wl` AS `lms_pick_wl`,`pl`.`lms_player_screen_name` AS `lms_player_screen_name`,`g`.`lms_game_name` AS `lms_game_name`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`g`.`lms_game_status_text` AS `lms_game_status_text`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result` from (((((`lastmanl_lms`.`lms_pick` `p` join `lastmanl_lms`.`lms_player` `pl` on(`p`.`lms_pick_player_id` = `pl`.`lms_player_id`)) join `lastmanl_lms`.`v_lms_game` `g` on(`p`.`lms_pick_game_id` = `g`.`lms_game_id`)) join `lastmanl_lms`.`lms_match` `m` on(`p`.`lms_pick_match_id` = `m`.`lms_match_id`)) join `lastmanl_lms`.`lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lastmanl_lms`.`lms_week` `w` on(`w`.`lms_week_no` = `m`.`lms_match_weekno`));
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_results` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_league` AS `lms_match_league`,`m`.`lms_match_ha` AS `lms_match_ha`,`t`.`lms_team_id` AS `home_team_id`,`t`.`lms_team_name` AS `home_team_name`,`t`.`lms_team_abbr` AS `home_team_abbr`,`o`.`lms_team_id` AS `away_team_id`,`o`.`lms_team_name` AS `away_team_name`,`o`.`lms_team_abbr` AS `away_team_abbr`,`rh`.`lms_match_team_score` AS `home_score`,`ra`.`lms_match_team_score` AS `away_score`,`rh`.`lms_match_team_wl` AS `home_result`,`ra`.`lms_match_team_wl` AS `away_result` from ((((`lastmanl_lms`.`lms_match` `m` join `lastmanl_lms`.`lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lastmanl_lms`.`lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) left join `lastmanl_lms`.`lms_results` `rh` on(`m`.`lms_match_date` = `rh`.`lms_match_date` and `t`.`lms_team_id` = `rh`.`lms_match_team`)) left join `lastmanl_lms`.`lms_results` `ra` on(`m`.`lms_match_date` = `ra`.`lms_match_date` and `o`.`lms_team_id` = `ra`.`lms_match_team`)) where `m`.`lms_match_ha` = 'h' order by `m`.`lms_match_weekno`,`m`.`lms_match_date`,`t`.`lms_team_name`;
CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `lastmanl_lms`.`v_lms_team_lookup` AS select `a`.`lms_team_abbr_abbr` AS `lms_team_abbr_abbr`,`a`.`lms_team_abbr_team_id` AS `lms_team_abbr_team_id`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`t`.`lms_team_active` AS `lms_team_active`,`t`.`lms_team_wins` AS `lms_team_wins`,`t`.`lms_team_abbr` AS `lms_team_abbr` from (`lastmanl_lms`.`lms_team_abbr` `a` join `lastmanl_lms`.`lms_team` `t` on(`a`.`lms_team_abbr_team_id` = `t`.`lms_team_id`));
