<?php
session_start();
require_once("../back/connexion.php");

// Si déjà connecté → rediriger vers index
if (isset($_SESSION['id_utilisateur'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// Afficher message succès après inscription
if (isset($_SESSION['success'])) {
    $message = "<div class='success'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}

// Traitement connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<div class='error'>Veuillez remplir tous les champs.</div>";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {

            // ✅ Session créée avec la bonne clé
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];

            header("Location: ../index.php");
            exit();

        } else {
            $message = "<div class='error'>Email ou mot de passe incorrect.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion | AnnoncePlus</title>

    <link rel="stylesheet" href="../Asset/css/login.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <div class="container">

        <div class="left">

            <div class="overlay">

                <h1>AnnoncePlus</h1>

                <p>
                    Achetez, vendez et trouvez tout ce dont vous avez besoin.
                </p>

            </div>

        </div>

        <div class="right">

            <form method="POST">

                <h2>Connexion</h2>

                <p>Connectez-vous à votre compte</p>

                <?php echo $message; ?>

                <div class="input-box">

                    <i class="fa fa-envelope"></i>

                    <input type="email" name="email" placeholder="Adresse email" required>

                </div>

                <div class="input-box">

                    <i class="fa fa-lock"></i>

                    <input type="password" name="password" placeholder="Mot de passe" required>

                </div>

                <button type="submit">
                    Se connecter
                </button>

                <div class="links">
                    <a href="register.php">Créer un compte</a>
                </div>

            </form>

        </div>

    </div>

</body>

</html>