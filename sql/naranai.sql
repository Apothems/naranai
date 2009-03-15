-- naranai DB schema

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Table structure for table `aliases`
--

CREATE TABLE IF NOT EXISTS `aliases` (
  `oldtag` varchar(128) NOT NULL,
  `newtag` varchar(128) NOT NULL,
  PRIMARY KEY  (`oldtag`),
  UNIQUE KEY `aliases__unique` (`oldtag`,`newtag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `owner_ip` char(16) NOT NULL,
  `posted` datetime default NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `image_id` (`image_id`),
  KEY `owner_ip` (`owner_ip`),
  KEY `posted` (`posted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(128) NOT NULL,
  `value` text,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL,
  `owner_ip` varchar(15) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `ext` varchar(4) NOT NULL,
  `source` varchar(249) default NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `posted` datetime NOT NULL,
  `numeric_score` int(11) NOT NULL default '0',
  `rating` tinyint(1) NOT NULL default '0',
  `note` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `images__hash` (`hash`),
  KEY `images__owner_id` (`owner_id`),
  KEY `images__width` (`width`),
  KEY `images__height` (`height`),
  KEY `images__numeric_score` (`numeric_score`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `image_groups`
--

CREATE TABLE IF NOT EXISTS `image_groups` (
  `image_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `image_group_key` (`image_id`,`group_id`),
  UNIQUE KEY `image_id` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `image_tags`
--

CREATE TABLE IF NOT EXISTS `image_tags` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  UNIQUE KEY `image_tags__key` (`image_id`,`tag_id`),
  KEY `image_tags__image_id` (`image_id`),
  KEY `image_tags__tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `numeric_score_votes`
--

CREATE TABLE IF NOT EXISTS `numeric_score_votes` (
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  UNIQUE KEY `image_id` (`image_id`,`user_id`),
  KEY `image_id_2` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reported`
--

CREATE TABLE IF NOT EXISTS `reported` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) default NULL,
  `reporter_name` varchar(32) default NULL,
  `reason_type` varchar(255) default NULL,
  `reason` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(64) NOT NULL,
  `count` int(11) NOT NULL default '0',
  `type` enum('normal','character','artist','series') NOT NULL default 'normal',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tags__tag` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_histories`
--

CREATE TABLE IF NOT EXISTS `tag_histories` (
  `id` int(11) NOT NULL auto_increment,
  `image_id` int(11) NOT NULL,
  `tags` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_set` datetime NOT NULL,
  `user_ip` char(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_types`
--

CREATE TABLE IF NOT EXISTS `tag_types` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `pass` varchar(32) default NULL,
  `joindate` datetime NOT NULL,
  `admin` varchar(1) NOT NULL default 'N',
  `email` varchar(249) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `users__name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
