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
        $sql = "SELECT m.nome AS materia,  d.cognome AS cognome_docente FROM studenti s
        JOIN insegnamenti i ON s.classe_id = i.classe_id
        JOIN materie m ON i.materia_id = m.id
        JOIN docenti d ON i.docente_id = d.id
    WHERE s.utente_id = ? ;";
        $result = $pdo->prepare($sql);
        $result->execute([$id]);

        $materie = $result->fetchall(PDO::FETCH_ASSOC);

        return $materie;
    }
    catch(PDOException $e){
        echo "<script>alert('Errore" . $e->getMessage() . "')</script>";
    }


}