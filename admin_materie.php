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

/* ---------- Inserimento materia ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_materia') {
    $nome = trim($_POST['nome_materia'] ?? '');
    if ($nome) {
        $res = insertMateria($nome);
        if ($res === true) {
            header("Location: admin_materie.php?success=1");
            exit();
        } else {
            $msg = $res ?: "Errore inserimento materia";
        }
    } else {
        $msg = "Inserisci il nome della materia";
    }
}

/* ---------- Inserimento insegnamento ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_insegnamento') {
    $docente_id  = (int)($_POST['docente_id'] ?? 0);
    $materia_id  = (int)($_POST['materia_id'] ?? 0);
    $classe_id   = (int)($_POST['classe_id'] ?? 0);

    if ($docente_id && $materia_id && $classe_id) {
        $res = insertInsegnamento($docente_id, $materia_id, $classe_id);
        if ($res === true) {
            header("Location: admin_materie.php?success=2");
            exit();
        } else {
            $msg = $res ?: "Errore inserimento assegnazione";
        }
    } else {
        $msg = "Seleziona tutti i campi per l'assegnazione";
    }
}

/* ---------- Eliminazione materia ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_materia') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $res = deleteMateria($id);
        if ($res === true) {
            header("Location: admin_materie.php?deleted=1");
            exit();
        } else {
            $msg = $res ?: "Errore eliminazione materia";
        }
    }
}

/* ---------- Eliminazione insegnamento ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_insegnamento') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $res = deleteInsegnamento($id);
        if ($res === true) {
            header("Location: admin_materie.php?deleted=2");
            exit();
        } else {
            $msg = $res ?: "Errore eliminazione assegnazione";
        }
    }
}

/* ---------- Messaggi ---------- */
if (isset($_GET['success'])) {
    $msg = $_GET['success'] == '2' ? "Assegnazione completata!" : "Materia inserita!";
}
if (isset($_GET['deleted'])) {
    $msg = $_GET['deleted'] == '2' ? "Assegnazione eliminata!" : "Materia eliminata!";
}

$materie      = getAllMaterie();
$classi       = getAllClassi();
$docenti      = getAllDocenti();
$insegnamenti = getAllInsegnamenti();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Materie - Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Gestione Materie e Assegnazioni</h1>
    <?php require_once 'components/navbar.php'; ?>

    <?php if ($msg): ?>
        <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <h2>Materie disponibili</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr><th>ID</th><th>Nome</th><th>Azioni</th></tr>
        </thead>
        <tbody>
            <?php foreach ($materie as $m): ?>
            <tr>
                <td><?= (int)$m['id'] ?></td>
                <td><?= htmlspecialchars($m['nome']) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Sei sicuro di voler eliminare questa materia?');">
                        <input type="hidden" name="action" value="delete_materia">
                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <button type="submit" style="background:#c00;color:#fff;border:none;padding:4px 8px;cursor:pointer;">Elimina</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($materie)): ?>
            <tr><td colspan="3">Nessuna materia trovata</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Aggiungi materia</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add_materia">
        <label>Nome materia: <input type="text" name="nome_materia" required></label>
        <button type="submit">Aggiungi</button>
    </form>

    <hr>

    <h2>Assegnazioni Docente → Materia → Classe</h2>
    <p><em>Nota: un docente può insegnare una sola materia per classe.</em></p>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Docente</th>
                <th>Materia</th>
                <th>Classe</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($insegnamenti as $i): ?>
            <tr>
                <td><?= (int)$i['id'] ?></td>
                <td><?= htmlspecialchars($i['docente_cognome'] . ' ' . $i['docente_nome']) ?></td>
                <td><?= htmlspecialchars($i['materia_nome']) ?></td>
                <td><?= htmlspecialchars($i['classe_nome']) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Sei sicuro di voler eliminare questa assegnazione? Verranno rimossi anche i voti collegati.');">
                        <input type="hidden" name="action" value="delete_insegnamento">
                        <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
                        <button type="submit" style="background:#c00;color:#fff;border:none;padding:4px 8px;cursor:pointer;">Elimina</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($insegnamenti)): ?>
            <tr><td colspan="5">Nessuna assegnazione trovata</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Nuova assegnazione</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add_insegnamento">

        <label>Docente:
            <select name="docente_id" required>
                <option value="">-- Seleziona --</option>
                <?php foreach ($docenti as $d): ?>
                <option value="<?= (int)$d['id'] ?>"><?= htmlspecialchars($d['cognome'] . ' ' . $d['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Materia:
            <select name="materia_id" required>
                <option value="">-- Seleziona --</option>
                <?php foreach ($materie as $m): ?>
                <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Classe:
            <select name="classe_id" required>
                <option value="">-- Seleziona --</option>
                <?php foreach ($classi as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <button type="submit">Assegna</button>
    </form>

    <p><a href="dashboardAdmin.php">← Torna alla Dashboard</a></p>
    <?php require_once 'components/footer.php'; ?>
</body>
</html>