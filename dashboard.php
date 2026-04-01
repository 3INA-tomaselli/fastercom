<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['ruolo'] === "admin") {
    header("Location: dashboardAdmin.php");
    exit();
}
if ($_SESSION['ruolo'] === "studente") {
    header("Location: dashboardStudente.php");
    exit();
}
if ($_SESSION['ruolo'] === "docente") {
    header("Location: dashboardDocente.php");
    exit();
}

$ruolo = $_SESSION['ruolo'];