<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';
require_once 'db/functions.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$ruolo = $_SESSION['ruolo'];

if($ruolo != "docente" && $ruolo != "admin"){
    header("Location: dashboardStudente.php");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $idStud = $_GET["id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idStud = $_POST["idStud"]; 

    if (empty($_POST["voto"])) {
        $errors[] = "voto mancante";
    } else {
        $voto = $_POST["voto"];
    }

    if (empty($_POST["tipo"])) {
        $errors[] = "tipo mancante";
    } else {
        $tipo = $_POST["tipo"];
    }

    if (empty($_POST["data"])) {
        $errors[] = "data mancante";
    } else {
        $data = $_POST["data"];
    }

    if (empty($_POST["nota"])) {
        $nota = NULL; 
    } else {
        $nota = $_POST["nota"];
    }

    $idDocente = $_SESSION['id'];
    if (empty($errors)) {
        $result = inserisciVoto($idStud, $idDocente, $voto, $tipo, $data, $nota);
        if($result !== true){
            $errors[] = $result;
        } else {
            $success = "Voto inserito con successo!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserimento Voto - Fastercom</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="school-header">
        <h1>Liceo P. Lodron</h1>
        <p>Registro Scolastico</p>
    </header>

    <?php require_once 'components/navbar.php'; ?>

    <h1>Inserimento Voto - Studente ID: <?= $idStud ?></h1>

    <?php if (!empty($errors)){ ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error){ ?>
                    <li><?= $error ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if (isset($success)){ ?>
        <p style="color:green;"><?= $success ?></p>
    <?php } ?>

    <form action="" method="POST">
        <input type="hidden" name="idStud" value="<?= $idStud ?>"> 

        <label for="voto">Voto</label>
        <input type="number" id="voto" name="voto" placeholder="Voto" min="1" max="10">

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo">
            <option value="" disabled selected>Seleziona tipo</option>
            <option value="scritto">Scritto</option>
            <option value="orale">Orale</option>
            <option value="pratico">Pratico</option>
        </select>

        <label for="data">Data</label>
        <input type="date" id="data" name="data">

        <label for="nota">Nota (facoltativa)</label>
        <input type="text" name="nota" placeholder="nota">

        <button>Inserisci</button>
    </form>

    <a href="dashboardDocente.php">torna alle classi</a><br>
    <a href="logout.php">Logout</a>

    <?php require_once 'components/footer.php'; ?>
</body>
</html>
