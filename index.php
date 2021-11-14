<?php

// Includes (fichiers externes)
include_once("./database/mysql.php");
include_once("./database/user.php");

// Connection à la base de données
$db = db_connect();
if ($db === null) {
    die;
}

// Variable de page
$search = "";
// Si une recherche est soumise
if (isset($_POST["go-search"])) {
    $search = htmlspecialchars(stripslashes(trim($_POST["search"])));
}

// Récupération de la liste des utilisateurs dans une variable
$users = get_users_list($search);

// Renvoie un code composé d'une ligne de tableau
// pour chaque utilisateur de la liste
function make_user_rows()
{
    $users = $GLOBALS["users"];

    $str = "";
    foreach ($users as $user) {
        $str .= "<tr>";
        $str .= "<td>" . $user["id"] . "</td>";
        $str .= "<td>" . $user["name"] . "</td>";
        $str .= "<td>" . $user["age"] . "</td>";
        $str .= "<td>" . $user["email"] . "</td>";
        // Lien pour la page d'édition. On met l'ID dans l'url.
        $str .= "<td>" . "<a href='./view.php?id=" . $user["id"] . "'>Modifier</a>" . "</td>";
        $str .= "</tr>";
    }

    return $str;
}

function search_user($search)
{
    var_dump($search);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Accueil</title>
</head>

<body>
    <?php include_once("./ui/header.php") ?>

    <main>

        <h1>Utilisateurs</h1>

        <form action="./" method="post" class="search-form">
            <input type="search" name="search" id="search" value="<?php echo $search; ?>">
            <input type="submit" name="go-search" value="Rechercher">
        </form>

        <div class="page-actions">
            <a href='./create.php'>+ Créer un nouvel utilisateur</a>
        </div>

        <table>
            <thead>
                <th>ID</th>
                <th>Nom</th>
                <th>Age</th>
                <th>Email</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php echo make_user_rows(); ?>
            </tbody>
        </table>

    </main>

</body>

</html>