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

if($ruolo != "studente" && $ruolo != "admin"){
    header("Location: dashboardDocente.php");
}




$email = $_SESSION['email'];

$id = getidStudenteByEmail($email);

$materie = getMaterieStudente($id);

?>
<h1>Dashboard Studente</h1>




    <table border="1" cellpadding="10" style="margin-top:20px;">
    <thead>
        <tr>
            <th>Materia</th>
            <th>Docente</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($materie)): ?>
            <?php foreach ($materie as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['materia'] ?? 'N/D'); ?></td>
                    <td><?= htmlspecialchars($m['cognome_docente'] ?? 'N/D'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Nessuna materia trovata</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<a href="logout.php">Logout</a>

<?php require_once 'components/footer.php'; ?>