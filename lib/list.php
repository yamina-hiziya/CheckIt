<?php

function getListsByUserId(PDO $pdo, int $userId): array
{
    $query = $pdo->prepare("SELECT list.*, category.name AS category_name, category.icon AS category_icon From list JOIN   category ON category.id = list.category_id WHERE user_id = :user_id");
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();
    $lists = $query->fetchAll(PDO::FETCH_ASSOC);

    return $lists;
}
