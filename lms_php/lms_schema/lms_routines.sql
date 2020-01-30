-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: lms
-- ------------------------------------------------------
-- Server version	5.7.18-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary view structure for view `v_lms_match`
--

DROP TABLE IF EXISTS `v_lms_match`;
/*!50001 DROP VIEW IF EXISTS `v_lms_match`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lms_match` AS SELECT 
 1 AS `lms_match_id`,
 1 AS `lms_match_weekno`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_match_team`,
 1 AS `lms_match_date`,
 1 AS `lms_match_result`,
 1 AS `lms_team_name`,
 1 AS `lms_week_start`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_player_games`
--

DROP TABLE IF EXISTS `v_lms_player_games`;
/*!50001 DROP VIEW IF EXISTS `v_lms_player_games`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lms_player_games` AS SELECT 
 1 AS `lms_player_id`,
 1 AS `lms_game_id`,
 1 AS `lms_game_player_status`,
 1 AS `lms_game_name`,
 1 AS `lms_game_start_wkno`,
 1 AS `lms_game_status`,
 1 AS `lms_game_total_players`,
 1 AS `lms_game_still_active`,
 1 AS `lms_game_week_count`,
 1 AS `lms_player_screen_name`,
 1 AS `lms_active`,
 1 AS `lms_game_player_status_text`,
 1 AS `lms_game_status_text`,
 1 AS `lms_week`,
 1 AS `lms_year`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_player_picks`
--

DROP TABLE IF EXISTS `v_lms_player_picks`;
/*!50001 DROP VIEW IF EXISTS `v_lms_player_picks`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lms_player_picks` AS SELECT 
 1 AS `lms_pick_player_id`,
 1 AS `lms_pick_game_id`,
 1 AS `lms_pick_match_id`,
 1 AS `lms_pick_wl`,
 1 AS `lms_player_screen_name`,
 1 AS `lms_game_name`,
 1 AS `lms_match_weekno`,
 1 AS `lms_team_id`,
 1 AS `lms_team_name`,
 1 AS `lms_game_status_text`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_match_date`,
 1 AS `lms_match_result`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_available_picks`
--

DROP TABLE IF EXISTS `v_lms_available_picks`;
/*!50001 DROP VIEW IF EXISTS `v_lms_available_picks`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lms_available_picks` AS SELECT 
 1 AS `lms_available_picks_player_id`,
 1 AS `lms_available_picks_game`,
 1 AS `lms_available_picks_team`,
 1 AS `lms_team_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_lms_game`
--

DROP TABLE IF EXISTS `v_lms_game`;
/*!50001 DROP VIEW IF EXISTS `v_lms_game`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
 1 AS `lms_player_screen_name`,
 1 AS `lms_week`,
 1 AS `lms_year`,
 1 AS `lms_week_start`,
 1 AS `lms_game_status_text`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_lms_match`
--

/*!50001 DROP VIEW IF EXISTS `v_lms_match`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`netwyrks`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_match` AS select `m`.`lms_match_id` AS `lms_match_id`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`m`.`lms_match_team` AS `lms_match_team`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result`,`t`.`lms_team_name` AS `lms_team_name`,`w`.`lms_week_start` AS `lms_week_start` from ((`lms_match` `m` join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) join `lms_week` `w` on((`m`.`lms_match_weekno` = `w`.`lms_week_no`))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`netwyrks`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_player_games` AS select `gp`.`lms_player_id` AS `lms_player_id`,`gp`.`lms_game_id` AS `lms_game_id`,`gp`.`lms_game_player_status` AS `lms_game_player_status`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`p`.`lms_active` AS `lms_active`,`pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`,`gl`.`lms_game_status_text` AS `lms_game_status_text`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year` from (((((`lms_game_player` `gp` join `lms_game` `g` on((`gp`.`lms_game_id` = `g`.`lms_game_id`))) join `lms_player` `p` on((`gp`.`lms_player_id` = `p`.`lms_player_id`))) join `lms_game_player_status` `pl` on((`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`))) join `lms_game_status` `gl` on((`g`.`lms_game_status` = `gl`.`lms_game_status_id`))) join `lms_week` `w` on((`w`.`lms_week_no` = `g`.`lms_game_start_wkno`))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`netwyrks`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_player_picks` AS select `p`.`lms_pick_player_id` AS `lms_pick_player_id`,`p`.`lms_pick_game_id` AS `lms_pick_game_id`,`p`.`lms_pick_match_id` AS `lms_pick_match_id`,`p`.`lms_pick_wl` AS `lms_pick_wl`,`pl`.`lms_player_screen_name` AS `lms_player_screen_name`,`g`.`lms_game_name` AS `lms_game_name`,`m`.`lms_match_weekno` AS `lms_match_weekno`,`t`.`lms_team_id` AS `lms_team_id`,`t`.`lms_team_name` AS `lms_team_name`,`g`.`lms_game_status_text` AS `lms_game_status_text`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`m`.`lms_match_date` AS `lms_match_date`,`m`.`lms_match_result` AS `lms_match_result` from (((((`lms_pick` `p` join `lms_player` `pl` on((`p`.`lms_pick_player_id` = `pl`.`lms_player_id`))) join `v_lms_game` `g` on((`p`.`lms_pick_game_id` = `g`.`lms_game_id`))) join `lms_match` `m` on((`p`.`lms_pick_match_id` = `m`.`lms_match_id`))) join `lms_team` `t` on((`m`.`lms_match_team` = `t`.`lms_team_id`))) join `lms_week` `w` on((`w`.`lms_week_no` = `m`.`lms_match_weekno`))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`netwyrks`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_available_picks` AS select `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`,`a`.`lms_available_picks_game` AS `lms_available_picks_game`,`a`.`lms_available_picks_team` AS `lms_available_picks_team`,`t`.`lms_team_name` AS `lms_team_name` from (`lms_available_picks` `a` join `lms_team` `t` on((`a`.`lms_available_picks_team` = `t`.`lms_team_id`))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`netwyrks`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lms_game` AS select `g`.`lms_game_id` AS `lms_game_id`,`g`.`lms_game_start_wkno` AS `lms_game_start_wkno`,`g`.`lms_game_name` AS `lms_game_name`,`g`.`lms_game_status` AS `lms_game_status`,`g`.`lms_game_week_count` AS `lms_game_week_count`,`g`.`lms_game_total_players` AS `lms_game_total_players`,`g`.`lms_game_still_active` AS `lms_game_still_active`,`g`.`lms_game_manager` AS `lms_game_manager`,`g`.`lms_game_code` AS `lms_game_code`,`p`.`lms_player_screen_name` AS `lms_player_screen_name`,`w`.`lms_week` AS `lms_week`,`w`.`lms_year` AS `lms_year`,`w`.`lms_week_start` AS `lms_week_start`,`gl`.`lms_game_status_text` AS `lms_game_status_text` from (((`lms_game` `g` join `lms_player` `p` on((`g`.`lms_game_manager` = `p`.`lms_player_id`))) join `lms_week` `w` on((`w`.`lms_week_no` = `g`.`lms_game_start_wkno`))) join `lms_game_status` `gl` on((`g`.`lms_game_status` = `gl`.`lms_game_status_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Dumping events for database 'lms'
--

--
-- Dumping routines for database 'lms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-01-30 16:21:51
