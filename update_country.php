<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Modification de pays</title>
</head>

<body>
    <h1>Modification du pays :</h1>

    <?php
    require_once("access.php");

    try {
        $dbh =  new PDO("mysql:host=$host;dbname=$dbname;charset=UTF8", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $id = $_GET['id'] ?? $_POST['id'] ?? '';
        $req = sprintf("SELECT * FROM pays WHERE id = %d", $id);
        $stmt = $dbh->query($req);
        $res = $stmt->fetch();
    } catch (PDOException $myexep) {
        die(sprintf('<p class="error">la connexion à la base de données à été refusée <em>%s</em></p>' .
            "\n", htmlspecialchars($myexep->getMessage())));
    }
    $id = $res['id'];
    $nom = $res['nom'];
    $code = $res['code'];
    $drapeau = $res['drapeau'];
    ?>

    <form action="update_country.php" method="post" enctype="multipart/form-data">
        <?php
        if (!isset($_POST['nom'])) {
        ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <p>
                Pays n° : <?= $id ?? '' ?>
            </p>
            <p>
                <label for="nom">Nom du pays :</label>
                <input type="text" name="nom" id="nom" value=<?= $nom ?? '' ?> required>
            </p>
            <p>
                <label for="code">Code du pays :</label>
                <input type="text" name="code" id="code" value=<?= $code ?? '' ?> required>
            </p>
            <p>
                <label for="drapeau">Drapeau</label>
                <img class="drapeau_country" src="http://localhost/php/Projet_php/flag_image.php?id=<?= $country['id'] ?? '' ?>" alt="<?= $country['nom'] ?>">
                <input type="file" name="file" id="drapeau">
            </p>
            <p>
                <input type="submit" value="Modifier">
            </p>
    </form>
<?php

        }
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $file_name = $file['name'];
            $file_tmp_name = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];
            $file_type = $file['type'];
            $file_ext = explode('.', $file_name);
            $file_ext = strtolower(end($file_ext));
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_ext, $allowed)) {
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        $file_name_new = uniqid('', true) . '.' . $file_ext;
                        $file_destination = 'uploads/' . $file_name_new;
                        if (move_uploaded_file($file_tmp_name, $file_destination)) {
                            echo 'Uploaded';
                            $drapeau = file_get_contents($file_destination);
                            $sql = $GLOBALS['dbh']->prepare("UPDATE pays SET drapeau = ? WHERE code = ?");
                            $sql->execute([$drapeau, $code]);
                        } else {
                            echo 'Not Uploaded';
                        }
                    } else {
                        echo 'File too big';
                    }
                } else {
                    echo 'There was an error';
                }
            } else {
                echo 'File type not allowed';
            }
        }
        //Update country
        if (isset($_POST['nom']) && isset($_POST['code'])) {
            $nom = $_POST['nom'];
            $code = $_POST['code'];
            $sql = $dbh->prepare("UPDATE pays SET nom = ?, code = ?, drapeau = ? WHERE id = ?");
            $sql->execute([$nom, $code, $drapeau, $id]);

?>
    <h2>Pays modifié</h2>
    <p>
        Id : <?= $id ?? '' ?>
    </p>
    <p>
        Nom : <?= $_POST['nom'] ?? '' ?>
    </p>
    <p>
        Code : <?= $_POST['code'] ?? '' ?>
    </p>
    <p>
        <a href="index.php">Retour à la liste des pays</a>
    </p>
<?php
        }
?>
</body>

</html>