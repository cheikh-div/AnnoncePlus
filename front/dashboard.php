<?php
session_start();
require_once("../back/connexion.php");

// Vérification de session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Construction de la requête
$sql = "SELECT
           annonces.*,
           categories.nom AS categorie,
           (
               SELECT chemin
               FROM images
               WHERE images.id_annonce = annonces.id_annonce
               LIMIT 1
           ) AS image
       FROM annonces
       INNER JOIN categories
           ON categories.id_categorie = annonces.id_categorie
       WHERE statut='Active'
       AND visibilite='Publique'";

$params = [];

// Recherche
if (!empty($_GET['recherche'])) {
    $sql .= " AND titre LIKE ?";
    $params[] = "%" . $_GET['recherche'] . "%";
}

// Catégorie
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

    <title>Annonces</title>

    <link rel="stylesheet" href="../Asset/css/style.css">
    <link rel="stylesheet" href="../Asset/css/dashboard.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <?php include("../back/header.php"); ?>

    <?php include("../back/sidebar.php"); ?>

    <div class="main-content">

        <h1>Toutes les annonces</h1>

        <form method="GET" class="search-form">

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

                <i class="fa fa-search"></i>

                Rechercher

            </button>

        </form>

        <div class="cards">

            <?php if (empty($annonces)) { ?>

                <p class="no-results">Aucune annonce trouvée.</p>

            <?php } else { ?>

                <?php foreach ($annonces as $annonce) { ?>

                    <div class="card">

                        <img src="../images/<?= htmlspecialchars($annonce['image'] ?? 'default.jpg'); ?>"
                            alt="<?= htmlspecialchars($annonce['titre']); ?>">

                        <div class="card-body">

                            <h3><?= htmlspecialchars($annonce['titre']); ?></h3>

                            <p class="prix">
                                <?= number_format($annonce['prix'], 0, ',', ' '); ?> MRU
                            </p>

                            <p>
                                <i class="fa fa-location-dot"></i>
                                <?= htmlspecialchars($annonce['localisation']); ?>
                            </p>

                            <p><?= htmlspecialchars($annonce['categorie']); ?></p>

                            <div class="buttons">

                                <a href="details.php?id=<?= (int) $annonce['id_annonce']; ?>" class="btn">
                                    Voir
                                </a>

                                <a href="#" class="fav">
                                    <i class="fa fa-heart"></i>
                                </a>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            <?php } ?>

        </div>

    </div>

    <?php include("../back/footer.php"); ?>

    <script src="../js/app.js"></script>

</body>

</html>