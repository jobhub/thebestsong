-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 30, 2014 at 04:29 PM
-- Server version: 5.5.37-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `thebestsong`
--

-- --------------------------------------------------------

--
-- Table structure for table `Album`
--

CREATE TABLE IF NOT EXISTS `Album` (
  `id` int(11) NOT NULL,
  `album_name` varchar(255) NOT NULL,
  `album_date` date DEFAULT NULL,
  `album_year` int(11) DEFAULT NULL,
  `description` tinytext,
  `artist_id` int(11) NOT NULL,
  `artist_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `votes_number` int(11) DEFAULT NULL,
  `general_score` int(11) DEFAULT NULL,
  `album_visual` varchar(255) DEFAULT NULL,
  `deezer_uri` varchar(255) DEFAULT NULL,
  `spotify_uri` varchar(255) DEFAULT NULL,
  `youtube_uri` varchar(255) DEFAULT NULL,
  `itunes_uri` varchar(255) DEFAULT NULL,
  `mood` varchar(255) DEFAULT NULL,
  `style1_id` int(11) DEFAULT NULL,
  `style1_name` varchar(100) DEFAULT NULL,
  `style1_weight` decimal(17,15) DEFAULT NULL,
  `style1_score_weighted` decimal(17,15) DEFAULT NULL,
  `style2_id` int(11) DEFAULT NULL,
  `style2_name` varchar(100) DEFAULT NULL,
  `style2_weight` decimal(17,15) DEFAULT NULL,
  `style2_score_weighted` decimal(17,15) DEFAULT NULL,
  `style3_id` int(11) DEFAULT NULL,
  `style3_name` varchar(100) DEFAULT NULL,
  `style3_weight` decimal(17,15) DEFAULT NULL,
  `style3_score_weighted` decimal(17,15) DEFAULT NULL,
  `style4_id` int(11) DEFAULT NULL,
  `style4_name` varchar(100) DEFAULT NULL,
  `style4_weight` decimal(17,15) DEFAULT NULL,
  `style4_score_weighted` decimal(17,15) DEFAULT NULL,
  `style5_id` int(11) DEFAULT NULL,
  `style5_name` varchar(100) DEFAULT NULL,
  `style5_weight` decimal(17,15) DEFAULT NULL,
  `style5_score_weighted` decimal(17,15) DEFAULT NULL,
  `style6_id` int(11) DEFAULT NULL,
  `style6_name` varchar(100) DEFAULT NULL,
  `style6_weight` decimal(17,15) DEFAULT NULL,
  `style6_score_weighted` decimal(17,15) DEFAULT NULL,
  `style7_id` int(11) DEFAULT NULL,
  `style7_name` varchar(100) DEFAULT NULL,
  `style7_weight` decimal(17,15) DEFAULT NULL,
  `style7_score_weighted` decimal(17,15) DEFAULT NULL,
  `style8_id` int(11) DEFAULT NULL,
  `style8_name` varchar(100) DEFAULT NULL,
  `style8_weight` decimal(17,15) DEFAULT NULL,
  `style8_score_weighted` decimal(17,15) DEFAULT NULL,
  `style9_id` int(11) DEFAULT NULL,
  `style9_name` varchar(100) DEFAULT NULL,
  `style9_weight` decimal(17,15) DEFAULT NULL,
  `style9_score_weighted` decimal(17,15) DEFAULT NULL,
  `style10_id` int(11) DEFAULT NULL,
  `style10_name` varchar(100) DEFAULT NULL,
  `style10_weight` decimal(17,15) DEFAULT NULL,
  `style10_score_weighted` decimal(17,15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artist_id` (`artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Album table';

-- --------------------------------------------------------

--
-- Stand-in structure for view `Album_Best_Song`
--
CREATE TABLE IF NOT EXISTS `Album_Best_Song` (
`max(general_score)` float
,`song_name` varchar(255)
,`id` int(11)
,`album_name` varchar(255)
,`album_id` int(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `Album_User_Vote`
--

CREATE TABLE IF NOT EXISTS `Album_User_Vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `album_name` varchar(512) NOT NULL,
  `vote_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Artist`
--

CREATE TABLE IF NOT EXISTS `Artist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `echonest_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `bandname` varchar(255) NOT NULL,
  `band_id` int(11) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `style1_id` int(11) DEFAULT NULL,
  `style1_name` varchar(100) DEFAULT NULL,
  `style1_weight` decimal(17,15) DEFAULT NULL,
  `style1_score_weighted` decimal(17,15) DEFAULT NULL,
  `style2_id` int(11) DEFAULT NULL,
  `style2_name` varchar(100) DEFAULT NULL,
  `style2_weight` decimal(17,15) DEFAULT NULL,
  `style2_score_weighted` decimal(17,15) DEFAULT NULL,
  `style3_id` int(11) DEFAULT NULL,
  `style3_name` varchar(100) DEFAULT NULL,
  `style3_weight` decimal(17,15) DEFAULT NULL,
  `style3_score_weighted` decimal(17,15) DEFAULT NULL,
  `style4_id` int(11) DEFAULT NULL,
  `style4_name` varchar(100) DEFAULT NULL,
  `style4_weight` decimal(17,15) DEFAULT NULL,
  `style4_score_weighted` decimal(17,15) DEFAULT NULL,
  `style5_id` int(11) DEFAULT NULL,
  `style5_name` varchar(100) DEFAULT NULL,
  `style5_weight` decimal(17,15) DEFAULT NULL,
  `style5_score_weighted` decimal(17,15) DEFAULT NULL,
  `style6_id` int(11) DEFAULT NULL,
  `style6_name` varchar(100) DEFAULT NULL,
  `style6_weight` decimal(17,15) DEFAULT NULL,
  `style6_score_weighted` decimal(17,15) DEFAULT NULL,
  `style7_id` int(11) DEFAULT NULL,
  `style7_name` varchar(100) DEFAULT NULL,
  `style7_weight` decimal(17,15) DEFAULT NULL,
  `style7_score_weighted` decimal(17,15) DEFAULT NULL,
  `style8_id` int(11) DEFAULT NULL,
  `style8_name` varchar(100) DEFAULT NULL,
  `style8_weight` decimal(17,15) DEFAULT NULL,
  `style8_score_weighted` decimal(17,15) DEFAULT NULL,
  `style9_id` int(11) DEFAULT NULL,
  `style9_name` varchar(100) DEFAULT NULL,
  `style9_weight` decimal(17,15) DEFAULT NULL,
  `style9_score_weighted` decimal(17,15) DEFAULT NULL,
  `style10_id` int(11) DEFAULT NULL,
  `style10_name` varchar(100) DEFAULT NULL,
  `style10_weight` decimal(17,15) DEFAULT NULL,
  `style10_score_weighted` decimal(17,15) DEFAULT NULL,
  `votes_number` int(11) DEFAULT NULL,
  `general_score` decimal(17,15) DEFAULT NULL,
  `visual_urls` text,
  `official_url` varchar(512) NOT NULL,
  `deezer_uri` varchar(255) DEFAULT NULL,
  `spotify_uri` varchar(255) DEFAULT NULL,
  `youtube_uri` varchar(255) DEFAULT NULL,
  `itunes_uri` varchar(255) DEFAULT NULL,
  `mood` varchar(255) NOT NULL,
  `hotttnesss` decimal(17,15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `echonest_id_UNIQUE` (`echonest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16914 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `artist_best_song`
--
CREATE TABLE IF NOT EXISTS `artist_best_song` (
`max(general_score)` float
,`id` int(11)
,`song_name` varchar(255)
,`artist_name` varchar(255)
,`artist_id` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `Artist_By_Style`
--
CREATE TABLE IF NOT EXISTS `Artist_By_Style` (
`style` varchar(100)
,`score` decimal(17,15)
,`name` varchar(255)
);
-- --------------------------------------------------------

--
-- Table structure for table `Artist_Similarity`
--

CREATE TABLE IF NOT EXISTS `Artist_Similarity` (
  `id` int(11) NOT NULL,
  `artist_name` varchar(125) NOT NULL,
  `similar_artist_id` varchar(255) NOT NULL,
  `similar_artist_name` varchar(255) NOT NULL,
  `familiarity` int(11) NOT NULL,
  `hotttness` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='in the similar_artists_id we store a list of similar artists IDs';

-- --------------------------------------------------------

--
-- Table structure for table `Artist_User_Vote`
--

CREATE TABLE IF NOT EXISTS `Artist_User_Vote` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `artist_name` text NOT NULL,
  `vote_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `email_confirmations`
--

CREATE TABLE IF NOT EXISTS `email_confirmations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `code` char(32) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  `modifiedAt` int(10) unsigned DEFAULT NULL,
  `confirmed` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned DEFAULT NULL,
  `ipAddress` char(15) NOT NULL,
  `attempted` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `Mood`
--

CREATE TABLE IF NOT EXISTS `Mood` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `Movie_Soundtrack`
--
CREATE TABLE IF NOT EXISTS `Movie_Soundtrack` (
`album_id` int(11)
,`album_name` varchar(255)
,`general_score` int(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `Musical_Style`
--

CREATE TABLE IF NOT EXISTS `Musical_Style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `label_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `description` text,
  `votes_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1265 ;

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `token` char(32) NOT NULL,
  `userAgent` varchar(120) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Song`
--

CREATE TABLE IF NOT EXISTS `Song` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `echonest_id` varchar(255) NOT NULL,
  `song_name` varchar(255) NOT NULL,
  `song_date` date DEFAULT NULL,
  `song_year` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `duration` decimal(10,5) DEFAULT NULL,
  `artist_id` int(11) NOT NULL,
  `artist_name` varchar(255) NOT NULL,
  `album_id` int(11) DEFAULT NULL,
  `album_name` varchar(255) DEFAULT NULL,
  `other_album_ids` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `votes_number` int(11) DEFAULT NULL,
  `general_score` float DEFAULT NULL,
  `mood` varchar(255) DEFAULT NULL,
  `hotttnesss` decimal(7,6) DEFAULT NULL,
  `style1_id` int(11) DEFAULT NULL,
  `style1_name` varchar(100) DEFAULT NULL,
  `style1_weight` decimal(17,15) DEFAULT NULL,
  `style1_score_weighted` decimal(17,15) DEFAULT NULL,
  `style2_id` int(11) DEFAULT NULL,
  `style2_name` varchar(100) DEFAULT NULL,
  `style2_weight` decimal(17,15) DEFAULT NULL,
  `style2_score_weighted` decimal(17,15) DEFAULT NULL,
  `style3_id` int(11) DEFAULT NULL,
  `style3_name` varchar(100) DEFAULT NULL,
  `style3_weight` decimal(17,15) DEFAULT NULL,
  `style3_score_weighted` decimal(17,15) DEFAULT NULL,
  `style4_id` int(11) DEFAULT NULL,
  `style4_name` varchar(100) DEFAULT NULL,
  `style4_weight` decimal(17,15) DEFAULT NULL,
  `style4_score_weighted` decimal(17,15) DEFAULT NULL,
  `style5_id` int(11) DEFAULT NULL,
  `style5_name` varchar(100) DEFAULT NULL,
  `style5_weight` decimal(17,15) DEFAULT NULL,
  `style5_score_weighted` decimal(17,15) DEFAULT NULL,
  `style6_id` int(11) DEFAULT NULL,
  `style6_name` varchar(100) DEFAULT NULL,
  `style6_weight` decimal(17,15) DEFAULT NULL,
  `style6_score_weighted` decimal(17,15) DEFAULT NULL,
  `style7_id` int(11) DEFAULT NULL,
  `style7_name` varchar(100) DEFAULT NULL,
  `style7_weight` decimal(17,15) DEFAULT NULL,
  `style7_score_weighted` decimal(17,15) DEFAULT NULL,
  `style8_id` int(11) DEFAULT NULL,
  `style8_name` varchar(100) DEFAULT NULL,
  `style8_weight` decimal(17,15) DEFAULT NULL,
  `style8_score_weighted` decimal(17,15) DEFAULT NULL,
  `style9_id` int(11) DEFAULT NULL,
  `style9_name` varchar(100) DEFAULT NULL,
  `style9_weight` decimal(17,15) DEFAULT NULL,
  `style9_score_weighted` decimal(17,15) DEFAULT NULL,
  `style10_id` int(11) DEFAULT NULL,
  `style10_name` varchar(100) DEFAULT NULL,
  `style10_weight` decimal(17,15) DEFAULT NULL,
  `style10_score_weighted` decimal(17,15) DEFAULT NULL,
  `deezer_url` varchar(512) DEFAULT NULL,
  `deezer_track_id` int(11) DEFAULT NULL,
  `spotify_url` varchar(512) DEFAULT NULL,
  `spotify_album_id` varchar(255) DEFAULT NULL,
  `itunes_url` varchar(512) DEFAULT NULL,
  `itunes_previewurl` varchar(512) DEFAULT NULL,
  `youtube_url` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `echonest_id` (`echonest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3704 ;

-- --------------------------------------------------------

--
-- Table structure for table `Song_User_Vote`
--

CREATE TABLE IF NOT EXISTS `Song_User_Vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `song_name` varchar(512) NOT NULL,
  `album_id` int(11) NOT NULL,
  `album_name` varchar(512) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `artist_name` varchar(512) NOT NULL,
  `vote_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `success_logins`
--

CREATE TABLE IF NOT EXISTS `success_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `ipAddress` char(15) NOT NULL,
  `userAgent` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `registration_date` datetime NOT NULL,
  `password_expiration_date` date NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `is_facebook_connect` tinyint(1) NOT NULL DEFAULT '0',
  `spotify_connect_token` varchar(255) DEFAULT NULL,
  `deezer_connect_token` varchar(255) DEFAULT NULL,
  `itunes_connect_token` varchar(255) DEFAULT NULL,
  `profile_id` int(11) NOT NULL,
  `profile_coeff` int(11) NOT NULL,
  `followers_number` int(11) NOT NULL,
  `credibility_mentor_coeff` int(11) NOT NULL,
  `active` enum('Y','N') NOT NULL,
  `banned` enum('Y','N') NOT NULL DEFAULT 'N',
  `suspended` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_User_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Deezer_Artist`
--

CREATE TABLE IF NOT EXISTS `User_Deezer_Artist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `artist_name` varchar(100) NOT NULL,
  `deezer_artist_id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Artists retrieved from playlists of Deezer' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Disliked_Songs`
--

CREATE TABLE IF NOT EXISTS `User_Disliked_Songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `disliked_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Fb_Musical_Activity`
--

CREATE TABLE IF NOT EXISTS `User_Fb_Musical_Activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `artist_fb_id` int(11) NOT NULL,
  `artist_name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Friends`
--

CREATE TABLE IF NOT EXISTS `User_Friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `user1_fb_id` int(11) NOT NULL,
  `user2_fb_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user1_id` (`user1_id`),
  KEY `user2_id` (`user2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Friends relations ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Liked_Songs`
--

CREATE TABLE IF NOT EXISTS `User_Liked_Songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `liked_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Mentor`
--

CREATE TABLE IF NOT EXISTS `User_Mentor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `mentor_id` (`mentor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines the list of mentors followed by an user' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Playlist`
--

CREATE TABLE IF NOT EXISTS `User_Playlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `playlist_name` varchar(100) NOT NULL,
  `song_ids` varchar(100) NOT NULL,
  `deezer_external_id` int(11) NOT NULL,
  `spotify_external_id` int(11) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='song_ids contains a comma-separated list';

-- --------------------------------------------------------

--
-- Table structure for table `User_Profile`
--

CREATE TABLE IF NOT EXISTS `User_Profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(255) NOT NULL,
  `profile_coefficient` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Currently we have "experts" and "normal_user"' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Session`
--

CREATE TABLE IF NOT EXISTS `User_Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` varchar(100) DEFAULT NULL,
  `session_key` varchar(100) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `remember_me` tinyint(1) DEFAULT NULL,
  `device_type` varchar(100) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id_2` (`session_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='In this table are stored all the session informations, such as IP, longitude...' AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `User_Spotify_Artist`
--

CREATE TABLE IF NOT EXISTS `User_Spotify_Artist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artist_name` varchar(100) NOT NULL,
  `spotify_artist_id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `User_Style_Credibility`
--

CREATE TABLE IF NOT EXISTS `User_Style_Credibility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `style_name` varchar(100) NOT NULL,
  `credibility_coeff` float NOT NULL,
  `previous_coeff` float NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `votes_number` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `style_id` (`style_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure for view `Album_Best_Song`
--
DROP TABLE IF EXISTS `Album_Best_Song`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Album_Best_Song` AS select max(`Song`.`general_score`) AS `max(general_score)`,`Song`.`song_name` AS `song_name`,`Song`.`id` AS `id`,`Song`.`album_name` AS `album_name`,`Song`.`album_id` AS `album_id` from `Song` group by `Song`.`album_name`,`Song`.`album_id`;

-- --------------------------------------------------------

--
-- Structure for view `artist_best_song`
--
DROP TABLE IF EXISTS `artist_best_song`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `artist_best_song` AS select max(`Song`.`general_score`) AS `max(general_score)`,`Song`.`id` AS `id`,`Song`.`song_name` AS `song_name`,`Song`.`artist_name` AS `artist_name`,`Song`.`artist_id` AS `artist_id` from `Song` group by `Song`.`artist_name`,`Song`.`artist_id`;

-- --------------------------------------------------------

--
-- Structure for view `Artist_By_Style`
--
DROP TABLE IF EXISTS `Artist_By_Style`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Artist_By_Style` AS select `Artist`.`style1_name` AS `style`,`Artist`.`style1_score_weighted` AS `score`,`Artist`.`name` AS `name` from `Artist` union select `Artist`.`style2_name` AS `style`,`Artist`.`style2_score_weighted` AS `score`,`Artist`.`name` AS `name` from `Artist` union select `Artist`.`style3_name` AS `style`,`Artist`.`style3_score_weighted` AS `score`,`Artist`.`name` AS `name` from `Artist` order by `style`,`score` desc;

-- --------------------------------------------------------

--
-- Structure for view `Movie_Soundtrack`
--
DROP TABLE IF EXISTS `Movie_Soundtrack`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Movie_Soundtrack` AS select `Album`.`id` AS `album_id`,`Album`.`album_name` AS `album_name`,`Album`.`general_score` AS `general_score` from `Album`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Album`
--
ALTER TABLE `Album`
  ADD CONSTRAINT `Album_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `Artist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Deezer_Artist`
--
ALTER TABLE `User_Deezer_Artist`
  ADD CONSTRAINT `User_Deezer_Artist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Fb_Musical_Activity`
--
ALTER TABLE `User_Fb_Musical_Activity`
  ADD CONSTRAINT `User_Fb_Musical_Activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Friends`
--
ALTER TABLE `User_Friends`
  ADD CONSTRAINT `User_Friends_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `User_Friends_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Mentor`
--
ALTER TABLE `User_Mentor`
  ADD CONSTRAINT `User_Mentor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `User_Mentor_ibfk_2` FOREIGN KEY (`mentor_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Playlist`
--
ALTER TABLE `User_Playlist`
  ADD CONSTRAINT `User_Playlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Spotify_Artist`
--
ALTER TABLE `User_Spotify_Artist`
  ADD CONSTRAINT `User_Spotify_Artist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `User_Style_Credibility`
--
ALTER TABLE `User_Style_Credibility`
  ADD CONSTRAINT `User_Style_Credibility_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `User_Style_Credibility_ibfk_2` FOREIGN KEY (`style_id`) REFERENCES `Musical_Style` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
