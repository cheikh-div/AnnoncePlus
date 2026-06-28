<?php
session_start();
require_once("../back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT id_categorie,nom FROM categories ORDER BY nom");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce | AnnoncePlus</title>
    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/publier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include("../back/sidebar.php"); ?>

    <div class="main">
        <?php include("../back/header.php"); ?>


        <div class="content">

            <h1>Publier une annonce</h1>

            <form action="../back/publier_action.php" method="POST" enctype="multipart/form-data"
                class="publication-form">

                <div class="form-group">
                    <label for="id_categorie">Catégorie</label>
                    <select name="id_categorie" id="id_categorie" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id_categorie']; ?>">
                                <?= htmlspecialchars($categorie['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="titre">Titre de l'annonce</label>
                    <input type="text" name="titre" id="titre" maxlength="200" placeholder="Ex : iPhone 13 Pro Max"
                        required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="6"
                        placeholder="Décrivez votre annonce en détail..." required></textarea>
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label for="prix">Prix (MRU)</label>
                        <input type="number" name="prix" id="prix" min="0" step="0.01" placeholder="0" required>
                    </div>

                    <div class="form-group">
                        <label for="localisation">Localisation</label>
                        <input type="text" name="localisation" id="localisation" placeholder="Ex : Nouakchott">
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label for="etat">État</label>
                        <select name="etat" id="etat">
                            <option value="Neuf">Neuf</option>
                            <option value="Occasion" selected>Occasion</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="visibilite">Visibilité</label>
                        <select name="visibilite" id="visibilite">
                            <option value="Publique">Publique</option>
                            <option value="Privee">Privée</option>
                        </select>
                    </div>

                </div>

                <div class="form-group">
                    <label for="images">Ajouter des images</label>
                    <div class="file-upload">
                        <input type="file" name="images[]" id="images" accept="image/*" multiple>
                        <label for="images" class="file-label">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Cliquez pour choisir des images</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-publier">
                    <i class="fa-solid fa-paper-plane"></i>
                    Publier l'annonce
                </button>

            </form>

        </div>

        <?php include("../back/footer.php"); ?>

    </div>

    <script src="../js/app.js"></script>
</body>

</html>