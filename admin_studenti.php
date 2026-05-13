

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_studente') {
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $nome       = trim($_POST['nome'] ?? '');
    $cognome    = trim($_POST['cognome'] ?? '');
    $classe_id  = (int)($_POST['classe_id'] ?? 0);
    $data_nascita = $_POST['data_nascita'] ?? null;
    $codice_fiscale = trim($_POST['codice_fiscale'] ?? '');

    if ($email && $password && $nome && $cognome && $classe_id && $codice_fiscale) {
        $utente_id = insertUtente($email, $password, 'studente');
        if (is_numeric($utente_id)) {
            $res = insertStudente($utente_id, $classe_id, $nome, $cognome, $data_nascita, $codice_fiscale);
            if ($res === true) {
                header("Location: admin_studenti.php?success=1");
                exit();
            } else {
                $msg = $res ?: "Errore inserimento studente";
            }
        } else {
            $msg = $utente_id ?: "Errore creazione utente";
        }
    } else {
        $msg = "Compila tutti i campi obbligatori";
    }
}

/* ---------- Eliminazione ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_studente') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $res = deleteStudente($id);
        if ($res === true) {
            header("Location: admin_studenti.php?deleted=1");
            exit();
        } else {
            $msg = $res ?: "Errore eliminazione studente";
        }
    }
}

if (isset($_GET['success'])) $msg = "Studente inserito con successo!";
if (isset($_GET['deleted'])) $msg = "Studente eliminato con successo!";

$studenti = getAllStudenti();
$classi   = getAllClassi();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Studenti - Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Gestione Studenti</h1>
    <?php require_once 'components/navbar.php'; ?>

    <?php if ($msg): ?>
        <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <h2 class="admin-section">Studenti registrati</h2>
    <table class="data-table" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Classe</th>
                <th>Data Nascita</th>
                <th>Codice Fiscale</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($studenti as $s): ?>
            <tr>
                <td><?= (int)$s['id'] ?></td>
                <td><?= htmlspecialchars($s['email'] ?? 'N/D') ?></td>
                <td><?= htmlspecialchars($s['nome']) ?></td>
                <td><?= htmlspecialchars($s['cognome']) ?></td>
                <td><?= htmlspecialchars($s['classe_nome'] ?? 'N/D') ?></td>
                <td><?= htmlspecialchars($s['data_nascita'] ?? '-') ?></td>
                <td><?= htmlspecialchars($s['codice_fiscale'] ?? '-') ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Sei sicuro di voler eliminare questo studente? Verranno rimossi anche i voti associati.');">
                        <input type="hidden" name="action" value="delete_studente">
                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                        <button type="submit" class="btn-delete">Elimina</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($studenti)): ?>
            <tr><td colspan="8">Nessuno studente trovato</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2 class="admin-section">Inserisci nuovo studente</h2>
    <form method="POST" action="" class="form-admin">
        <input type="hidden" name="action" value="add_studente">

        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Nome: <input type="text" name="nome" required></label><br>
        <label>Cognome: <input type="text" name="cognome" required></label><br>
        <label>Classe:
            <select name="classe_id" required>
                <option value="">-- Seleziona --</option>
                <?php foreach ($classi as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Data Nascita: <input type="date" name="data_nascita"></label><br>
        <label>Codice Fiscale: <input type="text" name="codice_fiscale" maxlength="16" required></label><br>

        <button type="submit">Inserisci Studente</button>
    </form>

    <p class="back-link"><a href="dashboardAdmin.php">← Torna alla Dashboard</a></p>
    <?php require_once 'components/footer.php'; ?>
</body>
</html>
