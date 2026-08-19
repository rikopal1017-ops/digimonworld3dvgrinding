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


?>
<div style="padding-left:20px;padding-right:20px;width:100%;">
<h1>Digimon Digivolution Requirement</h1>
<p style='text-align:justify;'>Menu ini akan memudahkan anda untuk mendapatkan informasi evolusi serta syarat yang diperlukan. Silakan Masukan Input pada menu di bawah ini :</p>

<?php
echo "<p style=\"text-align:left; margin:0; padding:0; \">Pilih Rookie</p><p class=\"parag\"><select class=\"sstext\" name=\"rookiename\">";
$sql = "SELECT a.name FROM rookielevelexp a GROUP BY a.name ORDER BY a.name";
$result = $db->query($sql);
foreach ($result as $row) {
    $name = htmlspecialchars($row['name']);
    echo "<option value='{$name}'>{$name}</option>";
}
echo "</select></p>";

echo "<p class=\"parag\"><button class='btn' style='width:100%;' onclick=\"javascript: loadelementdata('rookiedes', 'digimonlevelrequiremenproses.php', 'rookiename=' + document.getElementsByName('rookiename')[0].value);\">Klik disini untuk Proses Input</button></p>";

?>
<span id='rookiedes'></span>


</div>

<script type="text/javascript">
alert('Halo');
</script>

<?php
ob_end_flush();
?>