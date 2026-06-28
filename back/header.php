<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="header">
    <button class="hamburger" id="hamburger">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </button>
</header>