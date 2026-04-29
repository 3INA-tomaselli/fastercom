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

$id = $_SESSION['id'];
$classi = getNomiClassi($id); 

?>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Docenti - Fastercom</title>
    <link rel="stylesheet" href="assets/style.css">
 </head>

<h1 class = h1docente>Dashboard Docenti</h1>
<?php require_once 'components/navbar.php'; ?>
 <?php if ($classi): ?>
    
    <?php
$idClasse = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : null;
?>

<form method="GET" action="">
    <label for="classe_id">Seleziona classe:</label>
    <select name="classe_id" id="classe_id" onchange="this.form.submit()">
        <option value="">-- Scegli una classe --</option>
        <?php foreach ($classi as $classe): ?>
            <option 
                value="<?= $classe['classe_id'] ?>" 
                <?= isset($_GET['classe_id']) && $_GET['classe_id'] == $classe['classe_id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($classe['classe_nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

    <?php if ($idClasse != 0): ?>
        <?php $info = getInfoXDocente($idClasse, $id) ?>


        <table border="1" cellpadding="10" style="margin-top:20px;">
    <thead>
        <tr>
            <th>Id dello studente</th>
            <th>Nome dello studente</th>
            <th>Cognome dello studente</th>
            <th>Classe</th>
            <th>media dei voti</th>
            <th>inserisci nuovo voto</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($info)): ?>
            <?php foreach ($info as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['studente_id'] ?? 'N/D'); ?></td>
                    <td><?= htmlspecialchars($i['studente_nome'] ?? 'N/D'); ?></td>
                    <td><?= htmlspecialchars($i['studente_cognome']); ?></td>
                    <td><?= htmlspecialchars($i['classe_nome'] ); ?></td>
                    <td><?= $i['media_voti']; ?></td>
                     <td><a href="inserimentoVoto.php?id=<?=$i['studente_id']?>">inserisci voto</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nessuna materia trovata</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>







    <?php endif; ?>
    
<?php else: ?>
<p>non ci sono classi associate</p>
<?php endif; ?>  
 
<a class = docenteLogout href="logout.php">Logout</a>

<?php require_once 'components/footer.php'; ?>