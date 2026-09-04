<?php

require_once 'connessione.php';

// Inizializzazione variabili Home
$giochi_evidenza = [];
$top_pc = [];
$top_xbox = [];
$top_ps5 = [];
$top_switch = [];
$giochi_filtrati = [];

$piattaforma_selezionata = $_GET['piattaforma'] ?? null;

try {
    $db = connessione::connetti();

    if ($piattaforma_selezionata === 'tutti') {
        $queryTutti = "
            SELECT v.*, GROUP_CONCAT(g.genere SEPARATOR ', ') AS generi
            FROM videogiochi v
            LEFT JOIN giochi_generi gg ON v.id = gg.id_gioco
            LEFT JOIN generi g ON gg.id_genere = g.id
            GROUP BY v.id
            ORDER BY v.nome ASC
        ";
        $stmtTutti = $db->query($queryTutti);
        $giochi_filtrati = $stmtTutti->fetchAll(PDO::FETCH_ASSOC);

    } elseif (!empty($piattaforma_selezionata)) {
        $queryFiltrata = "
            SELECT v.*, GROUP_CONCAT(g.genere SEPARATOR ', ') AS generi
            FROM videogiochi v
            JOIN giochi_piattaforme gp ON v.id = gp.id_gioco
            JOIN piattaforme p ON gp.id_piattaforma = p.id
            LEFT JOIN giochi_generi gg ON v.id = gg.id_gioco
            LEFT JOIN generi g ON gg.id_genere = g.id
            WHERE LOWER(p.nome) LIKE LOWER(:piattaforma)
            GROUP BY v.id
            ORDER BY v.nome ASC
        ";
        $stmtFiltrata = $db->prepare($queryFiltrata);
        $stmtFiltrata->bindValue(':piattaforma', '%' . $piattaforma_selezionata . '%', PDO::PARAM_STR);
        $stmtFiltrata->execute();
        $giochi_filtrati = $stmtFiltrata->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // Vista Home standard
        $queryEvidenza = "
            SELECT v.*, GROUP_CONCAT(g.genere SEPARATOR ', ') AS generi
            FROM videogiochi v
            LEFT JOIN giochi_generi gg ON v.id = gg.id_gioco
            LEFT JOIN generi g ON gg.id_genere = g.id
            GROUP BY v.id
            ORDER BY v.stock ASC
            LIMIT 6
        ";
        $stmtEvidenza = $db->query($queryEvidenza);
        $giochi_evidenza = $stmtEvidenza->fetchAll(PDO::FETCH_ASSOC);

        function getTop3PerPiattaforma($db, $nome_piattaforma) {
            $sql = "
                SELECT v.*, GROUP_CONCAT(g.genere SEPARATOR ', ') AS generi
                FROM videogiochi v
                JOIN giochi_piattaforme gp ON v.id = gp.id_gioco
                JOIN piattaforme p ON gp.id_piattaforma = p.id
                LEFT JOIN giochi_generi gg ON v.id = gg.id_gioco
                LEFT JOIN generi g ON gg.id_genere = g.id
                WHERE LOWER(p.nome) LIKE LOWER(:piattaforma)
                GROUP BY v.id
                ORDER BY v.acquistati DESC
                LIMIT 3
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':piattaforma', '%' . $nome_piattaforma . '%', PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $top_pc = getTop3PerPiattaforma($db, 'pc');
        $top_xbox = getTop3PerPiattaforma($db, 'xbox');
        $top_ps5 = getTop3PerPiattaforma($db, 'ps5');
        $top_switch = getTop3PerPiattaforma($db, 'switch');
    }

} catch (PDOException $e) {
    echo "<p class='error-msg'>Errore di caricamento dati: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$carrello_elementi = 0;

function getGenreClass($generi_str) {
    if (empty($generi_str)) return 'badge-default';

    $primo_genere = strtolower(trim(explode(',', $generi_str)[0]));

    switch ($primo_genere) {
        case 'action rpg': return 'badge-rpg';
        case 'avventura': return 'badge-adventure';
        case 'platform': return 'badge-platform';
        case 'sparatutto / roguelike': return 'badge-shooter';
        case 'hack and slash': return 'badge-action';
        case 'open world': return 'badge-openworld';
        case 'simulatore di guida': return 'badge-racing';
        default: return 'badge-default';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GamePortal - Il Mondo dei Videogiochi</title>
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

            <a href="carrello.php" class="icon-btn" title="Carrello">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if ($carrello_elementi > 0): ?>
                    <span class="cart-badge"><?php echo $carrello_elementi; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>

<main class="container">

<?php 
// 1. CONTROLLO FILTRO: Verifica se l'utente ha selezionato una piattaforma specifica
if ($piattaforma_selezionata != null): 
?>

    <!-- ========================================== -->
    <!-- VISTA 1: CATALOGO FILTRATO                -->
    <!-- ========================================== -->
    <section id="catalogo-filtrato">
        <h2 class="section-title">
            <?php 
            if ($piattaforma_selezionata === 'tutti') {
                echo "Tutti i Videogiochi";
            } else {
                echo "Giochi per " . htmlspecialchars($piattaforma_selezionata);
            }
            ?>
        </h2>

        <div class="featured-grid">
            <?php foreach ($giochi_filtrati as $gioco): ?>
                <div class="game-card">
                    <div class="game-info">
                        <h3 class="game-title"><?php echo htmlspecialchars($gioco['nome']); ?></h3>
                        
                        <?php if (!empty($gioco['generi'])): ?>
                            <span class="badge <?php echo getGenreClass($gioco['generi']); ?>">
                                <?php echo htmlspecialchars($gioco['generi']); ?>
                            </span>
                        <?php endif; ?>

                        <p class="stock-info">Pezzi disponibili: <?php echo $gioco['stock']; ?></p>
                        <p class="price">€ <?php echo $gioco['prezzo']; ?></p>
                        
                        <!-- PULSANTI ACQUISTO CON ICONE DISTINTE -->
                        <form action="carrello.php" method="post" class="buy-actions">
                            <input type="hidden" name="id" value="<?php echo $gioco['id']; ?>">
                            
                            <button type="submit" name="aggiungi" class="btn-format btn-physical" value="fisico" title="Acquista Copia Fisica">
                                <i class="fa-solid fa-box-archive"></i> Fisico
                            </button>
                            
                            <button type="submit" name="aggiungi" class="btn-format btn-digital" value="virtuale" title="Acquista Copia Digitale">
                                <i class="fa-solid fa-download"></i> Digitale
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php 
// 2. VISTA DEFAULT: Home Page standard
else: 
?>    

    <!-- ========================================== -->
    <!-- VISTA 2: HOME PAGE DEFAULT                 -->
    <!-- ========================================== -->

    <!-- SEZIONE GIOCHI IN EVIDENZA -->
    <section id="evidenza">
        <h2 class="section-title">Giochi in Evidenza</h2>
        <div class="featured-grid">
            <?php foreach ($giochi_evidenza as $gioco): ?>
                <div class="game-card">
                    <div class="game-info">
                        <h3 class="game-title"><?php echo htmlspecialchars($gioco['nome']); ?></h3>
                        
                        <?php if (!empty($gioco['generi'])): ?>
                            <span class="badge <?php echo getGenreClass($gioco['generi']); ?>">
                                <?php echo htmlspecialchars($gioco['generi']); ?>
                            </span>
                        <?php endif; ?>

                        <p class="stock-info warning">Solo <?php echo $gioco['stock']; ?> pezzi!</p>
                        <p class="price">€ <?php echo $gioco['prezzo']; ?></p>
                        
                        <!-- PULSANTI ACQUISTO CON ICONE DISTINTE -->
                        <form action="carrello.php" method="post" class="buy-actions">
                            <input type="hidden" name="id" value="<?php echo $gioco['id']; ?>">
                            
                            <button type="submit" name="aggiungi" class="btn-format btn-physical" value="fisico" title="Acquista Copia Fisica">
                                <i class="fa-solid fa-box-archive"></i> Fisico
                            </button>
                            
                            <button type="submit" name="aggiungi" class="btn-format btn-digital" value="virtuale" title="Acquista Copia Digitale">
                                <i class="fa-solid fa-download"></i> Digitale
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- FUNZIONE HELPER: Genera le sezioni Top 3 -->
    <?php
    function renderTop3Section($titolo, $lista_giochi) {
    ?>
    <section class="top3-section">
        <h2 class="section-title"><?php echo $titolo; ?></h2>

        <div class="top-three-grid">
            <?php if (!empty($lista_giochi)): ?>
                <?php foreach ($lista_giochi as $index => $gioco): ?>
                    <div class="game-card">
                        <div class="game-info">
                            <span class="rank-badge">#<?php echo $index + 1; ?> in Classifica</span>

                            <h3 class="game-title"><?php echo htmlspecialchars($gioco['nome']); ?></h3>

                            <?php if (!empty($gioco['generi'])): ?>
                                <span class="badge <?php echo getGenreClass($gioco['generi']); ?>">
                                    <?php echo htmlspecialchars($gioco['generi']); ?>
                                </span>
                            <?php endif; ?>

                            <p class="price">€ <?php echo $gioco['prezzo']; ?></p>

                            <!-- PULSANTI ACQUISTO CON ICONE DISTINTE -->
                            <form action="carrello.php" method="post" class="buy-actions">
                                <input type="hidden" name="id" value="<?php echo $gioco['id']; ?>">
                                
                                <button type="submit" name="aggiungi" class="btn-format btn-physical" value="fisico" title="Acquista Copia Fisica">
                                    <i class="fa-solid fa-box-archive"></i> Fisico
                                </button>
                                
                                <button type="submit" name="aggiungi" class="btn-format btn-digital" value="virtuale" title="Acquista Copia Digitale">
                                    <i class="fa-solid fa-download"></i> Digitale
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nessun gioco in classifica disponibile.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php
    }

    renderTop3Section(" Top 3 PC", $top_pc);
    renderTop3Section(" Top 3 Xbox", $top_xbox);
    renderTop3Section(" Top 3 PlayStation 5", $top_ps5);
    renderTop3Section(" Top 3 Nintendo Switch", $top_switch);
    ?>

<?php endif; ?>

</main>

</body>
</html>