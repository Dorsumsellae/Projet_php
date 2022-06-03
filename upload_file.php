    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Fichier Csv</label>
        <input type="file" name="file">
        <input type="submit" value="Upload">
    </form>
    <?php
    include_once("access.php");

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        $file_type = $file['type'];
        $file_ext = explode('.', $file_name);
        $file_ext = strtolower(end($file_ext));
        $allowed = array('csv');

        $table_name = "pays";

        //command SQL to create table pays
        $create_table_req = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            nom VARCHAR(50) NOT NULL,
            code VARCHAR(3) NOT NULL,
            drapeau  BLOB,
            PRIMARY KEY (id)
        )";

        if (in_array($file_ext, $allowed)) {
            if ($file_error === 0) {
                if ($file_size <= 2097152) {
                    $file_name_new = uniqid('', true) . '.' . $file_ext;
                    $file_destination = 'uploads/' . $file_name_new;
                    if (move_uploaded_file($file_tmp_name, $file_destination)) {
                        try {
                            $dbh =  new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            if ($dbh->exec($create_table_req) === false) {
                                die(sprintf('<p class="error">la création de la table %s à été refusée <em>%s</em></p>' .
                                    "\n", $table_name, htmlspecialchars($dbh->errorInfo()[2])));
                            } else {
                                echo "<p>la table $table_name a été créée</p>\n";
                                if ($csv_file = fopen($file_destination, 'r')) {
                                    while ($line = fgetcsv($csv_file, 0, ",")) {
                                        $sql = $dbh->prepare("INSERT INTO $table_name (id, nom, code) VALUES (:id, :nom, :code)");
                                        $sql->execute([
                                            'id' => $line[0],
                                            'nom' => $line[1],
                                            'code' => $line[2],
                                        ]);
                                    }
                                    fclose($csv_file);
                                    unlink($file_destination);
                                } else {
                                    printf('<p class="error">Le fichier <samp>%s</samp> ne peut pas être ouvert en lecture.</p>' . "\n", $file_destination);
                                }
                            }
                        } catch (PDOException $myexep) {
                            die(sprintf('<p class="error">la connexion à la base de données à été refusée <em>%s</em></p>' .
                                "\n", htmlspecialchars($myexep->getMessage())));
                        }
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
    ?>