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

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, email=?, telephone=? WHERE id_utilisateur=?");
    $stmt->execute([$nom, $prenom, $email, $telephone, $idUtilisateur]);

    $message = "success";
    $utilisateur['nom'] = $nom;
    $utilisateur['prenom'] = $prenom;
    $utilisateur['email'] = $email;
    $utilisateur['telephone'] = $telephone;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil | AnnoncePlus</title>
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

            <h1>Modifier le profil</h1>

            <?php if ($message === "success"): ?>
                <div class="alert success">
                    <i class="fa fa-check-circle"></i> Profil mis à jour avec succès !
                </div>
            <?php endif; ?>

            <form method="POST" class="profil-form">

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($utilisateur['nom']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" name="prenom" id="prenom"
                        value="<?= htmlspecialchars($utilisateur['prenom']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($utilisateur['email']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" id="telephone"
                        value="<?= htmlspecialchars($utilisateur['telephone'] ?? ''); ?>">
                </div>

                <div class="form-actions">
                    <a href="profil.php" class="btn-annuler">
                        <i class="fa fa-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="btn-enregistrer">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>

            </form>

        </div>

        <?php include("../back/footer.php"); ?>
    </div>

    <script src="../js/app.js"></script>
</body>

</html>