<?php
require_once 'connessione.php';
session_start();

$email = $_SESSION['user'] ?? null;

// Reindirizzamento se annullato o non autenticato
if (isset($_POST['azione_annulla'])) {
    header("Location: index.php");
    exit();
}
if (!$email) {
    die("<p class='error'>Errore: Utente non autenticato. <a href='index.php'>Home</a></p>");
}

// 1. Recupera dati utente dal DB
$db = connessione::connetti();
$stmt = $db->prepare("SELECT nome, cognome, nickname FROM utenti WHERE email = ?");
$stmt->execute([$email]);
$utente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utente) {
    die("<p class='error'>Errore: Utente non trovato nel DB. <a href='index.php'>Home</a></p>");
}

// 2. Carica XML e trova nodo utente
$dom = new DOMDocument("1.0", "utf-8");
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

if (!file_exists("gamePortal.xml") || !$dom->load("gamePortal.xml")) {
    die("<p class='error'>Errore: Impossibile caricare il file XML. <a href='index.php'>Home</a></p>");
}

$nodoUtente = null;
foreach ($dom->getElementsByTagName("utente") as $u) {
    if ($u->getElementsByTagName("nickname")->item(0)->nodeValue === $utente['nickname']) {
        $nodoUtente = $u;
        break;
    }
}

if (!$nodoUtente) {
    die("<p class='error'>Errore: Utente non trovato nel file XML. <a href='index.php'>Home</a></p>");
}

$carrello = $nodoUtente->getElementsByTagName("carrello")->item(0);
$metodoSelezionato = $_POST['metodo_paga'] ?? 'Carta di Credito / Debito';
$ordineConfermato = false;

// 3. Conferma Ordine e Svuota Carrello
if (isset($_POST['azione_continua']) && $carrello && $carrello->getElementsByTagName("videogioco")->length > 0) {
    // Gestione sezione <ricevute>
    $ricevute = $nodoUtente->getElementsByTagName("ricevute")->item(0) ?? $nodoUtente->appendChild($dom->createElement("ricevute"));

    // Crea nodo <ordine>
    $ordineNode = $dom->createElement("ordine");
    $ordineNode->appendChild($dom->createElement("data", date('Y-m-d H:i:s')));
    $ordineNode->appendChild($dom->createElement("metodo_pagamento", $metodoSelezionato));

    $ordineNode->appendChild($dom->createElement("nome", $utente['nome']));
    $ordineNode->appendChild($dom->createElement("cognome", $utente['cognome']));

    // Sposta i prodotti dal carrello all'ordine
    $giochi = iterator_to_array($carrello->getElementsByTagName("videogioco"));
    foreach ($giochi as $g) {
        $ordineNode->appendChild($g->cloneNode(true));
        $carrello->removeChild($g);
    }
    
    $ricevute->appendChild($ordineNode);
    $dom->save("gamePortal.xml");
    $ordineConfermato = true;
}

// 4. Prepara articoli da mostrare a schermo
$articoli = [];
if ($carrello) {
    foreach ($carrello->getElementsByTagName("videogioco") as $g) {
        $articoli[] = [
            'nome'        => $g->getElementsByTagName("nome")->item(0)->nodeValue ?? 'N/D',
            'prezzo'      => (float)($g->getElementsByTagName("prezzo")->item(0)->nodeValue ?? 0),
            'piattaforma' => $g->getElementsByTagName("piattaforma")->item(0)->nodeValue ?? '',
            'quantita'   => (int)($g->getElementsByTagName("quantita")->item(0)->nodeValue ?? 1)
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricevuta d'Acquisto</title>
    <link rel="stylesheet" href="ricevuta.css">
</head>
<body>

<!-- Modal di Conferma -->
<?php if ($ordineConfermato): ?>
    <div class="overlay">
        <div class="alert-box">
            <h1 style="color: #28a745; margin-top:0;">ORDINE EFFETTUATO!</h1>
            <p>Grazie per l'acquisto tramite <strong><?= htmlspecialchars($metodoSelezionato) ?></strong>. L'ordine è salvato e il carrello è stato svuotato.</p>
            <a href="index.php" class="btn-ok">Torna alla Home</a>
        </div>
    </div>
<?php endif; ?>

<div class="receipt">
    <h2>Ricevuta di Pagamento</h2>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($utente['nome'] . ' ' . $utente['cognome']) ?></p>

    <form method="POST">
        <div class="payment-section">
            <label for="metodo_paga">Metodo di Pagamento:</label>
            <select name="metodo_paga" id="metodo_paga">
                <?php foreach (['Carta di Credito / Debito', 'PayPal', 'Bonifico Bancario', 'Klarna (Paga a rate)'] as $metodo): ?>
                    <option value="<?= $metodo ?>" <?= $metodoSelezionato === $metodo ? 'selected' : '' ?>><?= $metodo ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr>
        <h3>Articoli Acquistati:</h3>

        <?php 
        $totaleGenerale = 0;
        foreach ($articoli as $item): 
            $subtotale = $item['prezzo'] * $item['quantita'];
            $totaleGenerale += $subtotale;
        ?>
            <div class="item">
                <div>
                    <strong><?= htmlspecialchars($item['nome']) ?></strong>
                    <?php if (!empty($item['piattaforma'])): ?>
                        <span class="item-details">(<?= htmlspecialchars($item['piattaforma']) ?>)</span>
                    <?php endif; ?>
                    <br>
                    <small class="item-details">Qtà: <?= $item['quantita'] ?> x €<?= number_format($item['prezzo'], 2) ?></small>
                </div>
                <span>€<?= number_format($subtotale, 2) ?></span>
            </div>
        <?php endforeach; ?>

        <hr>
        <div class="item total">
            <span>Totale:</span>
            <span>€<?= number_format($totaleGenerale, 2) ?></span>
        </div>

        <div class="actions-group">
            <button type="submit" name="azione_annulla" class="btn btn-secondary">Torna Indietro</button>
            <button type="submit" name="azione_continua" class="btn btn-success">Conferma</button>
        </div>
    </form>
</div>

</body>
</html>