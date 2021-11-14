<?php

// Paramètres de la base de données
$db_config = [
    "host" => "localhost",
    "name" => "test_dw15",
    "user" => "root",
    "password" => "", // Wamp : ", Mamp : "root"
];

// Fonction de connection à la base de données
// Renvoie la connection ou null
function db_connect()
{
    $db_config = $GLOBALS["db_config"];
    try {
        // $bdd = new PDO('mysql:host=' . $db_config["host"] . ';dbname=' . $dbname . ';charset=utf8', $user, $password);
        $bdd = new PDO(
            "mysql:host=" . $db_config["host"] . ";dbname=" . $db_config["name"] . ";charset=utf8",
            $db_config["user"],
            $db_config["password"]
        );
        return $bdd;
    } catch (Exception $err) {
        var_dump($err->getMessage());
        return null;
    }
}
