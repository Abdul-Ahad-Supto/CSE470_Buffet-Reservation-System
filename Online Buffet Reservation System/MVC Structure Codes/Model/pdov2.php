<?php
// /Model/pdov2.php

// This path goes up three levels to the project root to find config.ini
$config = parse_ini_file('../../../config.ini');

$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

$pdo = new PDO($dsn, $config['user'], $config['password']);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
