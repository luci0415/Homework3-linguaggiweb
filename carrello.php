<?php

// Importa il file che contiene la classe responsabile della connessione al database.
require_once 'connessione.php';

// Tenta di stabilire una connessione al database.
if (!$db = connessione::connetti()) {
    die("Connessione al database non riuscita.");
}

// Avvia la sessione PHP
session_start();

// Crea il documento XML
$dom = new DOMDocument("1.0", "utf-8");
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load("gamePortal.xml");

$email = $_SESSION['user'] ?? null;

if (!$email) {
    die("Funzionalità relative al carrello non accessibili come ospite");
}

$stmt = $db->prepare("SELECT nickname FROM utenti WHERE email = ?");
$stmt->execute([$email]);
$row = $stmt->fetch();

if (!$row) {
    die("Utente non trovato nel database.");
}

$nickname = $row['nickname'];
$carrello = null;

if ($utenteNodo = getUtente($nickname, $dom)) {
    $carrello = $utenteNodo->getElementsByTagName("carrello")->item(0);
} else {
    die("Utente non trovato nel file XML");
}

function getUtente($nickname, $dom) {
    $utentiDOM = $dom->getElementsByTagName("utente");
    foreach ($utentiDOM as $utente) {
        if ($utente->getElementsByTagName("nickname")->item(0)->nodeValue == $nickname) {
            return $utente;
        }
    }
    return null;
}

function aggiungiCarrello($id, $materiale, $carrello, $dom, $db) {
    $stmt = $db->prepare("SELECT stock FROM videogiochi WHERE id = ?");
    $stmt->execute([$id]);

    if (!($stmt->fetch())) {
        echo ("Spiacenti, l'articolo selezionato non è più disponibile");
    } else if ($giocoDOM = giaAggiunto($id, $materiale, $carrello)) {
        piu1($giocoDOM);
    } else {
        $stmt = $db->prepare("SELECT nome, prezzo FROM videogiochi WHERE id = ?");
        $stmt->execute([$id]);
        $gioco = $stmt->fetch();

        $stmt = $db->prepare("
            SELECT p.nome
            FROM piattaforme p
            JOIN giochi_piattaforme gp ON p.id = gp.id_piattaforma
            JOIN videogiochi v ON v.id = gp.id_gioco
            WHERE v.id = ?");
        $stmt->execute([$id]);
        $piattaforma = $stmt->fetch();

        $giocoDOM = $carrello->appendChild($dom->createElement("videogioco"));
        $giocoDOM->appendChild($dom->createElement("id", $id));
        $giocoDOM->appendChild($dom->createElement("nome", $gioco["nome"]));
        $giocoDOM->appendChild($dom->createElement("prezzo", $gioco["prezzo"]));
        $giocoDOM->appendChild($dom->createElement("piattaforma", $piattaforma['nome']));
        $giocoDOM->appendChild($dom->createElement("materiale", $materiale));
        $giocoDOM->appendChild($dom->createElement("quantita", 1));
    }
}

function piu1($giocoDOM) {
    if ($giocoDOM) {
        $giocoDOM->getElementsByTagName("quantita")->item(0)->nodeValue++;
    }
}

function giaAggiunto($id, $materiale, $carrello) {
    $videogiochiDOM = $carrello->getElementsByTagName("videogioco");
    foreach ($videogiochiDOM as $giocoDOM) {
        $itemId = $giocoDOM->getElementsByTagName("id")->item(0)->nodeValue ?? '';
        $itemMat = $giocoDOM->getElementsByTagName("materiale")->item(0)->nodeValue ?? '';

        if ($itemId == $id && $itemMat == $materiale) {
            return $giocoDOM;
        }
    }
    return null;
}

function rimuoviCarrello($id, $materiale, $carrello) {
    $giochi = $carrello->getElementsByTagName("videogioco");
    foreach ($giochi as $giocoDOM) {
        $itemId = $giocoDOM->getElementsByTagName("id")->item(0)->nodeValue ?? '';
        $itemMat = $giocoDOM->getElementsByTagName("materiale")->item(0)->nodeValue ?? '';

        if ($itemId == $id && $itemMat == $materiale) {
            if (($quantitaDOM = $giocoDOM->getElementsByTagName("quantita")->item(0))->nodeValue > 1) {
                $quantitaDOM->nodeValue--;
            } else {
                $carrello->removeChild($giocoDOM);
            }
            break;
        }
    }
}

if (isset($_POST['aggiungi'])) {
    $id = $_POST['id'];
    $materiale = $_POST['aggiungi'];
    aggiungiCarrello($id, $materiale, $carrello, $dom, $db);
}

if (isset($_POST['piu1'])) {
    $id = $_POST['id'];
    $materiale = $_POST['materiale'];
    piu1(giaAggiunto($id, $materiale, $carrello));
}

if (isset($_POST['rimuovi'])) {
    $id = $_POST['id'];
    $materiale = $_POST['materiale'];
    rimuoviCarrello($id, $materiale, $carrello);
}

$dom->save("gamePortal.xml");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrello</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>

<!-- BARRA DI NAVIGAZIONE -->
<header class="navbar">
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
    </div>
</header>

<!-- Contenuto principale -->
<main class="container">

    <h1 class="cart-title">Carrello</h1>

    <?php
    if (!$carrello->hasChildNodes()) {
        echo "<p>Carrello vuoto.</p>";
    } else {
        echo '<div class="cart-list">';
        
        $totaleGenerale = 0;

        foreach ($carrello->getElementsByTagName("videogioco") as $giocoDOM) {

            $idVal = $giocoDOM->getElementsByTagName("id")->item(0)->nodeValue;
            $nomeVal = $giocoDOM->getElementsByTagName("nome")->item(0)->nodeValue;
            $prezzoVal = floatval($giocoDOM->getElementsByTagName("prezzo")->item(0)->nodeValue);
            $piattaformaVal = $giocoDOM->getElementsByTagName("piattaforma")->item(0)->nodeValue;
            $materialeVal = $giocoDOM->getElementsByTagName("materiale")->item(0)->nodeValue;
            $quantitaVal = intval($giocoDOM->getElementsByTagName("quantita")->item(0)->nodeValue);

            $totaleGenerale += ($prezzoVal * $quantitaVal);

            echo ("
                <div class='cart-item-card'>
                    <div class='cart-item-info'>
                        <h2>{$nomeVal}</h2>
                        <div class='item-meta'>
                            <span>Piattaforma: {$piattaformaVal}</span> | 
                            <span>Formato: {$materialeVal}</span>
                        </div>
                        <div class='item-price'>€" . number_format($prezzoVal, 2) . "</div>
                    </div>

                    <div class='cart-item-actions'>
                        <form action='carrello.php' method='POST' class='cart-buttons'>
                            <input type='hidden' name='id' value='{$idVal}'>
                            <input type='hidden' name='materiale' value='{$materialeVal}'>

                            <input type='submit' name='rimuovi' value='-'>
                            <span class='cart-quantity-display'>{$quantitaVal}</span>
                            <input type='submit' name='piu1' value='+'>
                        </form>
                    </div>
                </div>
            ");
        }

        echo '</div>';
    }
    ?>

    <!-- Sezione Checkout -->
    <?php if ($carrello->hasChildNodes()): ?>
        <div class="checkout-box">
            <h3>Completa l'ordine</h3>
            
            <div class="checkout-summary">
                <span>Totale Ordine:</span>
                <span class="total-price">€<?= number_format($totaleGenerale, 2) ?></span>
            </div>

            <form action="ricevuta.php" method="POST">
                <button type="submit" name="conferma_acquisto" class="btn-checkout">
                    Procedi con l'acquisto
                </button>
            </form>
        </div>
    <?php endif; ?>

</main>
</body>
</html>