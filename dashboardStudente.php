<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$ruolo = $_SESSION['ruolo'];

if($ruolo != "studente" || $ruolo != "admin"){
    header("Location ");
}

$materie = getMaterieStudente();

?>
<h1>Dashboard Studente</h1>

<a href="logout.php">Logout</a>

<?php if(count($materie) > 0): ?>
    <table border="1" cellpadding="10" style="margin-top:20px;">
        <thead>
            <tr>
                <th>Materia</th>
                <th>Docente</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($materie as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['materia']); ?></td>
                    <td><?php echo htmlspecialchars($m['cognome_docente']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nessuna materia assegnata.</p>
<?php endif; ?>

<?php require_once 'components/footer.php'; ?>