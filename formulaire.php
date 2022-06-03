<?php
require_once("access.php");
try {
  $dbh =  new PDO("mysql:host=$host;dbname=$dbname;charset=UTF8", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $req = sprintf("SELECT * FROM pays WHERE id = %d or nom = %s or code = %s", $_GET['id'], $dbh->quote($_GET['nom']), $dbh->quote($_GET['code']));
  $stmt = $dbh->query($req);
  $res = $stmt->fetch();
  // print_r($res);
} catch (PDOException $myexep) {
  die(sprintf('<p class="error">la connexion à la base de données à été refusée <em>%s</em></p>' .
    "\n", htmlspecialchars($myexep->getMessage())));
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Résultat de la recherche dans la BDD</title>
  <style type="text/css">
    table,
    td,
    th {
      border: 1px solid #000;
      margin: auto;
    }
  </style>
</head>

<body>
  <table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Code</th>
        <th>Drapeau</th>
    </tr>
    <tr> 
      <td><?= $res['id'] ?? '' ?></td>
      <td><?= $res['nom'] ?? '' ?></td>
      <td><?= $res['code'] ?? '' ?></td>
    </tr>
  </table>

</body>

</html>