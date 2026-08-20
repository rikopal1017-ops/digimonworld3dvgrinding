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

// Impor Tabel 1
importCsvToSqlite(
    $db, 
    'digimondvtier', 
    "{$lokasifoldercsv}digimondvtier.csv", 
    array('name', 'tier', 'dv10'),
    "CREATE TABLE digimondvtier (name TEXT, tier INTEGER, dv10 INTEGER)"
);
// Impor Tabel 2
importCsvToSqlite(
    $db, 
    'rookielevelexp', 
    "{$lokasifoldercsv}rookielevelexp.csv", 
    array('name', 'level', 'exp'),
    "CREATE TABLE rookielevelexp (name TEXT, level INTEGER, exp INTEGER)"
);
// Impor Tabel 3
importCsvToSqlite(
    $db, 
    'expdvpoin', 
    "{$lokasifoldercsv}expdvpoin.csv", 
    array('num', 'digimon', 'dv10', 'dv9', 'dv8', 'dv7', 'dv6', 'dv5', 'dv4', 'dv3', 'dv2', 'dv1', 'exp'),
    "CREATE TABLE expdvpoin (
        num INTEGER,
        digimon TEXT,
        dv10 INTEGER,
        dv9 INTEGER,
        dv8 INTEGER,
        dv7 INTEGER,
        dv6 INTEGER,
        dv5 INTEGER,
        dv4 INTEGER,
        dv3 INTEGER,
        dv2 INTEGER,
        dv1 INTEGER,
        exp INTEGER
    )"
);
?>

<div class="calc-container">
    <div class="calc-header">
        <h2 class="calc-title">
            <i class="fa-solid fa-calculator"></i> DV. Point Support Calculator
        </h2>
        <p class="calc-desc">
            Hitung kebutuhan grinding kamu. Pilih Digimon awal, masukkan status saat ini, lalu pilih target untuk rekomendasi musuh terbaik.
        </p>
    </div>

    <div class="form-grid">
        
        <!-- Bagian 1: Data Rookie Base -->
        <div class="section-divider">
            <i class="fa-solid fa-dragon"></i> 1. Status Digimon Utama (Rookie)
        </div>

        <div class="form-group">
            <label for="rookiename">
                <i class="fa-solid fa-paw"></i> Pilih Rookie:
            </label>
<?php
$ooocchage="getRequirementData('dataoutputproses', 'pages/dvcalculator_process.php', 'rookiename=' + document.getElementsByName('rookiename')[0].value + '&rookielevel=' + document.getElementsByName('rookielevel')[0].value + '&rookieexp=' + document.getElementsByName('rookieexp')[0].value + '&digivolvetier=' + document.getElementsByName('digivolvetier')[0].value + '&digivolvetierlevel=' + document.getElementsByName('digivolvetierlevel')[0].value + '&dvpoinlimit=' + document.getElementsByName('dvpoinlimit')[0].value + '&tipesusunan=' + document.getElementsByName('tipesusunan')[0].value)";
?>
            <select id="rookiename" name="rookiename" class="form-control" onchange="<?php echo $ooocchage; ?>">
                <option value="">-- Pilih Digimon --</option>
                <?php
                $sql = "SELECT a.name FROM rookielevelexp a GROUP BY a.name ORDER BY a.name";
                $result = $db->query($sql);
                foreach ($result as $row) {
                    $name = htmlspecialchars($row['name']);
                    echo "<option value='{$name}'>{$name}</option>";
                }
                ?>
            </select>
            <span class="field-hint">Pilih jenis Digimon Rookie yang kamu pakai.</span>
        </div>

        <div class="form-group">
            <label for="rookielevel">
                <i class="fa-solid fa-arrow-up-1-9"></i> Input Rookie Base Level:
            </label>
            <input type="text" id="rookielevel" name="rookielevel" value="" class="form-control" placeholder="Contoh: 15" oninput="<?php echo $ooocchage; ?>" />
            <span class="field-hint">Level dasar Digimon saat ini.</span>
        </div>

        <div class="form-group">
            <label for="rookieexp">
                <i class="fa-solid fa-star"></i> Input Rookie Exp (opsional):
            </label>
            <input type="text" id="rookieexp" name="rookieexp" value="" class="form-control" placeholder="Contoh: 1200" oninput="<?php echo $ooocchage; ?>" />
            <span class="field-hint">EXP yang sudah terkumpul (boleh dikosongkan).</span>
        </div>

        <!-- Bagian 2: Target Evolusi -->
        <div class="section-divider">
            <i class="fa-solid fa-dna"></i> 2. Target Digivolve & Poin
        </div>

        <div class="form-group">
            <label for="digivolvetier">
                <i class="fa-solid fa-layer-group"></i> Pilih Digivolve Tier:
            </label>
            <select id="digivolvetier" name="digivolvetier" class="form-control" onchange="<?php echo $ooocchage; ?>">
                <option value="">-- Pilih Digivolve Tier --</option>
                <?php
                $sql = "SELECT a.* FROM digimondvtier a ORDER BY a.name";
                $result = $db->query($sql);
                foreach ($result as $row) {
                    $name = htmlspecialchars($row['name']);
                    echo "<option value=\"{$row["name"]}\">{$row["name"]} (Tier {$row["tier"]}, Lvl 1 - {$row["dv10"]})</option>";
                }
                ?>
            </select>
            <span class="field-hint">Bentuk evolusi yang ingin ditingkatkan.</span>
        </div>

        <div class="form-group">
            <label for="digivolvetierlevel">
                <i class="fa-solid fa-chart-line"></i> Input Digivolve Tier Level:
            </label>
            <input type="text" id="digivolvetierlevel" name="digivolvetierlevel" value="" class="form-control" placeholder="Contoh: 5" oninput="<?php echo $ooocchage; ?>" />
            <span class="field-hint">Level dari Digivolve Tier saat ini.</span>
        </div>

        <div class="form-group">
            <label for="dvpoinlimit">
                <i class="fa-solid fa-filter"></i> Pilih DV.Point Limit:
            </label>
            <select id="dvpoinlimit" name="dvpoinlimit" class="form-control" onchange="<?php echo $ooocchage; ?>">
                <?php
                for($i=10; $i>=1; $i--) {
                    echo "<option value=\"{$i}\">{$i}</option>";
                }
                ?>
            </select>
            <span class="field-hint">Batas minimal perolehan poin per pertarungan yang ingin dimunculkan.</span>
        </div>

        <div class="section-divider">
            <i class="fa-solid fa-arrow-down-short-wide"></i> 3. Metode Pengurutan Hasil
        </div>
        <!-- Bagian Modul Baru: Tipe Susunan/Urutan -->
        <div class="form-group">
            <label for="tipesusunan">
                <i class="fa-solid fa-arrow-down-wide-short"></i> Urutkan Berdasarkan:
            </label>
            <select id="tipesusunan" name="tipesusunan" class="form-control" onchange="<?php echo $ooocchage; ?>">
                <option value="dv_point_tertinggi">DV Point Tertinggi</option>
                <option value="exp_tertinggi">EXP Tertinggi</option>
            </select>
            <span class="field-hint">Pilih prioritas pengurutan hasil rekomendasi musuh.</span>
        </div>

    </div>

    <!-- Container Hasil AJAX -->
    <div id="dataoutputproses"></div>
</div>