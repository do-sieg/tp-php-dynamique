<?php

// Liste des données de tous les utilisateurs
function get_users_list($search = "") {
    $db = $GLOBALS["db"];
    $sql_query = "SELECT * FROM users";
    // S'il y a une recherche en cours, on ajoute cette partie à la requête
    if (strlen($search) > 0) {
        $sql_query .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
    }
    $query = $db->prepare($sql_query);
    $query->execute();
    return $query->fetchAll();
}

// Données d'un utilisateur
function get_single_user($id)
{
    $db = $GLOBALS["db"];
    $query = $db->prepare("SELECT * FROM users WHERE id=$id");
    $query->execute();
    return $query->fetch();
}

// Ajout d'utilisateur
function create_user($name, $age, $email) {
    $db = $GLOBALS["db"];
    $response = [
        "success" => false, // On crée un résultat faux par défaut
        "err_code" => "",
        "err_message" => "",
    ];
    
    if (strlen($name) > 0 && strlen($age) > 0 && strlen($email) > 0) {
        $query = $db->prepare("INSERT INTO users (name, age, email) VALUES('$name', $age, '$email')");
        $query->execute();
        // La base de données renvoie 0 comme ID d'insertion si celle-ci n'a pas lieu
        if ($db->lastInsertId() != 0) {
            $response["success"] = true;
        } else {
            // On renvoie l'erreur signalée par MySQL
            $response["err_code"] = $query->errorInfo()[0];
            $response["err_message"] = $query->errorInfo()[2]; 
        }
    } else {
        // On renvoie une erreur (qu'on crée) si un champ est vide
        $response["err_code"] = "ERR_REQUIRED";
        $response["err_message"] = "Veuillez remplir tous les champs.";
    }
    return $response;
}

// Mise à jour d'utilisateur
function update_user($id, $name, $age, $email) {
    $db = $GLOBALS["db"];
    $response = [
        "success" => false, // On crée un résultat faux par défaut
        "err_code" => "",
        "err_message" => "",
    ];

    if (strlen($name) > 0 && strlen($age) > 0 && strlen($email) > 0) {
        $query = $db->prepare("UPDATE users SET name = '$name', age = $age, email = '$email' WHERE id=$id");
        $query->execute();

        // On vérifie qu'il n'y a pas d'erreur
        if ($query->errorInfo()[0] == "00000") {
            $response["success"] = true;
        } else {
            // On renvoie l'erreur signalée par MySQL
            $response["err_code"] = $query->errorInfo()[0];
            $response["err_message"] = $query->errorInfo()[2];
        }
    } else {
        // On renvoie une erreur (qu'on crée) si un champ est vide
        $response["err_code"] = "ERR_REQUIRED";
        $response["err_message"] = "Veuillez remplir tous les champs.";
    }
    return $response;
}

// Suppression d'utilisateur
function delete_user($id) {
    $success = false;

    $db = $GLOBALS["db"];
    $query = $db->prepare("DELETE FROM users WHERE id=$id");
    $query->execute();

    // On vérifie qu'il n'y a pas d'erreur et que quelque chose a bien été supprimé
    if ($query->errorInfo()[0] == "00000" && $query->rowCount() > 0) {
        $success = true;
    }
    return $success;
}
