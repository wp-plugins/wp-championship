-- phpMyAdmin SQL Dump
-- version 2.11.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. April 2008 um 08:50
-- Server Version: 5.0.54
-- PHP-Version: 4.4.8_pre20070816-pl0-gentoo

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `wp25`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cs_team`
--

CREATE TABLE IF NOT EXISTS `cs_team` (
  `tid` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `icon` varchar(40) NOT NULL,
  `groupid` varchar(2) NOT NULL,
  `qualified` tinyint(1) NOT NULL,
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM  AUTO_INCREMENT=17 ;

--
-- Daten für Tabelle `cs_team`
--

INSERT INTO `cs_team` VALUES(1, 'Deutschland', 'D.gif', 'B', 0);
INSERT INTO `cs_team` VALUES(2, 'Schweiz', 'CH.gif', 'A', 0);
INSERT INTO `cs_team` VALUES(3, 'Tschechien', 'CZ.gif', 'A', 0);
INSERT INTO `cs_team` VALUES(4, 'Portugal', 'P.gif', 'A', 0);
INSERT INTO `cs_team` VALUES(5, 'Türkei', 'TR.gif', 'A', 0);
INSERT INTO `cs_team` VALUES(6, 'Österreich', 'A.gif', 'B', 0);
INSERT INTO `cs_team` VALUES(7, 'Kroatien', 'HR.gif', 'B', 0);
INSERT INTO `cs_team` VALUES(8, 'Polen', 'PL.gif', 'B', 0);
INSERT INTO `cs_team` VALUES(9, 'Rumänien', 'RO.gif', 'C', 0);
INSERT INTO `cs_team` VALUES(10, 'Frankreich', 'F.gif', 'C', 0);
INSERT INTO `cs_team` VALUES(11, 'Niederlande', 'NL.gif', 'C', 0);
INSERT INTO `cs_team` VALUES(12, 'Italien', 'I.gif', 'C', 0);
INSERT INTO `cs_team` VALUES(13, 'Spanien', 'E.gif', 'D', 0);
INSERT INTO `cs_team` VALUES(14, 'Russland', 'RUS.gif', 'D', 0);
INSERT INTO `cs_team` VALUES(15, 'Griechenland', 'GR.gif', 'D', 0);
INSERT INTO `cs_team` VALUES(16, 'Schweden', 'S.gif', 'D', 0);
