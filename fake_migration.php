<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=siag3;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` text NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$db->exec("INSERT INTO migrations (version, class, `group`, namespace, time, batch) VALUES ('2026-07-31-163046', 'App\Database\Migrations\UpdateSiagSchema', 'default', 'App', UNIX_TIMESTAMP(), 1)");
echo "Done.\n";
