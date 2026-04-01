<?php
$host = "localhost";
$db = "fastercom";
$user = "root";
$pswd = "";

try{
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pswd
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Errore di connessione");
}