<?php
// Accesso al database
require_once 'connessione.php';

if (!$db = connessione::connetti()) {
    die("Connessione al database non riuscita.");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_SESSION['nickname'])) {
    header("Location: utente.php");
    exit();
}

// Controllo della registrazione
if (isset($_POST['submit'])) {

    // Sanitizzazione sicura compatibile con PHP 8+ (evita Warning/Deprecated se la chiave è nulla)
    $nome     = htmlspecialchars(trim($_POST['name'] ?? ''));
    $cognome  = htmlspecialchars(trim($_POST['cognome'] ?? '')); 
    $nickname = htmlspecialchars(trim($_POST['nickname'] ?? ''));
    $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $pass     = $_POST['pass'] ?? '';
    $cpass    = $_POST['cpass'] ?? '';

    // Controllo email esistente tramite Prepared Statement
    $query = "SELECT * FROM utenti WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {

        $messaggio = "Email già registrata.<br/><a href=\"registrazione.php\">Riprova</a>";

    } else {

        if ($pass !== $cpass) {

            $messaggio = "Le password inserite non coincidono.<br/><a href=\"registrazione.php\">Riprova</a>";

        } else {

            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            // Inserimento sicuro con Prepared Statement
            $query = "INSERT INTO utenti (nome, cognome, email, password, nickname)
                      VALUES (:nome, :cognome, :email, :password, :nickname)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'nome'     => $nome,
                'cognome'  => $cognome,
                'email'    => $email,
                'password' => $hashed_pass,
                'nickname' => $nickname
            ]);

            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load("gamePortal.xml");
            $negozioDOM = $dom->getElementsByTagName(negozio)->item(0);
            $utenteDOM = $negozioDOM->appendChild($dom->createElement('utente'));
            $utenteDOM->appendChild($dom->createElement('nickname', $nickname));
            $utenteDOM->appendChild($dom->createElement('carrello'));

            $messaggio = "Iscrizione avvenuta con successo!<br/><a href=\"login.php\">Login</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="image/logo2.jpg">
    <link rel="stylesheet" href="login_registrazione.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

<?php if (isset($messaggio)): ?>
    <div class="message">
        <span><?php echo $messaggio; ?></span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
    </div>
<?php endif; ?>

<section class="register-container">

    <form action="" method="POST" class="register-form">

        <h3 class="title">Registrati</h3>

        <div class="input-group">
            <input type="text" name="name" placeholder="Nome" required>
        </div>

        <div class="input-group">
            <input type="text" name="cognome" placeholder="Cognome" required>
        </div>

        <div class="input-group">
            <input type="text" name="nickname" placeholder="Nickname" required>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" name="pass" placeholder="Password" required>
        </div>

        <div class="input-group">
            <input type="password" name="cpass" placeholder="Conferma Password" required>
        </div>

        <input type="submit" value="Registra" class="btn" name="submit">

        <p class="login-link">
            Hai già un account?
            <a href="login.php">Login</a>
        </p>

    </form>

</section>

</body>
</html>