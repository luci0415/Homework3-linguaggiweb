<?php
require 'dati-generali.php';

class connessione {
    private static ?PDO $pdo = null;


    public static function connetti(): ?PDO
    {
        if (self::$pdo == null) {   //Se pdo non è già assegnato...
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $attr = "mysql:host=" . HOST . ";dbname:`" . DBNAME . "`;charset=" . CHARSET;
            try {
                self::$pdo = new PDO($attr, USER, PASS, $opts);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }

            //Crea il database
            $query = "CREATE DATABASE IF NOT EXISTS `" . DBNAME . "`";
            self::$pdo->exec($query);

            //Seleziona il database
            $query = "USE `" . DBNAME . "`";
            self::$pdo->exec($query);
        }
        return self::$pdo;
    }
}