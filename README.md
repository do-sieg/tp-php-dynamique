# TP Site web dynamique avec PHP et MySQL

Ceci est un corrigé pas à pas avec un découpage étape par étape pour la partie PHP.

Une copie de la requête de création de la table **users** se trouve dans le dossier **sql**. 

---

## Étape 1
_Avoir une page index.php qui génère et affiche la liste des utilisateurs (utiliser `<table>` de préférence, mais des div sont possibles). Créer des utilisateurs manuellement dans phpMyAdmin pour tester le visuel._

### 1.1 - Créer une page index.php
Rien de bien difficile. On crée une page avec du code html généré dans Visual Studio Code.

### 1.2 - Gérer la connexion MySQL
On peut utiliser des fonctions déjà connues. Ici, j'ai retouché la fonction que nous avions faite en classe et je l'ai renommée pour harmoniser les termes.

La fonction `db_connect()` est présente dans le fichier `database/mysql.php`. On y trouve la configuration de la base de données hors fonction.

Il ne reste plus qu'à importer cette fonction et à l'utiliser sur notre page **index.php**.

Le code utilisé sera le suivant sur chaque page :

```php
// Includes (fichiers externes)
include_once("./database/mysql.php");

// Connection à la base de données
$db = db_connect();
if ($db === null) {
    die;
}
```

Si la connection échoue, on arrête tout le processus avec `die`.

### 1.3 - Créer une fonction d'obtention de la liste d'utilisateurs
Je vous conseille de toujours créer les fonctions "en travaux" sur la page où vous travaillez. Une fois la fonction terminée et testée, vous pourrez la déplacer ailleurs.
De cette façon, vous **réduisez les allers-retours d'un fichier à l'autre** et gagnez un temps considérable.

La fonction s'appelle `get_users_list()`. Au départ, elle ressemblera à ceci :
```php
function get_users_list() {
    $db = $GLOBALS["db"];
    $query = $db->prepare("SELECT * FROM users");
    $query->execute();
    return $query->fetchAll();
}
```

Une fois terminée, la fonction est déplacée dans un fichier `database/user.php` qui contiendra toutes nos fonctions de communication avec la base de données.

On appelle ensuite ce fichier dans _index.php_ avec `include` ou `include_once`.

```php
// Includes (fichiers externes)
include_once("./database/mysql.php");
include_once("./database/user.php");
```

On récupère ensuite les données de la liste des utilisateurs ainsi :

```php
// Récupération de la liste des utilisateurs dans une variable
$users = get_users_list();
```

### 1.4 - Utiliser les données de la liste pour afficher un tableau HTML
Dans _index.php_, en partie HTML, on crée un tableau comme suit :

```html
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
```

On crée ensuite la fonction `make_user_rows()` (voir le fichier) qui permettra de générer un code HTML sous forme de balises `<tr>` successives contenant les données des utilisateurs dans des `<td>`.

Il est conseillé à ce stade de **créer quelques utilisateurs manuellement** à partir de phpMyAdmin pour vérifier l'affichage de cette liste.

---

## Étape 2
_Sur la même page, ou sur une autre (prévoir un lien de navigation ou un bouton dans ce cas), créer un formulaire qui permet d'ajouter un utilisateur avec les champs nécessaires (nom, âge, email)._

### 2.1 - Créer une nouvelle page pour l'ajout d'utilisateur
Je décide de faire une page séparée pour avoir un code plus clair, mais il est tout à fait possible de gérer un formulaire sur **index.php**.

Ma page s'appellera **create.php**.

Je remarque aussi que je vais avoir besoin de passer d'une page à l'autre. Il me faudra donc des **liens** en haut des pages.

### 2.2 - Créer un menu de navigation
Je décide de centraliser mon header et le menu de navigation dans un fichier unique `ui/header.php`.

Il contient du simple code html et on l'inclut dans nos pages comme ceci :

```php
<body>
    <?php include_once("./ui/header.php") ?>
```

### 2.3 - Ramener la connection MySQL (copier/coller)
Je ramène la **connexion à la BDD** comme pour la page précédente. Un simple copier-coller suffit.

### 2.4 - Créer un formulaire HTML
Je crée un **formulaire** dans ma partie HTML (voir le fichier **create.php**).

### 2.5 - Récupération et gestion des variables POST
Dans la partie php en haut du fichier, à la suite des includes et de la connexion à la base de données, je peux initialiser mes variables de page et de champs.

```php
// Variables de page
$page_step = 0; // Cette variable gère les étapes de la page (0 : formulaire, 1 : fin)
$err_code = "";
$err_message = "";

// Variables de champs
$name = "";
$age = "";
$email = "";
```

La variable `$page_step` est un peu spéciale. Elle décide si la page affiche un formulaire (0), ou une boîte d'information une fois un utilisateur créé (1).

Ensuite, je gère la réception des variables POST.

```php
// Si le formulaire est rempli et soumis
if (isset($_POST["go"])) {
    $name = htmlspecialchars(stripslashes(trim($_POST["name"])));
    $age = htmlspecialchars(stripslashes(trim($_POST["age"])));
    $email = htmlspecialchars(stripslashes(trim($_POST["email"])));

}
```

J'anticipe le **nettoyage** des champs du formulaire (étape 6).

### 2.6 - Créer une fonction d'ajout d'utilisateur dans la BDD
Je vais créer une fonction `create_user()` pour ajouter un utilisateur dans la base de données.

La fonction sera ensuite déplacée dans `database/users.php`.

C'est une fonction assez complexe qu'il va falloir étudier pas à pas (lignes 26 à 51).
- Elle crée un tableau associatif `response` qui contient trois infos : le succès, un code d'erreur et un message d'erreur. Le succès est **faux par défaut**.
- Ce tableau est renvoyé à la toute fin.
- Une première structure conditionnelle `if/else` gère les cas où un des champs du formulaire au moins serait **vide**. Si c'est le cas, on renvoie une erreur.
- Si les champs sont bien remplis, on peut procéder à la requête INSERT.
- Si tout s'est bien passé, on passe la clé `success` à `true`. Sinon, on renvoie l'erreur transmise.

### 2.7 - Affichage des erreurs sous le formulaire
De retour dans **create.php**, on examine la réponse retournée par la fonction qu'on vient de créer.

```php
    $response = create_user($name, $age, $email);

    if ($response["success"] === true) {
        $page_step = 1;
    } else {
        $err_code = $response["err_code"];
        $err_message = $response["err_message"];
        // Note : il est possible ici de récupérer le code
        // et de mettre nos propres messages d'erreur
    }
```

Il est conseillé de gérer les erreurs en premier, donc le `else` de cette condition. Dans ce bloc de code, on change deux variables (`$err_code` et `$err_message`).

Ensuite, dans la partie html, on place ce code à la suite du formulaire :

```php
<?php
if ($err_message) {
    echo "<p>$err_message</p>";
}
?>
```

### 2.8 - Boîte d'information avec lien vers accueil
Toujours dans la partie html, on va placer le formulaire entre ces deux lignes :

```php
<?php if ($page_step === 0) { ?>

// formulaire html
// affichage de l'erreur

<?php } ?>
```

Ceci fait en sorte que le formulaire ne soit affiché que si on est à l'étape 0.

Enfin, on place ce code à la suite :

```php
<?php if ($page_step === 1) { ?>

    <p>Utilisateur créé !</p>
    <a href="./">Retour</a>

<?php } ?>
```

Quand on aura créé un utilisateur, le code (le `if` un peu plus haut) change la valeur de la variable `$page_step`.

Et quand c'est le cas, le formulaire n'est plus affiché, mais on a à la place un simple message et un lien vers la page d'accueil.

---

## Étape 3
_Dans la liste, à droite de chaque utilisateur, mettre un bouton qui permet d'aller vers une page view.php où on peut accéder à un autre formulaire qui contient les données de l'utilisateur. Utiliser l'url et GET pour transmettre l'id de l'utilisateur.  
Bonus : afficher une erreur si l'id de l'url est invalide (pas d'utilisateur avec cet ID)._

### 3.1 - Ajouter le lien dans le tableau html
Dans le tableau créé en **index.php**, on ajoute un lien vers une page **view.php** avec pour paramètre `id` l'ID de l'utilisateur.

```php
// Lien pour la page d'édition. On met l'ID dans l'url.
$str .= "<td>" . "<a href='./view.php?id=" . $user["id"] . "'>Modifier</a>" . "</td>";
$str .= "</tr>";
```

### 3.2 - Créer la page view.php
Cette page est assez similaire à **create.php**.
On en copiera :
- les includes
- la connection à la BDD
- le formulaire HTML en le remaniant un peu
- les variables de page et de champs (lignes 13 à 21).

### 3.3 - Fonctionnement de la page view.php
Cette page gère plusieurs choses :
- le formulaire n'apparaît que si l'ID est valide (lignes 14 et 31)
- les données de l'utilisateur sont récupérées par la fonction `get_single_user()` qui effectue une requête SELECT basée sur l'ID de l'utilisateur. Ces données sont attribuées à certaines de nos variables de formulaire (lignes 27 à 30).
- si la base de données ne trouve rien (pas d'ID, ID invalide), on reste avec -1 pour `$page_step`, qui affichera une erreur plus bas dans la partie html (lignes 125 à 130).

---

## Étape 4
_Le formulaire de view.php permet de modifier les données de l'utilisateur._

Le reste (lignes 36 à 52, 87 à 106, 109 à 114) est presque identique à la page **create.php**.
La seule chose qui change est la fonction `update_user()` qui met à jour les données de l'utilisateur.

---

## Étape 5
_Prévoir un bouton dans view.php pour supprimer l'utilisateur._

Je saute le bonus ici parce qu'il est impossible à faire en php pur. On oublie donc la confirmation.

Voici les étapes à suivre :
1. Ajouter un bouton au formulaire pour lancer la suppression (ligne 97)
2. Créer une fonction de suppression d'utilisateur pour la BDD (`database/user.php`, lignes 82 à 95)
3. Appeler cette fonction et gérer ses résultats en cas de succès ou d'échec (lignes 54 à 64)
4. Afficher un message de confirmation de la suppression (lignes 125 à 130).

---

## Étape 6
_Nettoyer les champs envoyés par les différents formulaires (suppression des espaces superflus, nettoyage des balises html, etc...)._

Un exemple d'un tel nettoyage se trouve dans **view.php** aux lignes 37 à 39.

---

## Étape 7
_Bonus : faire une barre de recherche sur index.php (input + bouton) pour filtrer les utilisateurs sur leur nom et/ou leur email (si on tape "al" et qu'on appuie sur le bouton de recherche, la liste affiche les utilisateurs dont le nom et/ou l'email contient "al")._

Voici les étapes pour cette partie bonus :
1. Ajouter un formulaire de recherche sur index.php (input type search et bouton submit), voir lignes 69 à 72.
2. Récupérer le texte entré quand on soumet la recherche (POST), voir lignes 13 à 18.
3. Adapter la fonction `get_users_list()` pour gérer la recherche, voir lignes 14 et 21, et `database/user.php` aux lignes 4 à 10.

La liste se mettra à jour automatiquement car on **recharge la page** avec les formulaires. Cela inclut le formulaire de recherche.

---

Voilà, si quelque chose n'est pas clair, vous pouvez me contacter sur Discord ou par email à d.orchanian@gmail.com.