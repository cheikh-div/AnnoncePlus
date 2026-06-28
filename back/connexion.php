``
<?php
/****************************************************************
 * connexion.php
 * Connexion à la base de données avec PDO
 ****************************************************************/

$host = "localhost";
$dbname = "ann";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur de connexion : " . $e->getMessage());

}
?>``