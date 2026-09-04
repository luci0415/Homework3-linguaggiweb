<?php
require_once 'connessione.php';

if (!$db = connessione::connetti()) {
    die("Unable to connect to connessione.");
}
//$db->exec("DROP DATABASE " . DBNAME);
try {
    $query = "CREATE TABLE IF NOT EXISTS utenti (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        cognome VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        nickname VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        admin VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;";
    $db->exec($query);

    $pass = password_hash('1234', PASSWORD_DEFAULT);
    $query = "INSERT INTO utenti (nome, cognome, email, nickname, password) VALUES
    ('Lucia', 'Felici', 'lucia@gmail.com', 'Lucy', '$pass'),
    ('Emilio', 'Russo', 'emilio@gmail.com', 'Emi', '$pass'),
    ('Marco', 'Temperini', 'marte@gmail.com', 'Marte', '$pass');";
    $db->exec($query);

    // 2. Tabella Videogiochi
    // L'id identifica la singola versione del gioco per piattaforma.
    // Esempi: 0711719541356-PS5, 0711719541356-XBOX, 0711719541356-PC
    $query = "CREATE TABLE IF NOT EXISTS videogiochi (
        id VARCHAR(20) NOT NULL PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        prezzo FLOAT NOT NULL,
        dataUscita DATE NOT NULL,
        stock INT NOT NULL,
        acquistati INT NOT NULL
    ) ENGINE=InnoDB;";
    $db->exec($query);

    $query = "INSERT IGNORE INTO videogiochi (id, nome, prezzo, dataUscita, stock, acquistati) VALUES
        -- PS5
        ('0711719541356-PS5', 'Demon''s Souls', 79.99, '2020-11-12', 15, 42),
        ('0711719541172-PS5', 'Ratchet & Clank: Rift Apart', 69.99, '2021-06-11', 20, 35),
        ('0711719548485-PS5', 'Returnal', 59.99, '2021-04-30', 10, 18),
        ('0711719552550-PS5', 'God of War Ragnarök', 79.99, '2022-11-09', 30, 85),
        ('0711719541295-PS5', 'Marvel''s Spider-Man 2', 79.99, '2023-10-20', 25, 60),
        ('0711719546733-PS5', 'Horizon Forbidden West', 69.99, '2022-02-18', 18, 50),
        ('0505189323714-PS5', 'Hogwarts Legacy', 74.99, '2023-02-10', 40, 95),
        ('0505189323561-PS5', 'Elden Ring', 69.99, '2022-02-25', 12, 110),
        ('0505189323998-PS5', 'Final Fantasy XVI', 79.99, '2023-06-22', 14, 28),
        ('0711719554417-PS5', 'Gran Turismo 7', 74.99, '2022-03-04', 22, 40),
        ('0711719545001-PS5', 'The Last of Us Part I', 79.99, '2022-09-02', 11, 65),
        ('0711719545002-PS5', 'Death Stranding Director''s Cut', 49.99, '2021-09-24', 8, 30),
        ('0711719545003-PS5', 'Ghost of Tsushima Director''s Cut', 69.99, '2021-08-20', 19, 75),
        ('0711719545004-PS5', 'Stray', 29.99, '2022-07-19', 25, 50),
        ('0711719545005-PS5', 'Astro Bot', 69.99, '2024-09-06', 35, 90),
        ('0711719545006-PS5', 'Silent Hill 2', 69.99, '2024-10-08', 14, 40),
        ('0711719560001-PS5', 'Cyberpunk 2077', 59.99, '2020-12-10', 30, 120),
        ('0711719560002-PS5', 'Baldur''s Gate 3', 59.99, '2023-08-03', 18, 150),
        ('0711719560004-PS5', 'Resident Evil 4 Remake', 59.99, '2023-03-24', 21, 80),
        ('0711719560005-PS5', 'Diablo IV', 69.99, '2023-06-06', 25, 95),
        -- Xbox
        ('0711719550001-XBOX', 'Halo Infinite', 59.99, '2021-12-08', 16, 45),
        ('0711719550002-XBOX', 'Forza Horizon 5', 69.99, '2021-11-09', 28, 88),
        ('0711719550003-XBOX', 'Starfield', 79.99, '2023-09-06', 22, 70),
        ('0711719550004-XBOX', 'Gears 5', 39.99, '2019-09-10', 9, 33),
        ('0711719550005-XBOX', 'Sea of Thieves', 39.99, '2018-03-20', 17, 62),
        ('0711719550006-XBOX', 'Forza Motorsport', 79.99, '2023-10-10', 13, 29),
        ('0711719550007-XBOX', 'Senua''s Saga: Hellblade II', 49.99, '2024-05-21', 15, 38),
        ('0711719550008-XBOX', 'State of Decay 2', 29.99, '2018-05-22', 7, 25),
        ('0505189323714-XBOX', 'Hogwarts Legacy', 74.99, '2023-02-10', 40, 95),
        ('0505189323561-XBOX', 'Elden Ring', 69.99, '2022-02-25', 12, 110),
        ('0711719560001-XBOX', 'Cyberpunk 2077', 59.99, '2020-12-10', 30, 120),
        ('0711719560002-XBOX', 'Baldur''s Gate 3', 59.99, '2023-08-03', 18, 150),
        ('0711719560003-XBOX', 'Alan Wake 2', 59.99, '2023-10-27', 12, 44),
        ('0711719560004-XBOX', 'Resident Evil 4 Remake', 59.99, '2023-03-24', 21, 80),
        ('0711719560005-XBOX', 'Diablo IV', 69.99, '2023-06-06', 25, 95),
        ('0711719560006-XBOX', 'Street Fighter 6', 59.99, '2023-06-02', 10, 36),
        ('0711719560007-XBOX', 'Tekken 8', 69.99, '2024-01-26', 16, 52),
        ('0711719560008-XBOX', 'Dragon''s Dogma 2', 69.99, '2024-03-22', 14, 48),
        ('0711719560009-XBOX', 'Assassin''s Creed Valhalla', 49.99, '2020-11-10', 20, 60),
        ('0711719545004-XBOX', 'Stray', 29.99, '2022-07-19', 25, 50),
        -- PC
        ('0711719570001-PC', 'Half-Life: Alyx', 49.99, '2020-03-23', 5, 22),
        ('0711719570002-PC', 'Counter-Strike 2', 0.00, '2023-09-27', 999, 500),
        ('0711719570003-PC', 'Dota 2', 0.00, '2013-07-09', 999, 450),
        ('0711719570004-PC', 'World of Warcraft', 49.99, '2004-11-23', 50, 300),
        ('0711719570005-PC', 'Civilization VI', 59.99, '2016-10-21', 15, 65),
        ('0711719550001-PC', 'Halo Infinite', 59.99, '2021-12-08', 16, 45),
        ('0711719550002-PC', 'Forza Horizon 5', 69.99, '2021-11-09', 28, 88),
        ('0711719550003-PC', 'Starfield', 79.99, '2023-09-06', 22, 70),
        ('0711719550005-PC', 'Sea of Thieves', 39.99, '2018-03-20', 17, 62),
        ('0711719550007-PC', 'Senua''s Saga: Hellblade II', 49.99, '2024-05-21', 15, 38),
        ('0711719560001-PC', 'Cyberpunk 2077', 59.99, '2020-12-10', 30, 120),
        ('0711719560002-PC', 'Baldur''s Gate 3', 59.99, '2023-08-03', 18, 150),
        ('0711719560003-PC', 'Alan Wake 2', 59.99, '2023-10-27', 12, 44),
        ('0711719560004-PC', 'Resident Evil 4 Remake', 59.99, '2023-03-24', 21, 80),
        ('0711719560005-PC', 'Diablo IV', 69.99, '2023-06-06', 25, 95),
        ('0711719560006-PC', 'Street Fighter 6', 59.99, '2023-06-02', 10, 36),
        ('0711719560007-PC', 'Tekken 8', 69.99, '2024-01-26', 16, 52),
        ('0711719560008-PC', 'Dragon''s Dogma 2', 69.99, '2024-03-22', 14, 48),
        ('0711719541172-PC', 'Ratchet & Clank: Rift Apart', 69.99, '2021-06-11', 20, 35),
        ('0711719548485-PC', 'Returnal', 59.99, '2021-04-30', 10, 18),
        -- Switch
        ('0711719580001-SW', 'The Legend of Zelda: Tears of the Kingdom', 69.99, '2023-05-12', 35, 140),
        ('0711719580002-SW', 'Super Mario Odyssey', 59.99, '2017-10-27', 22, 115),
        ('0711719580003-SW', 'Mario Kart 8 Deluxe', 59.99, '2017-04-28', 40, 200),
        ('0711719580004-SW', 'Super Smash Bros. Ultimate', 59.99, '2018-12-07', 18, 98),
        ('0711719580005-SW', 'Pokémon Legends: Arceus', 59.99, '2022-01-28', 25, 85),
        ('0711719580006-SW', 'Animal Crossing: New Horizons', 59.99, '2020-03-20', 30, 130),
        ('0711719580007-SW', 'Metroid Dread', 59.99, '2021-10-08', 12, 40),
        ('0711719580008-SW', 'Super Mario Bros. Wonder', 59.99, '2023-10-20', 28, 92),
        ('0711719580009-SW', 'Xenoblade Chronicles 3', 59.99, '2022-07-29', 10, 31),
        ('0711719580010-SW', 'Luigi''s Mansion 3', 59.99, '2019-10-31', 15, 55),
        ('0711719580011-SW', 'Pokémon Scarlatto', 59.99, '2022-11-18', 25, 110),
        ('0711719580012-SW', 'Splatoon 3', 59.99, '2022-09-09', 20, 75),
        ('0711719580013-SW', 'Pikmin 4', 59.99, '2023-07-21', 18, 50),
        ('0711719580014-SW', 'Paper Mario: Il Portale Millenario', 59.99, '2024-05-23', 15, 45),
        ('0711719580015-SW', 'Fire Emblem Engage', 59.99, '2023-01-20', 12, 35),
        ('0711719580016-SW', 'The Legend of Zelda: Echoes of Wisdom', 59.99, '2024-09-26', 30, 85),
        ('0711719580019-SW', 'Donkey Kong Country Returns HD', 59.99, '2025-01-16', 22, 15),
        ('0711719580020-SW', 'Pokémon Legends: Z-A', 59.99, '2025-11-20', 35, 8),
        -- Switch 2
        ('0711719580017-SW2', 'Metroid Prime 4: Beyond', 69.99, '2025-06-15', 40, 10),
        ('0711719580018-SW2', 'Mario Kart 9', 69.99, '2025-10-10', 50, 5);";
    $db->exec($query);

    // 3. Tabella Generi
    $query = "CREATE TABLE IF NOT EXISTS generi (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        genere VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB;";
    $db->exec($query);

    $query = "INSERT IGNORE INTO generi (id, genere) VALUES
        (1, 'Action RPG'),
        (2, 'Avventura'),
        (3, 'Platform'),
        (4, 'Sparatutto / Roguelike'),
        (5, 'Hack and Slash'),
        (6, 'Open World'),
        (7, 'Simulatore di guida'),
        (8, 'Strategia / MMORPG');";
    $db->exec($query);

    // 4. Tabella Piattaforme
    $query = "CREATE TABLE IF NOT EXISTS piattaforme (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL UNIQUE
    ) ENGINE=InnoDB;";
    $db->exec($query);

    $query = "INSERT IGNORE INTO piattaforme (id, nome) VALUES
        (1, 'Switch'),
        (2, 'PS5'),
        (3, 'Xbox'),
        (4, 'PC'),
        (5, 'Switch 2');";
    $db->exec($query);

    // 5. Tabella Relazione Giochi-Generi
    $query = "CREATE TABLE IF NOT EXISTS giochi_generi (
        id_gioco VARCHAR(20),
        id_genere INT UNSIGNED,
        PRIMARY KEY(id_gioco, id_genere),
        FOREIGN KEY (id_gioco) REFERENCES videogiochi(id) ON DELETE CASCADE,
        FOREIGN KEY (id_genere) REFERENCES generi(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;";
    $db->exec($query);

    // Ogni versione per piattaforma eredita i generi del gioco originale.
    // L'id base è la parte prima del primo '-'.
    $query = "INSERT IGNORE INTO giochi_generi (id_gioco, id_genere)
        SELECT v.id, g.id_genere
        FROM videogiochi v
        JOIN (
            SELECT '0711719541356' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719541172' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719541172' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719548485' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719552550' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719552550' AS id_base, 5 AS id_genere UNION ALL
            SELECT '0711719541295' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719541295' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719546733' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719546733' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0505189323714' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0505189323714' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0505189323561' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0505189323561' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0505189323998' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719554417' AS id_base, 7 AS id_genere UNION ALL
            SELECT '0711719545001' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719545002' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719545003' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719545004' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719545005' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719545006' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719550001' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719550002' AS id_base, 7 AS id_genere UNION ALL
            SELECT '0711719550003' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719550003' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719550004' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719550005' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719550006' AS id_base, 7 AS id_genere UNION ALL
            SELECT '0711719550007' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719550008' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719560001' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719560001' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719560002' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719560003' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719560004' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719560005' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719560006' AS id_base, 5 AS id_genere UNION ALL
            SELECT '0711719560007' AS id_base, 5 AS id_genere UNION ALL
            SELECT '0711719560008' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719560009' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719570001' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719570002' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719570003' AS id_base, 8 AS id_genere UNION ALL
            SELECT '0711719570004' AS id_base, 8 AS id_genere UNION ALL
            SELECT '0711719570005' AS id_base, 8 AS id_genere UNION ALL
            SELECT '0711719580001' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719580001' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719580002' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719580003' AS id_base, 7 AS id_genere UNION ALL
            SELECT '0711719580004' AS id_base, 5 AS id_genere UNION ALL
            SELECT '0711719580005' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719580006' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719580007' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719580008' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719580009' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719580010' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719580011' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719580011' AS id_base, 6 AS id_genere UNION ALL
            SELECT '0711719580012' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719580013' AS id_base, 8 AS id_genere UNION ALL
            SELECT '0711719580014' AS id_base, 1 AS id_genere UNION ALL
            SELECT '0711719580015' AS id_base, 8 AS id_genere UNION ALL
            SELECT '0711719580016' AS id_base, 2 AS id_genere UNION ALL
            SELECT '0711719580017' AS id_base, 4 AS id_genere UNION ALL
            SELECT '0711719580018' AS id_base, 7 AS id_genere UNION ALL
            SELECT '0711719580019' AS id_base, 3 AS id_genere UNION ALL
            SELECT '0711719580020' AS id_base, 1 AS id_genere
        ) AS g
        ON SUBSTRING_INDEX(v.id, '-', 1) = g.id_base;";
    $db->exec($query);

    // 6. Tabella Relazione Giochi-Piattaforme
    $query = "CREATE TABLE IF NOT EXISTS giochi_piattaforme (
        id_gioco VARCHAR(20),
        id_piattaforma INT UNSIGNED,
        PRIMARY KEY(id_gioco, id_piattaforma),
        FOREIGN KEY (id_gioco) REFERENCES videogiochi(id) ON DELETE CASCADE,
        FOREIGN KEY (id_piattaforma) REFERENCES piattaforme(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;";
    $db->exec($query);

    // La piattaforma viene ricavata dal suffisso presente nell'id del gioco.
    $query = "INSERT IGNORE INTO giochi_piattaforme (id_gioco, id_piattaforma)
        SELECT v.id,
               CASE SUBSTRING_INDEX(v.id, '-', -1)
                   WHEN 'SW' THEN 1
                   WHEN 'PS5' THEN 2
                   WHEN 'XBOX' THEN 3
                   WHEN 'PC' THEN 4
                   WHEN 'SW2' THEN 5
               END
        FROM videogiochi v;";
    $db->exec($query);

} catch (PDOException $e) {
    die("Errore DB: " . $e->getMessage());
}

echo ("Database creato, aggiornato e popolato con successo.");
?>
