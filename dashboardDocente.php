<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$ruolo = $_SESSION['ruolo'];

if($ruolo != "docente" && $ruolo != "admin"){

    header("Location: dashboardStudente.php");
}

?>

<h1>Dashboard Docenti</h1>


<a href="logout.php">Logout</a>

<?php require_once 'components/footer.php'; ?>