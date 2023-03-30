-- MySQL dump 10.13  Distrib 8.0.29, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: lastmanl_lms
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary view structure for view `v_lms_league_calendar`
--

DROP TABLE IF EXISTS `v_lms_league_calendar`;
/*!50001 DROP VIEW IF EXISTS `v_lms_league_calendar`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_league_calendar` AS SELECT 
 1 AS `lms_league_id`,
 1 AS `lms_league_name`,
 1 AS `lms_league_abbr`,
 1 AS `lms_league_supported`,
 1 AS `lms_league_api_id`,
 1 AS `lms_league_current_calendar`,
 1 AS `lms_calendar_id`,
 1 AS `lms_calendar_name`,
 1 AS `lms_calendar_season`,
 1 AS `lms_calendar_current_week`,
 1 AS `lms_calendar_select_week`,
 1 AS `lms_select_deadline`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_team_lookup`
--

DROP TABLE IF EXISTS `v_lms_team_lookup`;
/*!50001 DROP VIEW IF EXISTS `v_lms_team_lookup`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_team_lookup` AS SELECT 
 1 AS `lms_team_abbr_abbr`,
 1 AS `lms_team_abbr_team_id`,
 1 AS `lms_team_id`,
 1 AS `lms_team_name`,
 1 AS `lms_team_active`,
 1 AS `lms_team_wins`,
 1 AS `lms_team_abbr`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_player_games`
--

DROP TABLE IF EXISTS `v_lms_player_games`;
/*!50001 DROP VIEW IF EXISTS `v_lms_player_games`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_player_games` AS SELECT 
 1 AS `lms_player_id`,
 1 AS `lms_game_id`,
 1 AS `lms_game_player_status`,
 1 AS `lms_game_player_elimination_week`,
 1 AS `lml_game_outcome_text`,
 1 AS `lms_game_name`,
 1 AS `lms_game_start_wkno`,
 1 AS `lms_game_status`,
 1 AS `lms_game_total_players`,
 1 AS `lms_game_still_active`,
 1 AS `lms_game_week_count`,
 1 AS `lms_game_code`,
 1 AS `lms_game_calendar`,
 1 AS `lms_player_screen_name`,
 1 AS `lms_active`,
 1 AS `lms_game_player_status_text`,
 1 AS `lms_game_status_text`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_calendar_season`,
 1 AS `lms_calendar_current_week`,
 1 AS `lms_calendar_select_week`,
 1 AS `lms_select_deadline`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_player_picks`
--

DROP TABLE IF EXISTS `v_lms_player_picks`;
/*!50001 DROP VIEW IF EXISTS `v_lms_player_picks`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_player_picks` AS SELECT 
 1 AS `lms_pick_player_id`,
 1 AS `lms_pick_game_id`,
 1 AS `lms_pick_match_id`,
 1 AS `lms_pick_wl`,
 1 AS `lms_player_screen_name`,
 1 AS `lms_game_name`,
 1 AS `lms_match_weekno`,
 1 AS `lms_match_ha`,
 1 AS `lms_match_league`,
 1 AS `lms_team_id`,
 1 AS `lms_team_name`,
 1 AS `lms_game_status`,
 1 AS `lms_game_status_text`,
 1 AS `lms_game_calendar`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_match_date`,
 1 AS `lms_match_result`,
 1 AS `lms_match_opp`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_fixture`
--

DROP TABLE IF EXISTS `v_lms_fixture`;
/*!50001 DROP VIEW IF EXISTS `v_lms_fixture`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_fixture` AS SELECT 
 1 AS `lms_match_id`,
 1 AS `lms_match_weekno`,
 1 AS `lms_match_team`,
 1 AS `lms_match_date`,
 1 AS `lms_match_result`,
 1 AS `lms_match_league`,
 1 AS `lms_match_opp`,
 1 AS `lms_team_id`,
 1 AS `lms_team_name`,
 1 AS `lms_team_active`,
 1 AS `lms_team_wins`,
 1 AS `lms_team_abbr`,
 1 AS `lms_opp_abbr`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_game`
--

DROP TABLE IF EXISTS `v_lms_game`;
/*!50001 DROP VIEW IF EXISTS `v_lms_game`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_game` AS SELECT 
 1 AS `lms_game_id`,
 1 AS `lms_game_start_wkno`,
 1 AS `lms_game_name`,
 1 AS `lms_game_status`,
 1 AS `lms_game_week_count`,
 1 AS `lms_game_total_players`,
 1 AS `lms_game_still_active`,
 1 AS `lms_game_manager`,
 1 AS `lms_game_code`,
 1 AS `lms_game_calendar`,
 1 AS `lms_player_screen_name`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_week_start`,
 1 AS `lms_game_status_text`,
 1 AS `lms_calendar_season`,
 1 AS `lms_calendar_current_week`,
 1 AS `lms_calendar_select_week`,
 1 AS `lms_select_deadline`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_results`
--

DROP TABLE IF EXISTS `v_lms_results`;
/*!50001 DROP VIEW IF EXISTS `v_lms_results`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_results` AS SELECT 
 1 AS `lms_match_id`,
 1 AS `lms_match_weekno`,
 1 AS `lms_match_date`,
 1 AS `lms_match_league`,
 1 AS `lms_match_ha`,
 1 AS `lms_match_calendar`,
 1 AS `home_team_id`,
 1 AS `home_team_name`,
 1 AS `home_team_abbr`,
 1 AS `away_team_id`,
 1 AS `away_team_name`,
 1 AS `away_team_abbr`,
 1 AS `home_score`,
 1 AS `away_score`,
 1 AS `home_result`,
 1 AS `away_result`,
 1 AS `home_result_type`,
 1 AS `no_result`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_match`
--

DROP TABLE IF EXISTS `v_lms_match`;
/*!50001 DROP VIEW IF EXISTS `v_lms_match`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_match` AS SELECT 
 1 AS `lms_match_id`,
 1 AS `lms_match_weekno`,
 1 AS `lms_match_ha`,
 1 AS `lms_match_calendar`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_week_calendar`,
 1 AS `lms_match_team`,
 1 AS `lms_match_date`,
 1 AS `lms_match_result`,
 1 AS `lms_team_name`,
 1 AS `lms_match_opp`,
 1 AS `lms_week_start`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_available_picks`
--

DROP TABLE IF EXISTS `v_lms_available_picks`;
/*!50001 DROP VIEW IF EXISTS `v_lms_available_picks`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_lms_available_picks` AS SELECT 
 1 AS `lms_available_picks_player_id`,
 1 AS `lms_available_picks_game`,
 1 AS `lms_available_picks_team`,
 1 AS `lms_team_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_lms_league_calendar`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_league_calendar`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_league_calendar` AS select `l`.`lms_league_id` AS `lms_league_id`,`l`.`lms_league_name` AS `lms_league_name`,`l`.`lms_league_abbr` AS `lms_league_abbr`,`l`.`lms_league_supported` AS `lms_league_supported`,`l`.`lms_league_api_id` AS `lms_league_api_id`,`l`.`lms_league_current_calendar` AS `lms_league_current_calendar`,`c`.`lms_calendar_id` AS `lms_calendar_id`,`c`.`lms_calendar_name` AS `lms_calendar_name`,`c`.`lms_calendar_season` AS `lms_calendar_season`,`c`.`lms_calendar_current_week` AS `lms_calendar_current_week`,`c`.`lms_calendar_select_week` AS `lms_calendar_select_week`,`s`.`lms_week_deadline` AS `lms_select_deadline` from ((`lms_league` `l` join `lms_calendar` `c` on((`l`.`lms_league_current_calendar` = `c`.`lms_calendar_id`))) left join `lms_week` `s` on(((`s`.`lms_week` = `c`.`lms_calendar_select_week`) and (`s`.`lms_week_calendar` = `c`.`lms_calendar_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_team_lookup`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_team_lookup`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_team_lookup` AS select `a`.`lms_team_abbr_abbr` AS `lms_team_abbr_abbr`,`a`.`lms_team_abbr_team_id` AS `lms_team_abbr_team_id`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`t`.`lms_team_active` AS `lms_team_active`,`t`.`lms_team_wins` AS `lms_team_wins`,`t`.`lms_team_abbr` AS `lms_team_abbr` from (`lms_team_abbr` `a` join `lms_team` `t` on((`a`.`lms_team_abbr_team_id` = `t`.`lms_team_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_player_games`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_player_games`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_player_games` AS select `gp`.`lms_player_id` AS `lms_player_id`,`gp`.`lms_game_id` AS `lms_game_id`,`gp`.`lms_game_player_status` AS `lms_game_player_status`,`gp`.`lms_game_player_elimination_week` AS `lms_game_player_elimination_week`,`o`.`lml_game_outcome_text` AS `lml_game_outcome_text`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`g`.`lms_game_code` AS `lms_game_code`,`g`.`lms_game_calendar` AS `lms_game_calendar`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`p`.`lms_active` AS `lms_active`,`pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`,`gl`.`lms_game_status_text` AS `lms_game_status_text`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`c`.`lms_calendar_season` AS `lms_calendar_season`,`c`.`lms_calendar_current_week` AS `lms_calendar_current_week`,`c`.`lms_calendar_select_week` AS `lms_calendar_select_week`,`s`.`lms_week_deadline` AS `lms_select_deadline` from ((((((((`lms_game_player` `gp` join `lms_game` `g` on((`gp`.`lms_game_id` = `g`.`lms_game_id`))) join `lms_player` `p` on((`gp`.`lms_player_id` = `p`.`lms_player_id`))) join `lms_game_player_status` `pl` on((`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`))) join `lms_game_status` `gl` on((`g`.`lms_game_status` = `gl`.`lms_game_status_id`))) join `lms_week` `w` on(((`w`.`lms_week_no` = `g`.`lms_game_start_wkno`) and (`w`.`lms_week_calendar` = `g`.`lms_game_calendar`)))) join `lms_game_outcome` `o` on((`gp`.`lms_game_player_outcome` = `o`.`lml_game_outcome_id`))) join `lms_calendar` `c` on((`g`.`lms_game_calendar` = `c`.`lms_calendar_id`))) join `lms_week` `s` on(((`s`.`lms_week` = `c`.`lms_calendar_select_week`) and (`s`.`lms_week_calendar` = `c`.`lms_calendar_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_player_picks`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_player_picks`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_player_picks` AS select `p`.`lms_pick_player_id` AS `lms_pick_player_id`,`p`.`lms_pick_game_id` AS `lms_pick_game_id`,`p`.`lms_pick_match_id` AS `lms_pick_match_id`,`p`.`lms_pick_wl` AS `lms_pick_wl`,`pl`.`lms_player_screen_name` AS `lms_player_screen_name`,`g`.`lms_game_name` AS `lms_game_name`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_ha` AS `lms_match_ha`,`m`.`lms_match_league` AS `lms_match_league`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_status_text` AS `lms_game_status_text`,`g`.`lms_game_calendar` AS `lms_game_calendar`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`o`.`lms_team_name` AS `lms_match_opp` from ((((((`lms_pick` `p` join `lms_player` `pl` on((`p`.`lms_pick_player_id` = `pl`.`lms_player_id`))) join `v_lms_game` `g` on((`p`.`lms_pick_game_id` = `g`.`lms_game_id`))) join `lms_match` `m` on((`p`.`lms_pick_match_id` = `m`.`lms_match_id`))) join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) join `lms_week` `w` on(((`w`.`lms_week_no` = `m`.`lms_match_weekno`) and (`g`.`lms_game_calendar` = `w`.`lms_week_calendar`)))) join `lms_team` `o` on((`m`.`lms_match_opp` = `o`.`lms_team_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_fixture`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_fixture`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_fixture` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_team` AS `lms_match_team`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`m`.`lms_match_league` AS `lms_match_league`,`m`.`lms_match_opp` AS `lms_match_opp`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`t`.`lms_team_active` AS `lms_team_active`,`t`.`lms_team_wins` AS `lms_team_wins`,`t`.`lms_team_abbr` AS `lms_team_abbr`,`o`.`lms_team_abbr` AS `lms_opp_abbr` from ((`lms_match` `m` join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) left join `lms_team` `o` on((`m`.`lms_match_opp` = `o`.`lms_team_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_game`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_game`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_game` AS select `g`.`lms_game_id` AS `lms_game_id`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_manager` AS `lms_game_manager`,`g`.`lms_game_code` AS `lms_game_code`,`g`.`lms_game_calendar` AS `lms_game_calendar`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`w`.`lms_week_start` AS `lms_week_start`,`gl`.`lms_game_status_text` AS `lms_game_status_text`,`c`.`lms_calendar_season` AS `lms_calendar_season`,`c`.`lms_calendar_current_week` AS `lms_calendar_current_week`,`c`.`lms_calendar_select_week` AS `lms_calendar_select_week`,`s`.`lms_week_deadline` AS `lms_select_deadline` from (((((`lms_game` `g` join `lms_player` `p` on((`g`.`lms_game_manager` = `p`.`lms_player_id`))) join `lms_week` `w` on(((`w`.`lms_week_no` = `g`.`lms_game_start_wkno`) and (`w`.`lms_week_calendar` = `g`.`lms_game_calendar`)))) join `lms_game_status` `gl` on((`g`.`lms_game_status` = `gl`.`lms_game_status_id`))) join `lms_calendar` `c` on((`g`.`lms_game_calendar` = `c`.`lms_calendar_id`))) left join `lms_week` `s` on(((`s`.`lms_week` = `c`.`lms_calendar_select_week`) and (`s`.`lms_week_calendar` = `c`.`lms_calendar_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_results`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_results`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_results` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_league` AS `lms_match_league`,`m`.`lms_match_ha` AS `lms_match_ha`,`m`.`lms_match_calendar` AS `lms_match_calendar`,`t`.`lms_team_id` AS `home_team_id`,`t`.`lms_team_name` AS `home_team_name`,`t`.`lms_team_abbr` AS `home_team_abbr`,`o`.`lms_team_id` AS `away_team_id`,`o`.`lms_team_name` AS `away_team_name`,`o`.`lms_team_abbr` AS `away_team_abbr`,`rh`.`lms_match_team_score` AS `home_score`,`ra`.`lms_match_team_score` AS `away_score`,`rh`.`lms_match_team_wl` AS `home_result`,`ra`.`lms_match_team_wl` AS `away_result`,`rt`.`lms_result_type_desc` AS `home_result_type`,`rt`.`lms_result_type_noresult` AS `no_result` from (((((`lms_match` `m` join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) join `lms_team` `o` on((`m`.`lms_match_opp` = `o`.`lms_team_id`))) left join `lms_results` `rh` on(((`m`.`lms_match_date` = `rh`.`lms_match_date`) and (`t`.`lms_team_id` = `rh`.`lms_match_team`)))) left join `lms_results` `ra` on(((`m`.`lms_match_date` = `ra`.`lms_match_date`) and (`o`.`lms_team_id` = `ra`.`lms_match_team`)))) left join `lms_result_type` `rt` on((`rh`.`lms_match_team_wl` = `rt`.`lms_result_type`))) order by `m`.`lms_match_weekno`,`m`.`lms_match_date`,`t`.`lms_team_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_match`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_match`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_match` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`m`.`lms_match_ha` AS `lms_match_ha`,`m`.`lms_match_calendar` AS `lms_match_calendar`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`w`.`lms_week_calendar` AS `lms_week_calendar`,`m`.`lms_match_team` AS `lms_match_team`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`t`.`lms_team_name` AS `lms_team_name`,`o`.`lms_team_name` AS `lms_match_opp`,`w`.`lms_week_start` AS `lms_week_start` from (((`lms_match` `m` join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) join `lms_week` `w` on(((`m`.`lms_match_weekno` = `w`.`lms_week_no`) and (`m`.`lms_match_calendar` = `w`.`lms_week_calendar`)))) join `lms_team` `o` on((`m`.`lms_match_opp` = `o`.`lms_team_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lms_available_picks`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_available_picks`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ehindle`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_available_picks` AS select `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`,`a`.`lms_available_picks_game` AS `lms_available_picks_game`,`a`.`lms_available_picks_team` AS `lms_available_picks_team`,`t`.`lms_team_name` AS `lms_team_name` from (`lms_available_picks` `a` join `lms_team` `t` on((`a`.`lms_available_picks_team` = `t`.`lms_team_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-30 22:39:15
