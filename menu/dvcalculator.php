<?php
ob_start();
session_start();

?>
<div style="padding-left:20px;padding-right:20px;width:100%;">
<h1>DV Level Grinding Support Tool</h1>
<p style='text-align:justify;'>Menu ini akan memudahkan anda untuk melakukan grinding DV Level dimana dengan input yang anda berikan menghasilkan output saran yang efektif dalam prosesnya. Silakan Masukan Input pada menu di bawah ini :</p>

<?php
$lokasifoldercsv = "../datacsv/";
// 1. Buat Database SQLite Sementara di Memory
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Buat Tabel rookielevelexp
$db->exec("CREATE TABLE rookielevelexp (
    name TEXT,
    level INTEGER,
    exp INTEGER
)");
// 3. Import Data dari CSV ke SQLite In-Memory
$namaFile = "{$lokasifoldercsv}rookielevelexp.csv";
if (($handle = fopen($namaFile, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Skip Header
    
    $stmt = $db->prepare("INSERT INTO rookielevelexp (name, level, exp) VALUES (?, ?, ?)");
    while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $stmt->execute($row);
    }
    fclose($handle);
}
// 4. Jalankan Query SQL Kamu
$sql = "SELECT a.name FROM rookielevelexp a GROUP BY a.name ORDER BY a.name";
$result = $db->query($sql);

// 5. Tampilkan Hasil dalam Dropdown / Option

echo "<table style='width:100%;'>";
echo "<tr>";
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";
echo "<p style=\"text-align:left; margin:0; padding:0; \">Pilih Rookie</p><p class=\"parag\"><select class=\"sstext\" name=\"rookiename\">";
foreach ($result as $row) {
    $name = htmlspecialchars($row['name']);
    echo "<option value='{$name}'>{$name}</option>";
}
echo "</select></p>";
echo "</td>";
//
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";

echo "<p style=\"text-align:left; margin:0; padding:0; \">Pilih Digimon Tier</p><p class=\"parag\"><select class=\"sstext\" name=\"digimontierdv\">";

// 2. Buat Tabel rookielevelexp
$db->exec("CREATE TABLE digimondvtier (name TEXT, tier INTEGER, dv10 INTEGER)");
// 3. Import Data dari CSV ke SQLite In-Memory
$namaFile = "{$lokasifoldercsv}digimondvtier.csv";
if (($handle = fopen($namaFile, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Skip Header
    $stmt = $db->prepare("INSERT INTO digimondvtier (name, tier, dv10) VALUES (?, ?, ?)");
    while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $stmt->execute($row);
    }
    fclose($handle);
}
// 4. Jalankan Query SQL Kamu
$sql = "SELECT a.* FROM digimondvtier a ORDER BY a.name";
$result = $db->query($sql);
foreach ($result as $row) {
	echo "<option value=\"{$row["name"]}\">{$row["name"]} (tier  {$row["tier"]}, lvl 1 - {$row["dv10"]})</option>";
}
echo "</select></p>";


echo "</td>";
echo "</tr>";


echo "<tr>";
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";
echo "<p style=\"text-align:left; margin:0; padding:0; \">Rookie Level</p><p class=\"parag\"><input class=\"sstext\" type=\"text\" maxlength='2' style=\"\" name=\"rookielevel\" value=\"\" onkeypress=\"javascript: return numberkey(event);\" placeholder=\"input rookie level\" /></p><p style=\"text-align:left; margin:0; padding:0; \">Rookle Level range dari 5 sampai 99, jika diabaikan, maka otomasis menjadi 5</p>";
echo "</td>";
//
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";
echo "<p style=\"text-align:left; margin:0; padding:0; \">Digimon Tier Level</p><p class=\"parag\"><input class=\"sstext\" type=\"text\" maxlength='2' style=\"\" name=\"digimontierdvlevel\" value=\"\" onkeypress=\"javascript: return numberkey(event);\" placeholder=\"input digimon tier level\" /></p><p style=\"text-align:left; margin:0; padding:0; \">Digimon Tier Level range dari 1 sampai 99, jika diabaikan, maka otomatis menjadi 1</p>";
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";
echo "<p style=\"text-align:left; margin:0; padding:0; \">Rookie EXP (optional)</p><p class=\"parag\"><input class=\"sstext\" type=\"text\" maxlength='6' style=\"\" name=\"rookieexp\" value=\"\" onkeypress=\"javascript: return numberkey(event);\" placeholder=\"input exp rookie (optional)\" /></p>";

echo "</td>";
//
echo "<td style='width:50%; margin:4px; padding: 4px;border: 1px solid blue;'>";
echo "<p style=\"text-align:left; margin:0; padding:0; \">DV.Point limit</p><p class=\"parag\"><select class=\"sstext\" name=\"dvpoinlimit\">";
for($i=10; $i>=1; $i--)
{
	echo "<option value=\"{$i}\">{$i}</option>";
}
echo "</select></p><p style=\"text-align:left; margin:0; padding:0; \">Semakin sedikit angka yg dipilih, semakin banyak hasil saran yang muncul</p>";
echo "</td>";
echo "</tr>";


echo "</table>";

echo "<p class=\"parag\"><button class='btn' style='width:100%;' onclick=\"javascript: loadelementdata('rookiedes', 'dvcalculatorrookielevel.php', 'rookiename=' + document.getElementsByName('rookiename')[0].value + '&rookielevel=' + document.getElementsByName('rookielevel')[0].value + '&rookieexp=' + document.getElementsByName('rookieexp')[0].value + '&digimontierdv=' + document.getElementsByName('digimontierdv')[0].value + '&digimontierdvlevel=' + document.getElementsByName('digimontierdvlevel')[0].value + '&dvpoinlimit=' + document.getElementsByName('dvpoinlimit')[0].value);\">Klik disini untuk Proses Input</button></p>";

?>
<span id='rookiedes'></span>


</div>

<script type="text/javascript">
alert('Halo');
</script>

<?php
ob_end_flush();
?>