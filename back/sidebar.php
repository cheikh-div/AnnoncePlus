<?php
$current = basename($_SERVER['PHP_SELF']);
$base = (strpos($_SERVER['PHP_SELF'], '/front/') !== false) ? '' : 'front/';
?>

<aside class="sidebar">

    <div class="sidebar-logo">
        <h2>AnnoncePlus</h2>
    </div>

    <nav>
        <ul>

            <li>
                <a href="<?= $base ?>annonces.php" class="<?= $current === 'annonces.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-list"></i>
                    <span>Annonces</span>
                </a>
            </li>

            <li>
                <a href="<?= $base ?>publier.php" class="<?= $current === 'publier.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier une annonce</span>
                </a>
            </li>

            <li>
                <a href="<?= $base ?>modifier.php" class="<?= $current === 'modifier.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Modifier une annonce</span>
                </a>
            </li>

            <li>
                <a href="<?= $base ?>profil.php" class="<?= $current === 'profil.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user"></i>
                    <span>Mon profil</span>
                </a>
            </li>

            <li class="logout">
                <a href="<?= $base ?>logout.php" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Déconnexion</span>
                </a>
            </li>

        </ul>
    </nav>

</aside>