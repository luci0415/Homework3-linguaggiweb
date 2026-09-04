<?php
//accesso al database
require_once 'connessione.php';

if (!$db = connessione::connetti()) {
    echo("Connessione al database non riuscita.");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/* se già loggato → vai alla home */
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = strtolower($_POST["email"]);
    $password = $_POST["password"];
    $controllo = false;

    // Uso del prepared statement con :email
    $query = "SELECT * FROM utenti WHERE email = :email";
    $stmt = $db->prepare($query);
    
    // Esecuzione in sicurezza
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $controllo = password_verify($password, $row['password']);
    }

    if ($controllo) {

        /* SESSIONE UNIFICATA */
        $_SESSION['user'] = $email;

        header("Location: index.php");
        exit();

    } else {

        echo <<<heredoc
            <p class="message">
                Email o password errate.
            </p>
        heredoc;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="login_registrazione.css">
</head>

<body>

<div class="login-container">

    <h2 class="title">Fai l'accesso</h2>

    <form action="" method="post">

        <div class="input-group">
            <input type="text" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="button-group">

            <input type="submit" value="accedi" class="btn">
            <input type="reset" value="reset" class="btn reset">

        </div>

        <br>

        <p class="register-link">
            Crea un nuovo account:
            <a href="registrazione.php">Registrazione</a><br/>
            o <a href="index.php">accedi come ospite</a>
        </p>

    </form>

</div>

</body>
</html>