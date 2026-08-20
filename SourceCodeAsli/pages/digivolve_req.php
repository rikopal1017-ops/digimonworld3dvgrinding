<?php
// pages/digivolve_req.php

$lokasifoldercsv = "../datacsv/";

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

// Impor Tabel
importCsvToSqlite(
    $db, 
    'digimon_evolution_requirements', 
    "{$lokasifoldercsv}digimon_evolution_requirements.csv", 
    array('Rookie', 'Target', 'Syarat1', 'Syarat2'),
    "CREATE TABLE digimon_evolution_requirements (
        Rookie TEXT,
        Target TEXT,
        Syarat1 TEXT,
        Syarat2 TEXT
    )"
);
?>

<div>
    <h2 style="color: #38bdf8; margin-bottom: 15px; border-bottom: 2px solid #334155; padding-bottom: 8px;">
        <i class="fa-solid fa-dna"></i> Digivolve Requirement Guide
    </h2>
    <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 20px;">
        Syarat khusus untuk membuka cabang evolusi (Digivolution) Digimon:
    </p>

    <div style="margin-bottom: 20px;">
        <label for="rookiename" style="display: block; color: #f8fafc; font-weight: 600; margin-bottom: 8px;">
            Pilih Rookie:
        </label>
        <select id="rookiename" name="rookiename" class="sstext" onchange="getRequirementData('dataoutputproses', 'pages/digivolve_req_process.php', 'rookie=' + this.value)" style="padding: 10px 15px; background-color: #1e293b; color: #f8fafc; border: 1px solid #334155; border-radius: 6px; font-size: 0.95rem; width: 100%; max-width: 300px; cursor: pointer;">
            <option value="">-- Pilih Digimon --</option>
            <?php
            // Catatan: Pastikan tabel/data rookielevelexp diisi jika query mengambil dari sana, atau sesuaikan dengan tabel digimon_evolution_requirements
            $sql = "SELECT DISTINCT Rookie AS name FROM digimon_evolution_requirements ORDER BY Rookie ASC";
            $result = $db->query($sql);
            foreach ($result as $row) {
                $name = htmlspecialchars($row['name']);
                echo "<option value='{$name}'>{$name}</option>";
            }
            ?>
        </select>
    </div>

    <!-- Container Hasil AJAX -->
    <div id="dataoutputproses" style="margin-top: 20px;"></div>
</div>
