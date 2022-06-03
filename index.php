<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dictionnaire des pays</title>
</head>

<body>
    <header>
        <h1>Dictionnaire des pays</h1>
    </header>
    <section class="search">
        <h2>Recher un pays</h2>
        <?php
        include_once("formulaire.html");
        ?>
    </section>
    <section class="countries_array">
        <h2>Liste des pays</h2>
        <?php
        include_once("countries_array.php");
        ?>
    </section>

</body>

</html>