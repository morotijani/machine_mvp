<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';

// Temporarily force driver to mysql and then sqlite to compare
function getColumns($pdo, $driver) {
    $tables = [];
    if ($driver === 'mysql') {
        $stmt = $pdo->query("SHOW TABLES");
        $tableList = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tableList as $table) {
            $cols = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            $colNames = array_map(function($c) { return $c['Field']; }, $cols);
            $tables[$table] = $colNames;
        }
    } else {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tableList = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tableList as $table) {
            if ($table === 'sqlite_sequence') continue;
            $cols = $pdo->query("PRAGMA table_info(`$table`)")->fetchAll(PDO::FETCH_ASSOC);
            $colNames = array_map(function($c) { return $c['name']; }, $cols);
            $tables[$table] = $colNames;
        }
    }
    return $tables;
}

try {
    // 1. MySQL
    $dsnMysql = "mysql:host=localhost;dbname=machine_mvp_online;charset=utf8mb4";
    $pdoMysql = new PDO($dsnMysql, 'root', '');
    $mysqlTables = getColumns($pdoMysql, 'mysql');

    // 2. SQLite
    $dbFile = __DIR__ . '/../database.sqlite';
    if (!file_exists($dbFile)) {
        // Run migration to create it if it doesn't exist
        $pdoSqlite = new PDO("sqlite:" . $dbFile);
        require __DIR__ . '/../src/Config/Migration.php';
        \App\Config\Migration::run($pdoSqlite);
    } else {
        $pdoSqlite = new PDO("sqlite:" . $dbFile);
    }
    $sqliteTables = getColumns($pdoSqlite, 'sqlite');

    // Compare
    $allTables = array_unique(array_merge(array_keys($mysqlTables), array_keys($sqliteTables)));
    sort($allTables);

    $differences = [];

    foreach ($allTables as $table) {
        $mysqlHas = isset($mysqlTables[$table]);
        $sqliteHas = isset($sqliteTables[$table]);

        if ($mysqlHas && !$sqliteHas) {
            $differences[] = "Table `$table` exists in MySQL but NOT in SQLite.";
            continue;
        }
        if (!$mysqlHas && $sqliteHas) {
            $differences[] = "Table `$table` exists in SQLite but NOT in MySQL.";
            continue;
        }

        // Both have it, compare columns
        $mysqlCols = $mysqlTables[$table];
        $sqliteCols = $sqliteTables[$table];

        $missingInSqlite = array_diff($mysqlCols, $sqliteCols);
        $missingInMysql = array_diff($sqliteCols, $mysqlCols);

        if (!empty($missingInSqlite)) {
            $differences[] = "Table `$table`: Missing in SQLite -> " . implode(', ', $missingInSqlite);
        }
        if (!empty($missingInMysql)) {
            $differences[] = "Table `$table`: Missing in MySQL -> " . implode(', ', $missingInMysql);
        }
    }

    if (empty($differences)) {
        echo "SUCCESS: MySQL and SQLite schemas are perfectly synced!\n";
    } else {
        echo "MISMATCHES FOUND:\n";
        echo implode("\n", $differences);
        echo "\n";
    }

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
