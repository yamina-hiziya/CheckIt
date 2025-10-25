<?php

try {

    $pdo = new PDO('mysql:dbname=studi_CheckIt;host=localhost;charset=utf8mb4', 'root', '');
} catch (Exception $e) {

    die('Erreur : ' . $e->getMessage());
}
