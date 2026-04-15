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
            <th>Media Voti</th>
            <th>Numero Voti</th>
            <th>Lista Voti</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($materie)): ?>
            <?php foreach ($materie as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['materia'] ?? 'N/D'); ?></td>
                    <td><?= htmlspecialchars($m['cognome_docente'] ?? 'N/D'); ?></td>
                    <td><?= $m['media_voti'] !== null ? number_format($m['media_voti'], 2) : 'N/D'; ?></td>
                    <td><?= htmlspecialchars($m['numero_voti'] ?? '0'); ?></td>
                    <td><?= htmlspecialchars($m['lista_voti'] ?? 'Nessun voto'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nessuna materia trovata</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<a href="logout.php">Logout</a>

<?php require_once 'components/footer.php'; ?>