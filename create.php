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
$page_step = 0; // Cette variable gère les étapes de la page (0 : formulaire, 1 : fin)
$err_code = "";
$err_message = "";

// Variables de champs
$name = "";
$age = "";
$email = "";

// Si le formulaire est rempli et soumis
if (isset($_POST["go"])) {
    $name = htmlspecialchars(stripslashes(trim($_POST["name"])));
    $age = htmlspecialchars(stripslashes(trim($_POST["age"])));
    $email = htmlspecialchars(stripslashes(trim($_POST["email"])));

    // Envoi d'une requête pour créer l'utilisateur
    $response = create_user($name, $age, $email);

    if ($response["success"] === true) {
        $page_step = 1;
    } else {
        $err_code = $response["err_code"];
        $err_message = $response["err_message"];
        // Note : il est possible ici de récupérer le code
        // et de mettre nos propres messages d'erreur
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
    <title>Créer un utilisateur</title>
</head>

<body>
    <?php include_once("./ui/header.php") ?>

    <main>

        <h1>Créer un utilisateur</h1>

        <?php if ($page_step === 0) { ?>

            <form action="create.php" method="post">
                <label>Nom</label>
                <input type="text" name="name" id="name" value="<?php echo $name; ?>"><br />
                <label>Age</label>
                <input type="number" name="age" id="age" value="<?php echo $age; ?>"><br />
                <label>Email</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>"><br />
                <input type="submit" name="go" value="Créer">
            </form>

            <?php
            if ($err_message) {
                echo "<p>$err_message</p>";
            }
            ?>

        <?php } ?>

        <?php if ($page_step === 1) { ?>

            <p>Utilisateur créé !</p>
            <a href="./">Retour</a>

        <?php } ?>

    </main>

</body>

</html>