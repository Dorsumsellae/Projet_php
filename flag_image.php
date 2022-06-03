<?php
require_once('access.php');
//require_once('import_img.php');

//get id of country
$id = $_GET['id'];

//get country flag from bdd
try {
    $dbh = new PDO($dsn, $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $req = sprintf("SELECT drapeau FROM pays WHERE id = %d", $id);
    $stmt = $dbh->query($req);
    $img = $stmt->fetchColumn(0);
    header('Content-Type: image/svg+xml');
    if (empty($img)) {
        echo <<<EOI
        <svg xmlns="http://www.w3.org/2000/svg" width="500" height="500">';
        <rect width="500" height="500"  fill="orange"/> 
        <text x="20" y="150" font-family="Sans-serif" font-size="25">Pas de drapeau</text>
        </svg>
        EOI;
    } else {
        echo $img;
    }
} catch (PDOException $myexep) {
    die(sprintf('<p class="error">la connexion à la base de données à été refusée <em>%s</em></p>' .
        "\n", htmlspecialchars($myexep->getMessage())));
}
