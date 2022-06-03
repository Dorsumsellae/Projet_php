<form action="" method="post" enctype="multipart/form-data">
    <label for="file">Fichier Zip</label>
    <input type="file" name="file">
    <input type="submit" value="Upload">
</form>

<?php
require_once("access.php");



//function to import img to database
function importImg(string $table, string $file_path)
{
    $fileName = explode('.', basename($file_path));
    $contryCode = $fileName[0];
    $img = file_get_contents($file_path);
    $sql = $GLOBALS['dbh']->prepare("UPDATE $table SET drapeau = ? WHERE code = ?");
    $sql->execute([$img, $contryCode]);
}

//function read bin file to determine the type of file
function readBinFile(string $file_path)
{
    $file = fopen($file_path, 'rb');
    $bin = fread($file, 4);
    fclose($file);
    preg_match('/(PNG)|(GIF)|(JPG)/', $bin, $fileExtension);
    return $fileExtension[0];
}


//import all files from folder data/flags
function importAllImg(string $path)
{
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $file_path = $path . '/' . $file;
            importImg('pays', $file_path);
        }
    }
}

//display flag from a country code
function displayFlag(string $code)
{
    $sql = $GLOBALS['dbh']->prepare("SELECT drapeau FROM pays WHERE code = ?");
    $sql->execute([$code]);
    $flag = $sql->fetch(PDO::FETCH_ASSOC);
    $flag = implode($flag);
    preg_match('/(SVG)|(PNG)|(GIF)|(JPG)/', $flag, $fileExtension);
    try {
        $flagExtension = $fileExtension[0];
        echo "<img class='flag' src='data:image/$flagExtension;base64," . base64_encode($flag) . "'>";
    } catch (Exception $e) {
        echo "<p class='error'>Le drapeau du code $code n'existe pas</p>";
    }
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
    $allowed = array('zip');

    $table_name = "pays";

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
                        $zip = new ZipArchive();
                        if ($zip->open($file_destination)) {
                            $zip->extractTo('uploads');
                            $zip->close();
                            importAllImg('uploads/flags');
                            echo "<p class='success'>Le fichier $file_name a été uploadé avec succès</p>";
                            unlink($file_destination);
                            array_map('unlink', glob("uploads/flags/*.*"));
                            rmdir('uploads/flags');
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
//close connection to database
unset($dbh);
?>
</body>

</html>