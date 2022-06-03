<?php
require_once('access.php');
try {
    $dbh = new PDO($dsn, $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $res = $dbh->query("SELECT * FROM pays");
    $countries = $res->fetchAll();
?>
    <div class="countries_array">
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nom</th>
                    <th>Code</th>
                    <th>Drapeau</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($countries as $country) { ?>
                    <tr>
                        <td>
                            <a class="id_country" href="http://localhost/php/Projet_php/update_country.php?id=<?= $country['id'] ?>" target="_blank"><?= $country['id'] ?></a>
                        </td>
                        <td>
                            <p class="nom_country"> <?= $country['nom'] ?></p>
                        </td>
                        <td>
                            <p class="code_country"> <?= $country['code'] ?></p>
                        </td>
                        <td>
                            <img class="drapeau_country" src="http://localhost/php/Projet_php/flag_image.php?id=<?= $country['id'] ?? '' ?>" alt="<?= $country['nom'] ?>">
                    </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>
<?php } catch (PDOException $myexep) {
    die(sprintf('<p class="error">Erreur SQL : <em>%s</em></p>' .
        "\n", htmlspecialchars($myexep->getMessage())));
} ?>