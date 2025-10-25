<?php

function verifyUserLoginPassword(PDO $pdo, string $email, string $password)
{
    //"$2y$10$eqFKy1FH08Fg5sgTzrGzw.l/BEZIOKA4Cn6quwQUm/tnM2H/0uncu" corresponds to "test"
    $query = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();
    //fetch() nous permet de recuperer une seule ligne
    $result = $query->fetch(PDO::FETCH_ASSOC);
    var_dump($result);
}
