CREATE TABLE `users` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8mb4_bin NOT NULL,
  `avatar` varchar(125) COLLATE utf8mb4_bin NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `user_roles` (
  `_id` int(11) NOT NULL,
  `role` varchar(15) NOT NULL,
  PRIMARY KEY (`_id`, `role`)
);
