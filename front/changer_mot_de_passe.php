<?php
session_start();
require_once("../back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$idUtilisateur = $_SESSION['id_utilisateur'];
$message = "";
$type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien = $_POST['ancien_mot_de_passe'];
    $nouveau = $_POST['nouveau_mot_de_passe'];
    $confirmer = $_POST['confirmer_mot_de_passe'];

    $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$idUtilisateur]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($ancien, $user['mot_de_passe'])) {
        $message = "Ancien mot de passe incorrect.";
        $type = "error";
    } elseif ($nouveau !== $confirmer) {
        $message = "Les mots de passe ne correspondent pas.";
        $type = "error";
    } elseif (strlen($nouveau) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
        $type = "error";
    } else {
        $hash = password_hash($nouveau, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe=? WHERE id_utilisateur=?");
        $stmt->execute([$hash, $idUtilisateur]);
        $message = "Mot de passe modifié avec succès !";
        $type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe | AnnoncePlus</title>
    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/modifier_profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include("../back/sidebar.php"); ?>
    <div class="overlay-sidebar" id="overlay-sidebar"></div>

    <div class="main">
        <?php include("../back/header.php"); ?>

        <div class="content">

            <h1>Changer le mot de passe</h1>

            <?php if ($message): ?>
                <div class="alert <?= $type; ?>">
                    <i class="fa <?= $type === 'success' ? 'fa-check-circle' : 'fa-circle-exclamation'; ?>"></i>
                        <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="profil-form">

                <div class="form-group">
                    <label for="ancien">Ancien mot de passe</label>
                    <input type="password" name="ancien_mot_de_passe" id="ancien" required>
                </div>

                <div class="form-group">
                    <label for="nouveau">Nouveau mot de passe</label>
                    <input type="password" name="nouveau_mot_de_passe" id="nouveau" required>
                </div>

                <div class="form-group">
                    <label for="confirmer">Confirmer le mot de passe</label>
                    <input type="password" name="confirmer_mot_de_passe" id="confirmer" required>
                </div>

                <div class="form-actions">
                    <a href="profil.php" class="btn-annuler">
                        <i class="fa fa-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="btn-enregistrer">
                        <i class="fa fa-lock"></i> Modifier
                    </button>
                </div>

            </form>

        </div>

        <?php include("../back/footer.php"); ?>
    </div>

    <script src="../js/app.js"></script>
</body>

</html>