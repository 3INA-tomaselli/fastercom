<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';

if (!isset($_SESSION['email']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Fastercom</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Pannello Amministrativo</h1>
    <?php require_once 'components/navbar.php'; ?>

    <div class="admin-grid">
        <a href="admin_studenti.php" class="admin-card">
            <h3>Studenti</h3>
            <p>Visualizza e inserisci studenti</p>
        </a>
        <a href="admin_docenti.php" class="admin-card">
            <h3>Docenti</h3>
            <p>Visualizza e inserisci docenti</p>
        </a>
        <a href="admin_materie.php" class="admin-card">
            <h3>Materie & Assegnazioni</h3>
            <p>Gestisci materie e assegna docenti</p>
        </a>
    </div>

    <a href="logout.php">Logout</a>
    <?php require_once 'components/footer.php'; ?>
</body>
</html>