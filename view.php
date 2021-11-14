<?php

// Includes (fichiers externes)
include_once("./database/mysql.php");
include_once("./database/user.php");

// Connection à la base de données
$db = db_connect();
if ($db === null) {
    die;
}

// Variables de page
$page_step = -1; // Cette variable gère les étapes de la page (0 : formulaire, 1 : fin, 2 : suppression, -1 : erreur ID)
$err_code = "";
$err_message = "";

// Variables de champs
$name = "";
$age = "";
$email = "";

// Récupérer l'ID et charger les données de l'utilisateur
if (isset($_GET["id"])) {
    $user_data = get_single_user($_GET["id"]);
    if ($user_data !== false) {
        $id = $user_data["id"];
        $name = $user_data["name"];
        $age = $user_data["age"];
        $email = $user_data["email"];
        $page_step = 0; // Afficher le contenu normal de la page
    }
}

// Si le formulaire est rempli et soumis
if (isset($_POST["go"])) {
    $name = htmlspecialchars(stripslashes(trim($_POST["name"])));
    $age = htmlspecialchars(stripslashes(trim($_POST["age"])));
    $email = htmlspecialchars(stripslashes(trim($_POST["email"])));

    // Envoi d'une requête pour modifier l'utilisateur
    $response = update_user($id, $name, $age, $email);

    if ($response["success"] === true) {
        $page_step = 1;
    } else {
        $err_code = $response["err_code"];
        $err_message = $response["err_message"];
        // Note : il est possible ici de récupérer le code
        // et de mettre nos propres messages d'erreur
    }
}

// Si la suppression est demandée
if (isset($_POST["delete"])) {
    // Envoi d'une requête pour supprimer l'utilisateur
    $success = delete_user($_GET["id"]);

    if ($success === true) {
        $page_step = 2;
    } else {
        $page_step = -1;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Modifier un utilisateur</title>
</head>

<body>

    <?php include_once("./ui/header.php") ?>

    <main>

        <h1>Modifier un utilisateur</h1>

        <?php if ($page_step === 0) { ?>

            <form action="view.php?id=<?php echo $_GET["id"]; ?>" method="post">
                <label>Nom</label>
                <input type="text" name="name" id="name" value="<?php echo $name; ?>"><br />
                <label>Age</label>
                <input type="number" name="age" id="age" value="<?php echo $age; ?>"><br />
                <label>Email</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>"><br />
                <input type="submit" name="go" value="Sauvegarder">
                <input type="submit" name="delete" value="Supprimer">
            </form>

            <?php
            if ($err_message) {
                echo "<p>$err_message</p>";
            }
            ?>

        <?php } ?>


        <?php if ($page_step === 1) { ?>

            <p>Utilisateur modifié !</p>
            <a href="./">Retour</a>

        <?php } ?>


        <?php if ($page_step === 2) { ?>

            <p>Utilisateur supprimé !</p>
            <a href="./">Retour</a>

        <?php } ?>


        <?php if ($page_step === -1) { ?>

            <p>Une erreur est survenue.</p>
            <a href="./">Retour</a>

        <?php } ?>

    </main>

</body>

</html>