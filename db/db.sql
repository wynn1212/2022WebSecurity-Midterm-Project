SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+08:00";

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `password`) VALUES ("1", "admin", "12345678");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("2", "root", "abcdef");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("3", "user", "qazwsx");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("4", "administrator", "edcrfv");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("5", "guest", "guest");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("6", "s3cr3t", "th3v3rys3cur3s3cr3tp4ssw0rd");