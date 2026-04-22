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

   