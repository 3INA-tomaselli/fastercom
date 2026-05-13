<?php
require_once 'components/session.php';
require_once 'db/connection.php';
require_once 'components/navbar.php';
require_once 'db/functions.php';

if (!isset($_SESSION['email']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = '';

/* ---------- Inserimento ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_docente') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $nome     = trim($_POST['nome'] ?? '');
    $cognome  = trim($_POST['cognome'] ?? '');

    if ($email && $password && $nome && $cognome) {
        $utente_id = insertUtente($email, $password, 'docente');
        if (is_numeric($utente_id)) {
            $res = insertDocente($utente_id, $nome, $cognome);
            if ($res === true) {
                header("Location: admin_docenti.php?success=1");
                exit();
            } else {
                $msg = $res ?: "Errore inserimento docente";
            }
        } else {
            $msg = $utente_id ?: "Errore creazione utente";
        }
    } else {
        $msg = "Compila tutti i campi obbligatori";
    }
}

/* ---------- Eliminazione ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_docente') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $res = deleteDocente($id);
        if ($res === true) {
            header("Location: admin_docenti.php?deleted=1");
            exit();
        } else {
            $msg = $res ?: "Errore eliminazione docente";
        }
    }
}

if (isset($_GET['success'])) $msg = "Docente inserito con successo!";
if (isset($_GET['deleted'])) $msg = "Docente eliminato con successo!";

$docenti = getAllDocenti();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Docenti - Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Gestione Docenti</h1>
    <?php require_once 'components/navbar.php'; ?>

    <?php if ($msg): ?>
        <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <h2>Docenti registrati</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docenti as $d): ?>
            <tr>
                <td><?= (int)$d['id'] ?></td>
                <td><?= htmlspecialchars($d['email'] ?? 'N/D') ?></td>
                <td><?= htmlspecialchars($d['nome']) ?></td>
                <td><?= htmlspecialchars($d['cognome']) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Sei sicuro di voler eliminare questo docente?');">
                        <input type="hidden" name="action" value="delete_docente">
                        <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                        <button type="submit" style="background:#c00;color:#fff;border:none;padding:4px 8px;cursor:pointer;">Elimina</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($docenti)): ?>
            <tr><td colspan="5">Nessun docente trovato</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Inserisci nuovo docente</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add_docente">

        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Nome: <input type="text" name="nome" required></label><br>
        <label>Cognome: <input type="text" name="cognome" required></label><br>

        <button type="submit">Inserisci Docente</button>
    </form>

    <p><a href="dashboardAdmin.php">← Torna alla Dashboard</a></p>
    <?php require_once 'components/footer.php'; ?>
</body>
</html>