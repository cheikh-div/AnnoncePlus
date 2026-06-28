<?php
session_start();
include("back/connexion.php");

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: front/login.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Infos utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email, photo FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id_utilisateur]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Stats avec protection try/catch
$stats = [
    'total_annonces' => 0,
    'total_publiques' => 0,
    'total_privees' => 0,
    'total_favoris' => 0,
    'total_messages' => 0,
    'total_notifications' => 0,
];

$queries = [
    'total_annonces' => "SELECT COUNT(*) FROM annonces WHERE id_utilisateur = ?",
    'total_publiques' => "SELECT COUNT(*) FROM annonces WHERE id_utilisateur = ? AND visibilite = 'Publique'",
    'total_privees' => "SELECT COUNT(*) FROM annonces WHERE id_utilisateur = ? AND visibilite = 'Privee'",
    'total_favoris' => "SELECT COUNT(*) FROM favoris WHERE id_utilisateur = ?",
    'total_messages' => "SELECT COUNT(*) FROM messages WHERE id_destinataire = ?",
    'total_notifications' => "SELECT COUNT(*) FROM notifications WHERE id_utilisateur = ?",
];

foreach ($queries as $key => $query) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_utilisateur]);
        $stats[$key] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats[$key] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>AnnoncePlus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="Asset/css/style.css">
</head>

<body>

    <?php include("back/sidebar.php"); ?>

    <div class="content">

        <header class="header">
            <div class="welcome">
                <h2>Bienvenue, <?= htmlspecialchars($user['prenom'] . " " . $user['nom']) ?></h2>
                <p>Heureux de vous revoir sur AnnoncePlus.</p>
            </div>
            <?php $photo = !empty($user['photo']) ? "images/" . $user['photo'] : "images/default.png"; ?>
            <div class="profile">
                <img src="<?= $photo ?>" alt="Photo">
            </div>
        </header>

        <div class="cards">

            <div class="card">
                <i class="fa-solid fa-list"></i>
                <h3><?= $stats['total_annonces'] ?></h3>
                <p>Total annonces</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-eye"></i>
                <h3><?= $stats['total_publiques'] ?></h3>
                <p>Publiques</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-lock"></i>
                <h3><?= $stats['total_privees'] ?></h3>
                <p>Privées</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-heart"></i>
                <h3><?= $stats['total_favoris'] ?></h3>
                <p>Favoris</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-envelope"></i>
                <h3><?= $stats['total_messages'] ?></h3>
                <p>Messages</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-bell"></i>
                <h3><?= $stats['total_notifications'] ?></h3>
                <p>Notifications</p>
            </div>

        </div>

    </div>

</body>

</html>