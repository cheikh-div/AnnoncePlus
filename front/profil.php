<?php
session_start();
require_once("../back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$idUtilisateur = $_SESSION['id_utilisateur'];

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    die("Utilisateur introuvable.");
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM annonces WHERE id_utilisateur = ?");
$stmt->execute([$idUtilisateur]);
$nbAnnonces = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM favoris WHERE id_utilisateur = ?");
$stmt->execute([$idUtilisateur]);
$nbFavoris = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include("../back/sidebar.php"); ?>

    <div class="main">
        <?php include("../back/header.php"); ?>

        <div class="content">

            <h1>Mon profil</h1>

            <div class="profil-container">

                <!-- صورة فقط قابلة للتغيير -->
                <div class="profil-gauche">

                    <form action="../back/update_photo.php" method="POST" enctype="multipart/form-data" id="form-photo">
                        <div class="photo-wrapper" onclick="document.getElementById('input-photo').click()">
                            <img src="../images/<?= !empty($utilisateur['photo']) ? htmlspecialchars($utilisateur['photo']) : 'default.png'; ?>"
                                class="photo-profil" id="preview-photo">
                            <div class="photo-overlay">
                                <i class="fa fa-camera"></i>
                            </div>
                        </div>
                        <input type="file" name="photo" id="input-photo" accept="image/*" style="display:none"
                            onchange="document.getElementById('form-photo').submit()">
                    </form>

                    <h2><?= htmlspecialchars($utilisateur['prenom'] . " " . $utilisateur['nom']); ?></h2>
                    <p><?= htmlspecialchars($utilisateur['email']); ?></p>
                    <?php if (!empty($utilisateur['telephone'])): ?>
                        <p><i class="fa fa-phone"></i> <?= htmlspecialchars($utilisateur['telephone']); ?></p>
                    <?php endif; ?>

                    <div class="stats">
                        <div class="stat">
                            <h3><?= $nbAnnonces ?></h3>
                            <span>Annonces</span>
                        </div>
                        <div class="stat">
                            <h3><?= $nbFavoris ?></h3>
                            <span>Favoris</span>
                        </div>
                        <div class="stat">
                            <h3><?= date("d/m/Y", strtotime($utilisateur['date_inscription'])); ?></h3>
                            <span>Inscrit le</span>
                        </div>
                    </div>

                </div>

                <!-- معلومات للعرض فقط -->
                <div class="profil-droite">

                    <div class="info-group">
                        <label><i class="fa fa-user"></i> Nom</label>
                        <p><?= htmlspecialchars($utilisateur['nom']); ?></p>
                    </div>

                    <div class="info-group">
                        <label><i class="fa fa-user"></i> Prénom</label>
                        <p><?= htmlspecialchars($utilisateur['prenom']); ?></p>
                    </div>

                    <div class="info-group">
                        <label><i class="fa fa-envelope"></i> Email</label>
                        <p><?= htmlspecialchars($utilisateur['email']); ?></p>
                    </div>

                    <div class="info-group">
                        <label><i class="fa fa-phone"></i> Téléphone</label>
                        <p><?= !empty($utilisateur['telephone']) ? htmlspecialchars($utilisateur['telephone']) : '—'; ?>
                        </p>
                    </div>

                    <div class="info-group">
                        <label><i class="fa fa-calendar"></i> Membre depuis</label>
                        <p><?= date("d/m/Y", strtotime($utilisateur['date_inscription'])); ?></p>
                    </div>

                    <div class="profil-actions">
                        <a href="modifier_profil.php" class="btn-modifier">
                            <i class="fa fa-pen"></i> Modifier le profil
                        </a>
                        <a href="changer_mot_de_passe.php" class="btn-password">
                            <i class="fa fa-lock"></i> Changer le mot de passe
                        </a>
                    </div>

                </div>

            </div>

        </div>

        <?php include("../back/footer.php"); ?>

    </div>
    <script src="../js/app.js"></script>

</body>

</html>