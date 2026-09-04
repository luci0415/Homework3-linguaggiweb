<?php
require_once 'connessione.php';

if (!$db = connessione::connetti()) {
    die("Connessione al database non riuscita.");
}

session_start();

$carrello_elementi = 0;

/* Se l'utente NON è loggato, reindirizza al login */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/* Gestione del Logout */
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Recupero dei dati dell'utente dal DB tramite email memorizzata in sessione */
$email = $_SESSION['user'];
$query = "SELECT nome, cognome, nickname, email, admin FROM utenti WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->execute(['email' => $email]);
$utente = $stmt->fetch(PDO::FETCH_ASSOC);

/* Se per qualche motivo l'utente non viene trovato nel DB */
if (!$utente) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente - <?php echo htmlspecialchars($utente['nickname']); ?></title>
    
    <!-- CSS per Navbar e Base -->
    <link rel="stylesheet" href="style.php"> <!-- o il tuo file css generale con la navbar -->
    <!-- CSS per Profilo / Login / Register -->
    <link rel="stylesheet" href="utente.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<!-- BARRA DI NAVIGAZIONE -->
    <header class="navbar">
    <!-- Elemento d'intestazione con classe 'navbar' per la navigazione principale -->
        <a href="index.php" class="logo">🎮 GamePortal</a>
        <ul class="nav-links">
            <li><a href="index.php?piattaforma=tutti">Tutti</a></li>
            <li><a href="index.php?piattaforma=switch">Switch1/Switch2</a></li>
            <li><a href="index.php?piattaforma=ps5">Playstation 5</a></li>
            <li><a href="index.php?piattaforma=xbox">Xbox</a></li>
            <li><a href="index.php?piattaforma=pc">PC</a></li>

        </ul>

        <div class="nav-right-icons">

            <a href="utente.php" class="icon-btn" title="Area Utente">
                <i class="fa-solid fa-user"></i>
            </a>

            <a href="carrello.php" class="icon-btn" title="Carrello">
            <i class="fa-solid fa-cart-shopping"></i>

            <?php if ($carrello_elementi > 0): ?>
                <span class="cart-badge"><?php echo $carrello_elementi; ?></span>
            <?php endif; ?>
        </a>
    </div>

</header>


<!-- CONTENITORE PROFILO UTENTE -->
<div class="user-profile-container">

    <div class="profile-header">
        <div class="avatar-circle">
            <i class="fas fa-user"></i>
        </div>
        <h2><?php echo htmlspecialchars($utente['nickname']); ?></h2>
        <?php if (!empty($utente['admin'])): ?>
            <span class="badge-admin">Amministratore</span>
        <?php else: ?>
            <span class="badge-user">Utente Standard</span>
        <?php endif; ?>
    </div>

    <div class="profile-details">
        <div class="detail-item">
            <span class="label"><i class="fas fa-id-card"></i> Nome completo</span>
            <span class="value"><?php echo htmlspecialchars($utente['nome'] . ' ' . $utente['cognome']); ?></span>
        </div>

        <div class="detail-item">
            <span class="label"><i class="fas fa-user-tag"></i> Nickname</span>
            <span class="value"><?php echo htmlspecialchars($utente['nickname']); ?></span>
        </div>

        <div class="detail-item">
            <span class="label"><i class="fas fa-envelope"></i> Email</span>
            <span class="value"><?php echo htmlspecialchars($utente['email']); ?></span>
        </div>
    </div>

    <!-- Punti azione: Logout e torna alla home -->
    <div class="profile-actions">
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Torna alla Home</a>
        
        <form action="" method="POST" style="flex: 1;">
            <button type="submit" name="logout" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

</div>

</body>
</html>