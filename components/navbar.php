<nav>
    <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'studente'): ?>
        <a href="dashboardStudente.php">Studenti</a>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['ruolo']) && ($_SESSION['ruolo'] === 'docente' || $_SESSION['ruolo'] === 'admin')): ?>
        <a href="dashboardDocente.php">Docenti</a>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'admin'): ?>
        <a href="dashboardAdmin.php">Amministratori</a>
    <?php endif; ?>
    
    <a href="../fastercom/logout.php">Logout</a>
</nav>