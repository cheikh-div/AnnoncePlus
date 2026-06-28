<?php
session_start();
include("../back/connexion.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = "<div class='error'>Veuillez remplir tous les champs obligatoires.</div>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='error'>Adresse email invalide.</div>";

    } elseif ($password != $confirmPassword) {
        $message = "<div class='error'>Les mots de passe ne correspondent pas.</div>";

    } else {

        $check = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "<div class='error'>Cet email est déjà utilisé.</div>";

        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO utilisateurs 
                (nom, prenom, email, telephone, mot_de_passe) 
                VALUES (?, ?, ?, ?, ?)");

            if ($stmt->execute([$nom, $prenom, $email, $telephone, $hashedPassword])) {
                $_SESSION['success'] = "Compte créé avec succès.";
                header("Location: login.php");
                exit();
            } else {
                $message = "<div class='error'>Une erreur est survenue.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="../Asset/css/style_register.css">
</head>

<body>

    <div class="container">

        <div class="register-box">

            <h2>Créer un compte</h2>

            <?php echo $message; ?>

            <form method="POST">

                <div class="input-group">
                    <input type="text" name="nom" placeholder="Nom" required>
                </div>

                <div class="input-group">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <input type="text" name="telephone" placeholder="Téléphone">
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>

                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                </div>

                <button type="submit">S'inscrire</button>

            </form>

            <p>
                Vous avez déjà un compte ?
                <a href="login.php">Se connecter</a>
            </p>

        </div>

    </div>

</body>

</html>