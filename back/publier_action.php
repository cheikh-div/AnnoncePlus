<?php
session_start();
require_once("connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../front/login.php");
    exit();
}

$idUtilisateur = $_SESSION['id_utilisateur'];
$titre = trim($_POST['titre']);
$description = trim($_POST['description']);
$prix = $_POST['prix'];
$localisation = trim($_POST['localisation']);
$etat = $_POST['etat'];
$visibilite = $_POST['visibilite'];
$id_categorie = $_POST['id_categorie'];

// Insérer l'annonce
$stmt = $pdo->prepare("INSERT INTO annonces 
    (id_utilisateur, id_categorie, titre, description, prix, localisation, etat, visibilite, statut)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')");

$stmt->execute([
    $idUtilisateur,
    $id_categorie,
    $titre,
    $description,
    $prix,
    $localisation,
    $etat,
    $visibilite
]);

$idAnnonce = $pdo->lastInsertId();

// Upload des images
if (!empty($_FILES['images']['name'][0])) {
    $uploadDir = "../images/";

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
        if ($_FILES['images']['error'][$key] === 0) {
            $nom = uniqid() . "_" . basename($_FILES['images']['name'][$key]);
            $destination = $uploadDir . $nom;

            if (move_uploaded_file($tmp, $destination)) {
                $stmt = $pdo->prepare("INSERT INTO images (id_annonce, chemin) VALUES (?, ?)");
                $stmt->execute([$idAnnonce, $nom]);
            }
        }
    }
}

header("Location: ../front/annonces.php");
exit();
?>