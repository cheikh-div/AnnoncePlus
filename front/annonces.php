<?php
session_start();
require_once("../back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$idUtilisateur = $_SESSION['id_utilisateur'];
$vue = $_GET['vue'] ?? 'publique';

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT
            annonces.*,
            categories.nom AS categorie,
            (
                SELECT chemin FROM images
                WHERE images.id_annonce = annonces.id_annonce
                LIMIT 1
            ) AS image
        FROM annonces
        INNER JOIN categories ON categories.id_categorie = annonces.id_categorie
        WHERE statut='Active'";

$params = [];

if ($vue === 'privee') {
    $sql .= " AND annonces.id_utilisateur = ?";
    $params[] = $idUtilisateur;
} else {
    $sql .= " AND visibilite='Publique'";
}

if (!empty($_GET['recherche'])) {
    $sql .= " AND titre LIKE ?";
    $params[] = "%" . $_GET['recherche'] . "%";
}

if (!empty($_GET['categorie'])) {
    $sql .= " AND annonces.id_categorie = ?";
    $params[] = $_GET['categorie'];
}

$sql .= " ORDER BY date_publication DESC";

$req = $pdo->prepare($sql);
$req->execute($params);
$annonces = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces | AnnoncePlus</title>
    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/annonces.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include("../back/sidebar.php"); ?>

    <div class="overlay-sidebar" id="overlay-sidebar"></div>

    <div class="main">

        <?php include("../back/header.php"); ?>

        <div class="content">

            <h1>Annonces</h1>

            <!-- Deux boutons -->
            <div class="vue-toggle">
                <a href="?vue=publique" class="btn-vue <?= $vue === 'publique' ? 'active' : '' ?>">
                    <i class="fa fa-globe"></i> Publiques
                </a>
                <a href="?vue=privee" class="btn-vue <?= $vue === 'privee' ? 'active' : '' ?>">
                    <i class="fa fa-lock"></i> Mes annonces
                </a>
            </div>

            <form method="GET" class="search-form">
                <input type="hidden" name="vue" value="<?= htmlspecialchars($vue); ?>">
                <input type="text" name="recherche" placeholder="Rechercher une annonce..."
                    value="<?= htmlspecialchars($_GET['recherche'] ?? ''); ?>">
                <select name="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat) { ?>
                        <option value="<?= $cat['id_categorie']; ?>" <?= (!empty($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['nom']); ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit">
                    <i class="fa fa-search"></i> Rechercher
                </button>
            </form>

            <div class="cards">
                <?php if (empty($annonces)): ?>
                    <p class="no-results">Aucune annonce trouvée.</p>
                <?php else: ?>
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
                                    <a href="details.php?id=<?= (int) $annonce['id_annonce']; ?>" class="btn">Voir</a>
                                    <a href="#" class="fav"><i class="fa fa-heart"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

        <?php include("../back/footer.php"); ?>

    </div>

    <script src="../js/app.js"></script>

</body>

</html>