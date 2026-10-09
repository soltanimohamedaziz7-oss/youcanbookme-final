<?php
// Cette couche ouvre une connexion MySQLi et active UTF-8 pour les textes.


// 
// CONNEXION A LA BASE DE DONNEES
// 



require_once __DIR__ . "/../config/config.php";

function open_connection()
{
    $connection = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME
    );

    if ($connection->connect_error) {
        die("Impossible de se connecter à la base de données.");
    }

    $connection->set_charset("utf8mb4");


    return $connection;
}

function close_connection($connection)
{
    $connection->close();
}
