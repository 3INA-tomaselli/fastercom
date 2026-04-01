<?php
require_once "components/session.php";
// distruggo la sessione e rimando lo user alla pagina di login
session_unset();
session_destroy();
header("Location: login.php");