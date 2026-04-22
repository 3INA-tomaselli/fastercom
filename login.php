<?php
require_once "components/session.php";

if(isset($_SESSION['username'])){
    header("Location: dashboard.php");
}

require_once "db/functions.php";

$errors = [];
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["email"])) {
        $errors[] = "email mancante";
    } else {
        $email = $_POST["email"];
    }

    if (empty($_POST["password"])) {
        $errors[] = "Password mancante";
    } else {
        $password = $_POST["password"];
    }

    if (empty($errors)) {
        $loginError = checkLogin($email, $password);
        
        if (empty($loginError)) {
            $utente = getUserByEmail($email);
            $_SESSION["email"] = $utente['email'];
            $_SESSION["ruolo"] = $utente['ruolo'];
            $_SESSION['id'] = $utente['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = $loginError;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fastercom</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="school-header">
        <h1>Liceo P. Lodron</h1>
        <p>Registro Scolastico</p>
    </header>

    <?php require_once 'components/navbar.php'; ?>

    <main>
        <div class="wrapper">

            <?php if (!empty($errors)){ ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error){ ?>
                            <li><?= $error ?></li>
                        <?php }; ?>
                    </ul>
                </div>
            <?php }; ?>

            <div class="flip-card__inner">
                <div class="flip-card__front">
                    <div class="title">Log in</div>
                    <form class="flip-card__form" action="" method="POST">
                        <input class="flip-card__input" name="email" placeholder="Email" type="email">
                        <input class="flip-card__input" name="password" placeholder="Password" type="password">
                        <button class="flip-card__btn">Let`s go!</button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php require_once "components/footer.php" ?>
</body>
</html>