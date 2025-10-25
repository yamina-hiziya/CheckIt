<?php

//attention a ne pas utiliser ce code en production sans le securiser contre les injections SQL
//n'est pas securiser contre les injections SQL

$pdo = new PDO('mysql:dbname=studi_CheckIt;host=localhost;charset=utf8mb4', 'root', '');
$id = $_GET['id'];
$query = $pdo->query("SELECT * FROM user WHERE id = $id");
$result = $query->fetch(PDO::FETCH_ASSOC);
