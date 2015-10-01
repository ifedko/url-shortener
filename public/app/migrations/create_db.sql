CREATE TABLE `url_shortener` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `source_url` text DEFAULT NULL,
  `short_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `counter` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `short_code` (`short_code`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;