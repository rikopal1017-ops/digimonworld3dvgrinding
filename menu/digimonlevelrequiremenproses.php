<?php
ob_start();
session_start();

$lokasifoldercsv = "../datacsv/";

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Daftarkan fungsi floor PHP ke dalam SQLite
$db->sqliteCreateFunction('floor', 'floor', 1);

// Function re-usable untuk impor CSV ke SQLite
function importCsvToSqlite($db, $tableName, $csvFile, $columns, $createTableSql) {
    $db->exec($createTableSql);
    
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";"); // Skip Header
        
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $stmt = $db->prepare("INSERT INTO {$tableName} VALUES ({$placeholders})");
        
        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $stmt->execute($row);
        }
        fclose($handle);
    }
}

// Impor Tabel 2
importCsvToSqlite(
    $db, 
    'rookielevelexp', 
    "{$lokasifoldercsv}rookielevelexp.csv", 
    ['name', 'level', 'exp'],
    "CREATE TABLE rookielevelexp (name TEXT, level INTEGER, exp INTEGER)"
);
// Impor Tabel 3
importCsvToSqlite(
    $db, 
    'digimon_evolution_requirements', 
    "{$lokasifoldercsv}digimon_evolution_requirements.csv", 
    ['Rookie', 'Target', 'Syarat1', 'Syarat2'],
    "CREATE TABLE digimon_evolution_requirements (
        Rookie TEXT,
        Target TEXT,
        Syarat1 TEXT,
        Syarat2 TEXT
    )"
);
// Jalankan query dari $db yang sama sepuasnya!







if(isset($_REQUEST["rookiename"])) $rookiename=(string)$_REQUEST["rookiename"];
else $rookiename="Agumon";

echo "<p style=\"text-align:left; margin:0; padding:0; \">Rookie</p><p class=\"parag\">{$rookiename}</p>";

$sqldigimonsaran = "select a.* from digimon_evolution_requirements a where a.Rookie='{$rookiename}'";
$result3 = $db->query($sqldigimonsaran)->fetchAll(PDO::FETCH_ASSOC);
if (empty($result3)) {
	echo "Nungguin ya?? maaf ya, datanya belum diisi";
}
else
{
	echo "<h3>List Digivolution</h3>";
	foreach ($result3 as $sia)
    {
        echo "<p class=\"parag\">{$sia["Target"]}</p>";
        echo "<ol><li>{$sia["Syarat1"]}</li>";
        if(strlen($sia["Syarat2"]>0)) echo "<li>{$sia["Syarat2"]}</li>";
        echo "</ol>";
	}
}


ob_end_flush();
?>