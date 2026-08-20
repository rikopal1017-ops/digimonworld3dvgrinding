<?php
// pages/rookie_exp.php
?>
<div>
    <h2 style="color: #38bdf8; margin-bottom: 15px; border-bottom: 2px solid #334155; padding-bottom: 8px;">
        <i class="fa-solid fa-bolt"></i> Rookie EXP Tabel Information
    </h2>

    <div style="background-color: rgba(30, 41, 59, 0.7); border-left: 4px solid #38bdf8; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px; font-size: 0.9rem; color: #cbd5e1; line-height: 1.5;">
        📌 <strong>Sumber Informasi:</strong><br />
        Data tabel kebutuhan EXP Rookie ini dirangkum berdasarkan referensi diskusi dari komunitas <a href="https://gamefaqs.gamespot.com/boards/562323-digimon-world-3/64473556" target="_blank" style="color: #38bdf8; text-decoration: underline;">GameFAQs - Digimon World 3</a>.
    </div>

<?php
$lokasifoldercsv = "../datacsv/";
$csvFile = "{$lokasifoldercsv}rookielevelexp.csv";

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->sqliteCreateFunction('floor', 'floor', 1);

// Fungsi re-usable untuk impor CSV ke SQLite (Menggunakan sintaks array() kompatibel versi lama)
function importCsvToSqlite($db, $tableName, $csvFile, $columns, $createTableSql) {
    $db->exec($createTableSql);
    
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";"); // Skip Header CSV
        
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $stmt = $db->prepare("INSERT INTO {$tableName} VALUES ({$placeholders})");
        
        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $stmt->execute($row);
        }
        fclose($handle);
    }
}

// Proses Impor Data CSV
importCsvToSqlite(
    $db, 
    'rookielevelexp', 
    $csvFile, 
    array('name', 'level', 'exp'),
    "CREATE TABLE rookielevelexp (name TEXT, level INTEGER, exp INTEGER)"
);

// Menyusun Header Tabel Dinamis
$tavleheaddigimon = "";
$queryheaddigimon = "";
$digimoncount = 0;

$sql_sia = "SELECT DISTINCT a.name FROM rookielevelexp a ORDER BY a.name";
$result = $db->query($sql_sia);

foreach ($result as $sia) {
    $digimoncount++;
    $tavleheaddigimon .= "<th>" . htmlspecialchars($sia["name"]) . "</th>";
    $queryheaddigimon .= ", SUM(CASE WHEN a.name = '" . $sia["name"] . "' THEN a.exp ELSE 0 END) AS '" . $sia["name"] . "'";
}

// Menampilkan Struktur Utama Tabel
?>
    <div class="dv-table-container">
        <table class="dv-table">
            <thead>
                <tr>
                    <th>Level</th>
                    <?= $tavleheaddigimon; ?>
                </tr>
            </thead>
            <tbody>
<?php
// Mengambil Data EXP Berdasarkan Level & Menampilkan Baris Tabel
$sql_sia = "SELECT a.level {$queryheaddigimon} FROM rookielevelexp a GROUP BY a.level ORDER BY a.level ASC";
$result = $db->query($sql_sia);

foreach ($result as $sia) {
    echo "<tr>";
    echo "<td>" . $sia["level"] . "</td>";
    for ($a = 1; $a <= $digimoncount; $a++) {
        $expValue = ($sia[$a] == 0) ? '-' : number_format($sia[$a]);
        echo "<td style='text-align:center;'>" . $expValue . "</td>";
    }
    echo "</tr>";
}
?>
            </tbody>
        </table>
    </div>
</div>