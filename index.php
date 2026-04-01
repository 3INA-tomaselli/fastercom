<?php
require_once 'db/connection.php';
require_once 'components/session.php';


if (isset($_SESSION['user'])) {
    if($_SESSION['ruolo'] == 'admin') {
        header("Location: dashboardAdmin.php");
        exit();
    } elseif($_SESSION['ruolo'] == 'docente') {
        header("Location: dashboardDocente.php");
        exit();
    } elseif($_SESSION['ruolo'] == 'studente') {
        header("Location: dashboardStudente.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>