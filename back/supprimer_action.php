<?php
session_start();
require_once("connexion.php");

// 1. التأكد من تسجيل الدخول
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../front/login.php");
    exit();
}

// 2. التحقق من وجود الـ ID
if (isset($_GET['id'])) {
    $idAnnonce = (int) $_GET['id'];
    $idUtilisateur = $_SESSION['id_utilisateur'];

    // 3. جلب مسارات الصور لحذفها من السيرفر
    $reqImages = $pdo->prepare("SELECT chemin FROM images WHERE id_annonce = ?");
    $reqImages->execute([$idAnnonce]);
    $images = $reqImages->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as $img) {
        $filePath = "../images/" . $img['chemin'];
        if (file_exists($filePath) && $img['chemin'] != 'default.jpg') {
            unlink($filePath); // حذف الملف من المجلد
        }
    }

    // 4. الحذف من قاعدة البيانات (باستخدام معامل id_utilisateur لضمان الأمان)
    // نبدأ بالحذف من الجداول الفرعية (images ثم caracteristiques ثم annonces)
    $pdo->prepare("DELETE FROM images WHERE id_annonce = ?")->execute([$idAnnonce]);
    $pdo->prepare("DELETE FROM caracteristiques WHERE id_annonce = ?")->execute([$idAnnonce]);

    $deleteAnnonce = $pdo->prepare("DELETE FROM annonces WHERE id_annonce = ? AND id_utilisateur = ?");
    $deleteAnnonce->execute([$idAnnonce, $idUtilisateur]);

    // 5. التوجيه بعد الحذف
    header("Location: ../front/modifier.php?msg=deleted");
    exit();
} else {
    header("Location: ../front/modifier.php");
    exit();
}