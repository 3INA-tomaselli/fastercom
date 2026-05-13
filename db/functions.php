<?php
require_once 'connection.php';
require_once 'components/session.php';


function checkLogin($email, $password){
    global $pdo;

    try{
        $error = "";

        $sql = "SELECT * FROM utenti WHERE email = ?";
        $result = $pdo->prepare($sql);
        $result->execute([$email]);

        $utente = $result->fetch(PDO::FETCH_ASSOC);

        if (empty($utente)) {
            $error = "Email non esistente!";
        }
        else{
            if(!password_verify($password,$utente['password_hash'])){
                $error = "Password errata!";
            }
        }
        return $error;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore" . $e->getMessage() . "')</script>";
    }
}

function getUserByEmail($email){
    global $pdo;

    try{
        $sql = "SELECT * FROM utenti WHERE email = ?";
        $result = $pdo->prepare($sql);
        $result->execute([$email]);

        $utente = $result->fetch(PDO::FETCH_ASSOC);

        return $utente;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore" . $e->getMessage() . "')</script>";
    }
}
function getidStudenteByEmail($email){
    global $pdo;

    try{
        $sql = "SELECT id FROM utenti WHERE email = ?";
        $result = $pdo->prepare($sql);
        $result->execute([$email]);

        $row = $result->fetch(PDO::FETCH_ASSOC);

        return $row['id'] ?? null;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore " . $e->getMessage() . "')</script>";
    }
}

function getMaterieStudente($id){
    global $pdo;
    $email = $_SESSION["email"];
    try{
        $sql = "SELECT 
    m.nome AS materia,
    d.cognome AS cognome_docente,
    AVG(v.valore) AS media_voti,
    COUNT(v.id) AS numero_voti,
    GROUP_CONCAT(v.valore ORDER BY v.data SEPARATOR ', ') AS lista_voti
FROM studenti s
JOIN insegnamenti i ON s.classe_id = i.classe_id
JOIN materie m ON i.materia_id = m.id
JOIN docenti d ON i.docente_id = d.id
LEFT JOIN voti v 
    ON v.insegnamento_id = i.id 
   AND v.studente_id = s.id
WHERE s.utente_id = ?
GROUP BY m.id, d.id; ;";
        $result = $pdo->prepare($sql);
        $result->execute([$id]);

        $materie = $result->fetchall(PDO::FETCH_ASSOC);

        return $materie;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore" . $e->getMessage() . "')</script>";
    }


}

function getNomiClassi($id) {
global $pdo;

    try{
        $sql = "SELECT
    c.id   AS classe_id,
    c.nome AS classe_nome
FROM insegnamenti i
JOIN classi c ON c.id = i.classe_id
WHERE i.docente_id = (
    SELECT id FROM docenti WHERE utente_id = ?
)
GROUP BY c.id, c.nome
ORDER BY c.nome;";

        $result = $pdo->prepare($sql);
        $result->execute([$id]);

        $classi= $result->fetchAll(PDO::FETCH_ASSOC);

        return $classi;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore " . $e->getMessage() . "')</script>";
    }
    
}


function getInfoXDocente($idClasse, $idDocente) {
    global $pdo;

    try{
        $sql = "SELECT
    s.id          AS studente_id,
    s.nome        AS studente_nome,
    s.cognome     AS studente_cognome,
    c.nome        AS classe_nome,
    ROUND(AVG(v.valore), 2) AS media_voti
FROM studenti s
JOIN classi c ON c.id = s.classe_id
LEFT JOIN voti v ON v.studente_id = s.id
WHERE s.classe_id = ?
AND s.classe_id IN (
    SELECT i.classe_id
    FROM insegnamenti i
    WHERE i.docente_id = (
        SELECT id FROM docenti WHERE utente_id = ?
    )
)
GROUP BY s.id, s.nome, s.cognome, c.nome
ORDER BY s.cognome, s.nome;";
        $result = $pdo->prepare($sql);
        $result->execute([$idClasse, $idDocente]);

        $info = $result->fetchAll(PDO::FETCH_ASSOC);

        return $info;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore " . $e->getMessage() . "')</script>";
    }

    
}




function inserisciVoto($idStud, $idDocente, $voto, $tipo, $data, $nota) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        // 0. Ricava il docente_id reale dall'utente_id in sessione
        $sqlDocente = "SELECT id FROM docenti WHERE utente_id = :idDocente";
        $stmtDocente = $pdo->prepare($sqlDocente);
        $stmtDocente->execute([':idDocente' => $idDocente]);
        $docente = $stmtDocente->fetch(PDO::FETCH_ASSOC);

        if (!$docente) {
            $pdo->rollBack();
            return "Docente non trovato";
        }

        $docenteId = $docente['id'];

        // 1. Recupera l'id della materia
        $sql1 = "SELECT m.id as materia_id
                 FROM materie m
                 JOIN insegnamenti i ON i.materia_id = m.id
                 JOIN studenti s ON s.classe_id = i.classe_id
                 WHERE s.id = :idStud
                   AND i.docente_id = :docenteId
                 LIMIT 1";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([':idStud' => $idStud, ':docenteId' => $docenteId]);
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $pdo->rollBack();
            return "Nessun insegnamento trovato per questo studente e docente";
        }

        $materiaId = $row['materia_id'];

        // 2. Inserisce il voto
        $sql2 = "INSERT INTO voti (insegnamento_id, studente_id, valore, tipo, data, nota)
                 VALUES (
                     (
                         SELECT i.id
                         FROM insegnamenti i
                         WHERE i.classe_id = (
                                 SELECT classe_id 
                                 FROM studenti 
                                 WHERE id = :idStud
                             )
                           AND i.docente_id = :docenteId
                           AND i.materia_id = :materiaId
                     ),
                     :idStud,
                     :voto,
                     :tipo,
                     :data,
                     :nota
                 )";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':idStud'    => $idStud,
            ':docenteId' => $docenteId,
            ':materiaId' => $materiaId,
            ':voto'      => $voto,
            ':tipo'      => $tipo,
            ':data'      => $data,
            ':nota'      => $nota
        ]);

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        return "Qualcosa è andato storto: " . $e->getMessage();
    }
}

function getAllStudenti() {
    global $pdo;
    try {
        $sql = "SELECT s.id, s.nome, s.cognome, s.data_nascita, s.codice_fiscale,
                       c.nome AS classe_nome, u.email
                FROM studenti s
                LEFT JOIN classi c ON c.id = s.classe_id
                LEFT JOIN utenti u ON u.id = s.utente_id
                ORDER BY s.cognome, s.nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore getAllStudenti: " . $e->getMessage());
        return [];
    }
}

function getAllDocenti() {
    global $pdo;
    try {
        $sql = "SELECT d.id, d.nome, d.cognome, u.email
                FROM docenti d
                LEFT JOIN utenti u ON u.id = d.utente_id
                ORDER BY d.cognome, d.nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore getAllDocenti: " . $e->getMessage());
        return [];
    }
}

function getAllMaterie() {
    global $pdo;
    try {
        $sql = "SELECT * FROM materie ORDER BY nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore getAllMaterie: " . $e->getMessage());
        return [];
    }
}

function getAllClassi() {
    global $pdo;
    try {
        $sql = "SELECT * FROM classi ORDER BY nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore getAllClassi: " . $e->getMessage());
        return [];
    }
}

function getAllInsegnamenti() {
    global $pdo;
    try {
        $sql = "SELECT i.id,
                       d.nome AS docente_nome, d.cognome AS docente_cognome,
                       m.nome AS materia_nome, c.nome AS classe_nome
                FROM insegnamenti i
                JOIN docenti d ON d.id = i.docente_id
                JOIN materie m ON m.id = i.materia_id
                JOIN classi c ON c.id = i.classe_id
                ORDER BY c.nome, m.nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore getAllInsegnamenti: " . $e->getMessage());
        return [];
    }
}

function insertUtente($email, $password, $ruolo) {
    global $pdo;
    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql  = "INSERT INTO utenti (email, password_hash, ruolo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $hash, $ruolo]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Email già esistente";
        }
        error_log("Errore insertUtente: " . $e->getMessage());
        return false;
    }
}

function insertStudente($utente_id, $classe_id, $nome, $cognome, $data_nascita, $codice_fiscale) {
    global $pdo;
    try {
        $sql = "INSERT INTO studenti (utente_id, classe_id, nome, cognome, data_nascita, codice_fiscale)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$utente_id, $classe_id, $nome, $cognome, $data_nascita, $codice_fiscale]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Codice fiscale già esistente";
        }
        error_log("Errore insertStudente: " . $e->getMessage());
        return false;
    }
}

function insertDocente($utente_id, $nome, $cognome) {
    global $pdo;
    try {
        $sql = "INSERT INTO docenti (utente_id, nome, cognome) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$utente_id, $nome, $cognome]);
        return true;
    } catch (PDOException $e) {
        error_log("Errore insertDocente: " . $e->getMessage());
        return false;
    }
}

function insertMateria($nome) {
    global $pdo;
    try {
        $sql = "INSERT INTO materie (nome) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Materia già esistente";
        }
        error_log("Errore insertMateria: " . $e->getMessage());
        return false;
    }
}

function insertInsegnamento($docente_id, $materia_id, $classe_id) {
    global $pdo;
    try {
        $sql = "INSERT INTO insegnamenti (docente_id, materia_id, classe_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$docente_id, $materia_id, $classe_id]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Assegnazione già esistente per questo docente, materia e classe";
        }
        error_log("Errore insertInsegnamento: " . $e->getMessage());
        return false;
    }
}

function deleteStudente($studente_id) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT utente_id FROM studenti WHERE id = ?");
        $stmt->execute([$studente_id]);
        $utente_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare("DELETE FROM studenti WHERE id = ?");
        $stmt->execute([$studente_id]);
        if ($utente_id) {
            $stmt = $pdo->prepare("DELETE FROM utenti WHERE id = ?");
            $stmt->execute([$utente_id]);
        }
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Errore deleteStudente: " . $e->getMessage());
        return "Errore durante l'eliminazione dello studente";
    }
}

function deleteDocente($docente_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM insegnamenti WHERE docente_id = ?");
        $stmt->execute([$docente_id]);
        if ($stmt->fetchColumn() > 0) {
            return "Impossibile eliminare: il docente ha insegnamenti associati. Elimina prima le assegnazioni dalla pagina Materie.";
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT utente_id FROM docenti WHERE id = ?");
        $stmt->execute([$docente_id]);
        $utente_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare("DELETE FROM docenti WHERE id = ?");
        $stmt->execute([$docente_id]);
        if ($utente_id) {
            $stmt = $pdo->prepare("DELETE FROM utenti WHERE id = ?");
            $stmt->execute([$utente_id]);
        }
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Errore deleteDocente: " . $e->getMessage());
        return "Errore durante l'eliminazione del docente";
    }
}

function deleteMateria($materia_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM insegnamenti WHERE materia_id = ?");
        $stmt->execute([$materia_id]);
        if ($stmt->fetchColumn() > 0) {
            return "Impossibile eliminare: la materia è usata in una o più assegnazioni. Elimina prima le assegnazioni.";
        }
        $stmt = $pdo->prepare("DELETE FROM materie WHERE id = ?");
        $stmt->execute([$materia_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Errore deleteMateria: " . $e->getMessage());
        return "Errore durante l'eliminazione della materia";
    }
}

function deleteInsegnamento($insegnamento_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM insegnamenti WHERE id = ?");
        $stmt->execute([$insegnamento_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Errore deleteInsegnamento: " . $e->getMessage());
        return "Errore durante l'eliminazione dell'assegnazione";
    }
}