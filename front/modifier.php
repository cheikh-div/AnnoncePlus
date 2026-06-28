<?php
session_start();
require_once("../back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$idUtilisateur = $_SESSION['id_utilisateur'];

// إذا فيه id — اعرض فورم التعديل
if (isset($_GET['id'])) {
    $idAnnonce = (int) $_GET['id'];

    $req = $pdo->prepare("SELECT * FROM annonces WHERE id_annonce = ? AND id_utilisateur = ?");
    $req->execute([$idAnnonce, $idUtilisateur]);
    $annonce = $req->fetch(PDO::FETCH_ASSOC);

    if (!$annonce) {
        header("Location: modifier.php");
        exit();
    }

    $categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

} else {
    // بدون id — اعرض كل الإعلانات
    $req = $pdo->prepare("SELECT annonces.*, categories.nom AS categorie,
        (SELECT chemin FROM images WHERE images.id_annonce = annonces.id_annonce LIMIT 1) AS image
        FROM annonces
        INNER JOIN categories ON categories.id_categorie = annonces.id_categorie
        WHERE annonces.id_utilisateur = ?
        ORDER BY date_publication DESC");
    $req->execute([$idUtilisateur]);
    $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une annonce | AnnoncePlus</title>
    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/modifier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include("../back/sidebar.php"); ?>

    <div class="main">

        <?php include("../back/header.php"); ?>

        <div class="content">

            <?php if (isset($_GET['id'])): ?>

                <!-- ===== FORMULAIRE MODIFICATION ===== -->
                <h1>Modifier une annonce</h1>

                <form action="../back/modifier_action.php" method="POST" class="modifier-form">

                    <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce']; ?>">

                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($annonce['titre']); ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="id_categorie">Catégorie</label>
                        <select name="id_categorie" id="id_categorie">
                            <?php foreach ($categories as $cat) { ?>
                                <option value="<?= $cat['id_categorie']; ?>" <?= ($cat['id_categorie'] == $annonce['id_categorie']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prix">Prix (MRU)</label>
                            <input type="number" name="prix" id="prix" value="<?= $annonce['prix']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="localisation">Localisation</label>
                            <input type="text" name="localisation" id="localisation"
                                value="<?= htmlspecialchars($annonce['localisation']); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="etat">État</label>
                            <select name="etat" id="etat">
                                <option value="Neuf" <?= ($annonce['etat'] == "Neuf") ? 'selected' : ''; ?>>Neuf</option>
                                <option value="Occasion" <?= ($annonce['etat'] == "Occasion") ? 'selected' : ''; ?>>Occasion
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="visibilite">Visibilité</label>
                            <select name="visibilite" id="visibilite">
                                <option value="Publique" <?= ($annonce['visibilite'] == "Publique") ? 'selected' : ''; ?>>
                                    Publique</option>
                                <option value="Privee" <?= ($annonce['visibilite'] == "Privee") ? 'selected' : ''; ?>>Privée
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="6"
                            required><?= htmlspecialchars($annonce['description']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="modifier.php" class="btn-annuler">
                            <i class="fa fa-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn-enregistrer">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>

                </form>

            <?php else: ?>

                <!-- ===== LISTE DES ANNONCES ===== -->
                <h1>Mes annonces</h1>

                <?php if (empty($annonces)): ?>
                    <p class="no-results">Vous n'avez aucune annonce.</p>
                <?php else: ?>
                    <div class="cards">
                        <?php foreach ($annonces as $annonce): ?>
                            <div class="card">
                                <img src="../images/<?= htmlspecialchars($annonce['image'] ?? 'default.jpg'); ?>"
                                    alt="<?= htmlspecialchars($annonce['titre']); ?>">
                                <div class="card-body">
                                    <h3><?= htmlspecialchars($annonce['titre']); ?></h3>
                                    <p class="prix"><?= number_format($annonce['prix'], 0, ',', ' '); ?> MRU</p>
                                    <p><i class="fa fa-location-dot"></i> <?= htmlspecialchars($annonce['localisation']); ?></p>
                                    <p><?= htmlspecialchars($annonce['categorie']); ?></p>
                                    <div class="buttons">
                                        <a href="modifier.php?id=<?= $annonce['id_annonce']; ?>" class="btn-modifier-card">
                                            <i class="fa fa-pen"></i> Modifier
                                        </a>
                                        <a href="../back/supprimer_action.php?id=<?= $annonce['id_annonce']; ?>"
                                            class="btn-supprimer" onclick="return confirm('Supprimer cette annonce ?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>

        <?php include("../back/footer.php"); ?>

    </div>

    <script src="../js/app.js"></script>

</body>

</html>