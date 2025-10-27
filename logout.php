<?php
require_once __DIR__ . '/lib/session.php';
//prévient les attaques de fixation de session
session_regenerate_id(true);

//supprime les données de la session
session_destroy();

//supprime les données  du tableau $_SESSION
unset($_SESSION);

header('Location: login.php');
