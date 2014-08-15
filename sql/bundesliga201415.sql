-- Bundesligasaison 2014/2015
-- wp-championship

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- Teams Bundesliga 2014/2015
insert into cs_team values (1,'Bayern München','FCB','fc-bayern-muenchen-wappen.png','A',0,0);
insert into cs_team values (2,'Borussia Dortmund','BVB','borussia-dortmund-wappen.png','A',0,0);
insert into cs_team values (3,'Bayer Leverkusen','B04','bayer-04-leverkusen-wappen.png','A',0,0);
insert into cs_team values (4,'FC Schalke 04','S04','fc-schalke-04-wappen.png','A',0,0);
insert into cs_team values (5,'SC Freiburg','SCF','sc-freiburg-wappen.png','A',0,0);
insert into cs_team values (6,'Eintracht Frankfurt','FFM','eintracht-frankfurt-wappen.png','A',0,0);
insert into cs_team values (7,'Hamburger SV','HSV','hamburger-sv-wappen.png','A',0,0);
insert into cs_team values (8,'Bor. Mönchengladbach','BMG','borussia-moenchengladbach-wappen.png','A',0,0);
insert into cs_team values (9,'Hannover 96','H96','hannover-96-wappen.png','A',0,0);
insert into cs_team values (10,'SC Paderborn','SCP','padderborn.png','A',0,0);
insert into cs_team values (11,'VfL Wolfsburg','WOB','vfl-wolfsburg-wappen.png','A',0,0);
insert into cs_team values (12,'VfB Stuttgart','VFB','vfb-stuttgart-wappen.png','A',0,0);
insert into cs_team values (13,'1. FSV Mainz 05','M05','fsv-mainz-05-wappen.png','A',0,0);
insert into cs_team values (14,'Werder Bremen','BRE','werder-bremen-wappen.png','A',0,0);
insert into cs_team values (15,'FC Augsburg','FCA','fc-augsburg-wappen.png','A',0,0);
insert into cs_team values (16,'1899 Hoffenheim','HOF','tsg-1899-hoffenheim-wappen.png','A',0,0);
insert into cs_team values (17,'Hertha BSC Berlin','BSC','Hertha_BSC.png','A',0,0);
insert into cs_team values (18,'1. FC Köln','FCK','fc_koeln.png','A',0,0);

-- Spiele Bundesliga 2014/2015
insert into cs_match values (1,'V',1,1,11,'München','2014-08-22 20:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (2,'V',1,16,15,'Hoffenheim','2014-08-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (3,'V',1,9,4,'Hannover','2014-08-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (4,'V',1,17,14,'Berlin','2014-08-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (5,'V',1,6,5,'Frankfurt','2014-08-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (6,'V',1,18,7,'Köln','2014-08-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (7,'V',1,2,3,'Dortmund','2014-08-23 18:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (8,'V',1,10,13,'Paderborn','2014-08-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (9,'V',1,8,12,'Gladbach','2014-08-24 17:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (10,'V',2,15,2,'Augsburg','2014-08-29 20:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (11,'V',2,3,17,'Leverkusen','2014-08-30 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (12,'V',2,11,6,'Wolfsburg','2014-08-30 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (13,'V',2,14,16,'Bremen','2014-08-30 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (14,'V',2,12,18,'Stuttgart','2014-08-30 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (15,'V',2,7,10,'Hamburg','2014-08-30 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (16,'V',2,4,1,'Gelsenkirchen','2014-08-30 18:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (17,'V',2,13,9,'Mainz','2014-08-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (18,'V',2,5,8,'Freiburg','2014-08-31 17:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (19,'V',3,3,14,'Leverkusen','2014-09-12 20:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (20,'V',3,1,12,'München','2014-09-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (21,'V',3,2,5,'Dortmund','2014-09-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (22,'V',3,16,11,'Hoffenheim','2014-09-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (23,'V',3,17,13,'Berlin','2014-09-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (24,'V',3,10,18,'Paderborn','2014-09-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (25,'V',3,8,4,'Gladbach','2014-09-13 18:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (26,'V',3,6,15,'Frankfurt','2014-09-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (27,'V',3,9,7,'Hannover','2014-09-14 17:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (28,'V',4,5,17,'Freiburg','2014-09-19 20:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (29,'V',4,4,6,'Gelsenkirchen','2014-09-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (30,'V',4,15,14,'Augsburg','2014-09-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (31,'V',4,12,16,'Stuttgart','2014-09-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (32,'V',4,7,1,'Hamburg','2014-09-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (33,'V',4,10,9,'Paderborn','2014-09-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (34,'V',4,11,3,'Wolfsburg','2014-09-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (35,'V',4,13,2,'Mainz','2014-09-21 17:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (36,'V',4,18,8,'Köln','2014-09-21 17:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (37,'V',5,1,10,'München','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (38,'V',5,2,12,'Dortmund','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (39,'V',5,3,15,'Leverkusen','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (40,'V',5,8,7,'Gladbach','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (41,'V',5,16,5,'Hoffenheim','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (42,'V',5,9,18,'Hannover','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (43,'V',5,17,11,'Berlin','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (44,'V',5,14,4,'Bremen','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (45,'V',5,6,13,'Braunschweig','2014-09-24 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (46,'V',6,4,2,'Gelsenkirchen','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (47,'V',6,11,14,'Wolfsburg','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (48,'V',6,13,16,'Mainz','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (49,'V',6,15,17,'Augsburg','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (50,'V',6,5,3,'Freiburg','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (51,'V',6,12,9,'Stuttgart','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (52,'V',6,7,6,'Hamburg','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (53,'V',6,18,1,'Köln','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (54,'V',6,10,8,'Paderborn','2014-09-27 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (55,'V',7,1,9,'München','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (56,'V',7,2,7,'Dortmund','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (57,'V',7,3,10,'Leverkusen','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (58,'V',7,11,15,'Wolfsburg','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (59,'V',7,8,13,'Gladbach','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (60,'V',7,16,4,'Hoffenheim','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (61,'V',7,17,12,'Berlin','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (62,'V',7,14,5,'Bremen','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (63,'V',7,6,18,'Frankfurt','2014-10-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (64,'V',8,1,14,'München','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (65,'V',8,4,17,'Gelsenkirchen','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (66,'V',8,13,15,'Mainz','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (67,'V',8,9,8,'Hannover','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (68,'V',8,5,11,'Freiburg','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (69,'V',8,12,3,'Stuttgart','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (70,'V',8,7,16,'Hamburg','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (71,'V',8,18,2,'Köln','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (72,'V',8,10,6,'Paderborn','2014-10-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (73,'V',9,2,9,'Dortmund','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (74,'V',9,3,4,'Leverkusen','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (75,'V',9,11,13,'Wolfsburg','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (76,'V',9,8,1,'Gladbach','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (77,'V',9,15,5,'Augsburg','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (78,'V',9,16,10,'Hoffenheim','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (79,'V',9,17,7,'Berlin','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (80,'V',9,14,18,'Bremen','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (81,'V',9,6,12,'Frankfurt','2014-10-25 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (82,'V',10,1,2,'München','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (83,'V',10,4,15,'Gelsenkirchen','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (84,'V',10,8,16,'Gladbach','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (85,'V',10,13,14,'Mainz','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (86,'V',10,9,6,'Hannover','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (87,'V',10,12,11,'Stuttgart','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (88,'V',10,7,3,'Hamburg','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (89,'V',10,18,5,'Köln','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (90,'V',10,10,17,'Paderborn','2014-11-01 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (91,'V',11,2,8,'Dortmund','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (92,'V',11,3,13,'Leverkusen','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (93,'V',11,11,7,'Wolfsburg','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (94,'V',11,15,10,'Augsburg','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (95,'V',11,16,18,'Hoffenheim','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (96,'V',11,17,9,'Berlin','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (97,'V',11,14,12,'Bremen','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (98,'V',11,6,1,'Frankfurt','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (99,'V',11,5,4,'Freiburg','2014-11-08 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (100,'V',12,1,16,'München','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (101,'V',12,4,11,'Gelsenkirchen','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (102,'V',12,8,6,'Gladbach','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (103,'V',12,13,5,'Mainz','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (104,'V',12,9,3,'Hannover','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (105,'V',12,12,15,'Stuttgart','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (106,'V',12,7,14,'Hamburg','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (107,'V',12,18,17,'Köln','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (108,'V',12,10,2,'Paderborn','2014-11-22 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (109,'V',13,4,13,'Gelsenkirchen','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (110,'V',13,3,18,'Leverkusen','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (111,'V',13,11,8,'Wolfsburg','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (112,'V',13,15,7,'Augsburg','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (113,'V',13,16,9,'Hoffenheim','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (114,'V',13,17,1,'Berlin','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (115,'V',13,14,10,'Bremen','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (116,'V',13,6,2,'Frankfurt','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (117,'V',13,5,12,'Freiburg','2014-11-29 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (118,'V',14,1,3,'München','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (119,'V',14,2,16,'Dortmund','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (120,'V',14,8,17,'Gladbach','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (121,'V',14,9,11,'Hannover','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (122,'V',14,6,14,'Frankfurt','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (123,'V',14,12,4,'Stuttgart','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (124,'V',14,7,13,'Hamburg','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (125,'V',14,18,15,'Köln','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (126,'V',14,10,5,'Paderborn','2014-12-06 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (127,'V',15,4,18,'Gelsenkirchen','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (128,'V',15,3,8,'Leverkusen','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (129,'V',15,11,10,'Wolfsburg','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (130,'V',15,13,12,'Mainz','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (131,'V',15,15,1,'Augsburg','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (132,'V',15,16,6,'Hoffenheim','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (133,'V',15,17,2,'Berlin','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (134,'V',15,14,9,'Bremen','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (135,'V',15,5,7,'Freiburg','2014-12-13 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (136,'V',16,1,5,'München','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (137,'V',16,2,11,'Dortmund','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (138,'V',16,8,14,'Gladbach','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (139,'V',16,16,3,'Hoffenheim','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (140,'V',16,9,15,'Hannover','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (141,'V',16,6,17,'Frankfurt','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (142,'V',16,7,12,'Hamburg','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (143,'V',16,18,13,'Köln','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (144,'V',16,10,4,'Paderborn','2014-12-17 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (145,'V',17,4,7,'Gelsenkirchen','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (146,'V',17,3,6,'Leverkusen','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (147,'V',17,11,18,'Wolfsburg','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (148,'V',17,13,1,'Mainz','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (149,'V',17,15,8,'Augsburg','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (150,'V',17,17,16,'Berlin','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (151,'V',17,14,2,'Bremen','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (152,'V',17,5,9,'Freiburg','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (153,'V',17,12,10,'Stuttgart','2014-12-20 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (154,'V',18,4,9,'Gelsenkirchen','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (155,'V',18,3,2,'Leverkusen','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (156,'V',18,11,1,'Wolfsburg','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (157,'V',18,13,10,'Mainz','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (158,'V',18,15,16,'Augsburg','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (159,'V',18,14,17,'Bremen','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (160,'V',18,5,6,'Freiburg','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (161,'V',18,12,8,'Stuttgart','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (162,'V',18,7,18,'Hamburg','2015-01-31 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (163,'V',19,1,4,'München','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (164,'V',19,2,15,'Dortmund','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (165,'V',19,8,5,'Gladbach','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (166,'V',19,16,14,'Hoffenheim','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (167,'V',19,9,13,'Hannover','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (168,'V',19,17,3,'Berlin','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (169,'V',19,6,11,'Frankfurt','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (170,'V',19,18,12,'Köln','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (171,'V',19,10,7,'Paderborn','2015-02-04 20:00:00',-1,-1,-1,-1,-1);
insert into cs_match values (172,'V',20,4,8,'Gelsenkirchen','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (173,'V',20,11,16,'Wolfsburg','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (174,'V',20,13,17,'Mainz','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (175,'V',20,15,6,'Augsburg','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (176,'V',20,14,3,'Bremen','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (177,'V',20,5,2,'Freiburg','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (178,'V',20,12,1,'Stuttgart','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (179,'V',20,7,9,'Hamburg','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (180,'V',20,18,10,'Köln','2015-02-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (181,'V',21,1,7,'München','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (182,'V',21,2,13,'Dortmund','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (183,'V',21,3,11,'Leverkusen','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (184,'V',21,8,18,'Gladbach','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (185,'V',21,16,12,'Hoffenheim','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (186,'V',21,9,10,'Hannover','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (187,'V',21,17,5,'Berlin','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (188,'V',21,14,15,'Bremen','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (189,'V',21,6,4,'Frankfurt','2015-02-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (190,'V',22,4,14,'Gelsenkirchen','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (191,'V',22,11,17,'Wolfsburg','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (192,'V',22,13,6,'Mainz','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (193,'V',22,15,3,'Augsburg','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (194,'V',22,5,17,'Freiburg','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (195,'V',22,12,2,'Stuttgart','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (196,'V',22,7,8,'Hamburg','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (197,'V',22,18,9,'Köln','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (198,'V',22,10,1,'Paderborn','2015-02-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (199,'V',23,1,18,'München','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (200,'V',23,2,4,'Dortmund','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (201,'V',23,3,5,'Leverkusen','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (202,'V',23,8,10,'Gladbach','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (203,'V',23,16,13,'Hoffenheim','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (204,'V',23,9,12,'Hannover','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (205,'V',23,17,15,'Berlin','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (206,'V',23,14,11,'Bremen','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (207,'V',23,6,7,'Frankfurt','2015-02-28 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (208,'V',24,4,16,'Gelsenkirchen','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (209,'V',24,13,8,'Mainz','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (210,'V',24,15,11,'Augsburg','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (211,'V',24,9,1,'Hannover','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (212,'V',24,5,14,'Freiburg','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (213,'V',24,12,17,'Stuttgart','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (214,'V',24,7,2,'Hamburg','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (215,'V',24,18,6,'Köln','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (216,'V',24,10,3,'Paderborn','2015-03-07 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (217,'V',25,2,18,'Dortmund','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (218,'V',25,3,12,'Leverkusen','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (219,'V',25,11,5,'Wolfsburg','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (220,'V',25,8,9,'Gladbach','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (221,'V',25,15,13,'Augsburg','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (222,'V',25,16,7,'Hoffenheim','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (223,'V',25,17,4,'Berlin','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (224,'V',25,14,1,'Bremen','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (225,'V',25,6,10,'Frankfurt','2015-03-14 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (226,'V',26,1,8,'München','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (227,'V',26,4,3,'Gelsenkirchen','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (228,'V',26,13,11,'Mainz','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (229,'V',26,9,2,'Hannover','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (230,'V',26,5,15,'Freiburg','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (231,'V',26,12,6,'Stuttgart','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (232,'V',26,7,17,'Hamburg','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (233,'V',26,18,14,'Köln','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (234,'V',26,10,16,'Paderborn','2015-03-21 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (235,'V',27,2,1,'Dortmund','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (236,'V',27,3,7,'Leverkusen','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (237,'V',27,11,12,'Wolfsburg','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (238,'V',27,15,4,'Augsburg','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (239,'V',27,16,8,'Hoffenheim','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (240,'V',27,17,10,'Berlin','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (241,'V',27,14,13,'Bremen','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (242,'V',27,6,9,'Frankfurt','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (243,'V',27,5,18,'Freiburg','2015-04-04 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (244,'V',28,1,6,'München','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (245,'V',28,4,5,'Gelsenkirchen','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (246,'V',28,8,2,'Gladbach','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (247,'V',28,13,3,'Mainz','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (248,'V',28,9,17,'Hannover','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (249,'V',28,12,14,'Stuttgart','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (250,'V',28,7,11,'Hamburg','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (251,'V',28,18,16,'Köln','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (252,'V',28,10,15,'Paderborn','2015-04-11 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (253,'V',29,2,10,'Dortmund','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (254,'V',29,3,9,'Leverkusen','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (255,'V',29,11,4,'Wolfsburg','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (256,'V',29,15,12,'Augsburg','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (257,'V',29,16,1,'Hoffenheim','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (258,'V',29,17,18,'Berlin','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (259,'V',29,14,7,'Bremen','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (260,'V',29,6,8,'Frankfurt','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (261,'V',29,5,13,'Freiburg','2015-04-18 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (262,'V',30,1,17,'München','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (263,'V',30,2,6,'Dortmund','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (264,'V',30,8,11,'Gladbach','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (265,'V',30,13,4,'Mainz','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (266,'V',30,9,16,'Hannover','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (267,'V',30,12,5,'Stuttgart','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (268,'V',30,7,15,'Hamburg','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (269,'V',30,18,3,'Köln','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (270,'V',30,10,14,'Paderborn','2015-04-25 15.30:00',-1,-1,-1,-1,-1);
insert into cs_match values (271,'V',31,4,12,'Gelsenkirchen','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (272,'V',31,3,1,'Leverkusen','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (273,'V',31,11,9,'Wolfsburg','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (274,'V',31,13,7,'Mainz','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (275,'V',31,15,18,'Augsburg','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (276,'V',31,16,2,'Hoffenheim','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (277,'V',31,17,8,'Berlin','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (278,'V',31,14,6,'Bremen','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (279,'V',31,5,10,'Freiburg','2015-05-02 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (280,'V',32,1,15,'München','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (281,'V',32,2,17,'Dortmund','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (282,'V',32,8,3,'Gladbach','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (283,'V',32,9,14,'Hannover','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (284,'V',32,6,16,'Frankfurt','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (285,'V',32,12,13,'Stuttgart','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (286,'V',32,7,5,'Hamburg','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (287,'V',32,18,4,'Köln','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (288,'V',32,10,11,'Paderborn','2015-05-09 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (289,'V',33,4,10,'Gelsenkirchen','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (290,'V',33,3,16,'Leverkusen','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (291,'V',33,11,2,'Wolfsburg','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (292,'V',33,13,18,'Mainz','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (293,'V',33,15,9,'Augsburg','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (294,'V',33,17,6,'Hertha','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (295,'V',33,14,8,'Bremen','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (296,'V',33,5,1,'Freiburg','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (297,'V',33,12,7,'Stuttgart','2015-05-15 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (298,'V',34,1,13,'München','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (299,'V',34,2,14,'Dortmund','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (300,'V',34,8,15,'Gladbach','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (301,'V',34,16,17,'Hoffenheim','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (302,'V',34,9,5,'Hannover','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (303,'V',34,6,3,'Frankfurt','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (304,'V',34,7,4,'Hamburg','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (305,'V',34,18,11,'Köln','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
insert into cs_match values (306,'V',34,10,12,'Paderborn','2015-05-23 15:30:00',-1,-1,-1,-1,-1);
