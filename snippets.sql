DROP TABLE IF EXISTS `csc_snippets`;
CREATE TABLE `csc_snippets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `category_tag` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `visibility` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `snippet` longtext NOT NULL,
  `created_time` int(11) unsigned NOT NULL,
  `updated_time` int(11) unsigned NOT NULL,
  `view_count` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;