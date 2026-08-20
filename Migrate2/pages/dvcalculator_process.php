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
<div>

<?php
//rookiename=' + document.getElementsByName('rookiename')[0].value + '&rookielevel=' + document.getElementsByName('rookielevel')[0].value + '&rookieexp=' + document.getElementsByName('rookieexp')[0].value + '&digivolvetier=' + document.getElementsByName('digivolvetier')[0].value + '&digivolvetierlevel=' + document.getElementsByName('digivolvetierlevel')[0].value + '&dvpoinlimit=' + document.getElementsByName('dvpoinlimit')[0].value + '&tipesusunan=' + document.getElementsByName('tipesusunan')[0].value


if(isset($_REQUEST["rookiename"]) && strlen($_REQUEST["rookiename"])>0) $rookiename=(string)$_REQUEST["rookiename"];
else $rookiename="Agumon";

if(isset($_REQUEST["rookielevel"])){
	if((int)$_REQUEST["rookielevel"]<5) $rookielevel=5;
	else $rookielevel=(int)$_REQUEST["rookielevel"];
}
else $rookielevel=5;

if(isset($_REQUEST["rookieexp"])) $rookieexp = (double) $_REQUEST["rookieexp"];
else $rookieexp=0;

if(isset($_REQUEST["digivolvetier"]) && strlen($_REQUEST["digivolvetier"])>0) $digimontierdv=(string)$_REQUEST["digivolvetier"];
else $digimontierdv="Greymon";

if(isset($_REQUEST["digivolvetierlevel"])){
	if((int)$_REQUEST["digivolvetierlevel"]<1) $digimontierdvlevel=1;
	else $digimontierdvlevel = (double) $_REQUEST["digivolvetierlevel"];
}
else $digimontierdvlevel=1;

if(isset($_REQUEST["tipesusunan"])) $tipesusunan=(string)$_REQUEST["tipesusunan"];
else $tipesusunan="";

$digimontierdvlevellimit=1;

if(isset($_REQUEST["dvpoinlimit"])) $dvpoinlimit = (double) $_REQUEST["dvpoinlimit"];
else $dvpoinlimit=10;

$digimontierscore=10;
$sql = "select a.* from digimondvtier a where a.name='{$digimontierdv}'";
$resultdv = $db->query($sql);
foreach ($resultdv as $sia) {
	$digimontierdvlevellimit=(int)$sia["dv10"];
	if($digimontierdvlevel > $digimontierdvlevellimit) $digimontierscore=50;
}

$expawal=0;
$explanjut=0;

$sql = "select a.* from rookielevelexp a where a.name='{$rookiename}' and a.level={$rookielevel}";
$result = $db->query($sql);
foreach ($result as $sia) {
	$expawal=(int)$sia["exp"];
	$explanjut=$expawal;
	if($rookielevel==99){
		//nothing
	}
	else
	{
		$rookielevelnext=$rookielevel+1;

		$sql = "select a.* from rookielevelexp a where a.name='{$rookiename}' and a.level={$rookielevelnext}";
		$result2 = $db->query($sql);
		foreach ($result2 as $sia2) {
			$explanjut=(int)$sia2["exp"];

			if($rookieexp > $expawal && $rookieexp < $explanjut){
				$expawal = $rookieexp;
			}
		}
	}

	/* --- PERBAIKAN DESKRIPSI OUTPUT RINGKASAN STATUS --- */
	echo "<p style=\"text-align:left; margin:0; padding:0; font-weight:bold;\">Digimon Utama (Rookie):</p>";
	echo "<p class=\"parag\">{$sia["name"]} — Level {$sia["level"]}</p>";

	echo "<p style=\"text-align:left; margin:0; padding:0; font-weight:bold;\">Status EXP Saat Ini:</p>";
	echo "<p class=\"parag\">{$expawal} EXP";
	$expselisih = $explanjut - $expawal;
	if($rookielevel<99){
		echo " (Membutuhkan <strong>{$expselisih} EXP</strong> lagi untuk naik ke level selanjutnya di <strong>{$explanjut} EXP</strong>)";
	}
	else{
		$expselisih = 1000;
		echo " (Level telah mencapai batas maksimal Lvl 99. Target perkiraan kalkulasi diset sebesar <strong>{$expselisih} EXP</strong> untuk menentukan jumlah tanding)";
	}
	echo "</p>";

	echo "<p style=\"text-align:left; margin:0; padding:0; font-weight:bold;\">Target Evolusi (Digivolve Tier):</p>";
	echo "<p class=\"parag\">{$digimontierdv} — Level {$digimontierdvlevel}";
	if($digimontierdvlevel <= $digimontierdvlevellimit) echo " (Range Level 1–{$digimontierdvlevellimit} | Syarat Level Up: <strong>10 DV Point</strong>)";
	else echo " (Melebihi Limit Lvl {$digimontierdvlevellimit} | Syarat Level Up: <strong>50 DV Point</strong>)";
	echo "</p>";

	$colomDVEarn = "(CASE WHEN {$rookielevel} <= a.dv10 THEN 10
	WHEN {$rookielevel} <= a.dv9  THEN 9
	WHEN {$rookielevel} <= a.dv8  THEN 8
	WHEN {$rookielevel} <= a.dv7  THEN 7
	WHEN {$rookielevel} <= a.dv6  THEN 6
	WHEN {$rookielevel} <= a.dv5  THEN 5
	WHEN {$rookielevel} <= a.dv4  THEN 4
	WHEN {$rookielevel} <= a.dv3  THEN 3
	WHEN {$rookielevel} <= a.dv2  THEN 2
	WHEN {$rookielevel} <= a.dv1  THEN 1
	ELSE 0 
END)";
	$colombattlecountfornextlevel = "(CASE WHEN a.exp>0 THEN floor({$expselisih}/a.exp) ELSE 0 END)";
	$colomdvpointtotal = "(($colombattlecountfornextlevel)*($colomDVEarn))";

	if($tipesusunan=="exp_tertinggi"){
		$susususususun = "a.exp desc";
		$fvgnekrhtr = "EXP Musuh Tertinggi";
	}
	else{
		$susususususun = "({$colomdvpointtotal}/{$digimontierscore}) desc, ({$colombattlecountfornextlevel}*a.exp) asc";
		$fvgnekrhtr = "Total DV.Point Tertinggi";
	}
	
	$sqldigimonsaran = "select {$colomDVEarn} as 'DVPoinearn', {$colombattlecountfornextlevel} as 'battlecount', {$colomdvpointtotal} as 'DVPoinTotal', ({$colomdvpointtotal}/{$digimontierscore}) as 'DVLevelTotalDecimal', floor({$colomdvpointtotal}/{$digimontierscore}) as 'DVLevelTotal', ({$colomdvpointtotal}%{$digimontierscore}) as 'DVPointTotalLeft', ({$colombattlecountfornextlevel}*a.exp) as 'EXPTotal', a.* from expdvpoin a where ({$colomDVEarn}>={$dvpoinlimit}) and a.dv10<={$rookielevel}+5 and a.exp>0 order by {$susususususun}";
	$result3 = $db->query($sqldigimonsaran)->fetchAll(PDO::FETCH_ASSOC);
	if (empty($result3)) {
		echo "<p style='color:#f87171; font-weight:500;'>Tidak ada rekomendasi musuh yang ditemukan. Coba turunkan batas minimal DV Point (saat ini ditiadakan/dibatasi hingga {$dvpoinlimit} DV Point).</p>";
	} else {
		/* --- PERBAIKAN DESKRIPSI CATATAN / RUMUS KALKULASI --- */
		echo "<div class=\"calc-notes\" style=\"margin-top:15px; font-size:0.85rem;\">
		<p style=\"font-weight:bold; margin-bottom:5px;\"><i class=\"fa-solid fa-circle-info\"></i> Panduan Rumus Perhitungan:</p>
		<table style=\"width:100%; border-collapse:collapse;\">
			<tr><td style=\"padding:4px 8px; width:220px; font-weight:600;\">Jumlah Tanding</td><td style=\"padding:4px 8px;\"><code>floor(Sisa EXP ke Level Selanjutnya / EXP Musuh)</code> <br><small style=\"color:#94a3b8;\">*Pembulatan ke bawah (mengabaikan angka di belakang koma).</small></td></tr>
			<tr><td style=\"padding:4px 8px; font-weight:600;\">DV.Point Total</td><td style=\"padding:4px 8px;\"><code>DV.Point per Battle × Jumlah Tanding</code></td></tr>
			<tr><td style=\"padding:4px 8px; font-weight:600;\">DV.Level Total</td><td style=\"padding:4px 8px;\"><code>floor(DV.Point Total / {$digimontierscore})</code> <br><small style=\"color:#94a3b8;\">*Pembagi <strong>{$digimontierscore}</strong> disesuaikan berdasarkan <a href='https://gamefaqs.gamespot.com/boards/562323-digimon-world-3/47657837' target='_blank' style='color:#38bdf8;'>Tingkat Tier Digimon</a> dan Level Tier saat ini.</small></td></tr>
			<tr><td style=\"padding:4px 8px; font-weight:600;\">EXP Total Perolehan</td><td style=\"padding:4px 8px;\"><code>EXP Musuh × Jumlah Tanding</code></td></tr>
			<tr><td colspan='2' style=\"padding:8px; font-style:italic; color:#38bdf8;\">Tabel rekomendasi di bawah ini diurutkan berdasarkan: <strong>{$fvgnekrhtr}</strong>.</td></tr>
		</table>
		</div>";

		/* --- BAGIAN TABEL SAMA (TIDAK DIUBAH SAMA SEKALI) --- */
		echo "<div class=\"dv-table-container\"><table class=\"dv-table\">";
		echo "<thead>";
        echo "<tr>
            <th onclick=\"sortTable('digimondvgrinding', 0, 'text')\">Digimon</th>
            <th onclick=\"sortTable('digimondvgrinding', 1, 'number')\">EXP Point</th>
            <th onclick=\"sortTable('digimondvgrinding', 2, 'number')\">DV Point</th>
            <th onclick=\"sortTable('digimondvgrinding', 3, 'number')\">Jumlah Tanding</th>
            <th onclick=\"sortTable('digimondvgrinding', 4, 'number')\">Dv.Point Total</th>
            <th onclick=\"sortTable('digimondvgrinding', 5, 'number')\">Dv.Level Total</th>
            <th onclick=\"sortTable('digimondvgrinding', 6, 'number')\">Dv.Point Sisa</th>
            <th onclick=\"sortTable('digimondvgrinding', 7, 'number')\">EXP Total</th>
        </tr>";
		echo "</thead><tbody>";
        foreach ($result3 as $sia3) {
			echo "<tr><td>{$sia3["digimon"]}</td>";
			echo "<td style='text-align:center;'>{$sia3["exp"]}</td>";
			echo "<td style='text-align:center;'>" . (int) $sia3["DVPoinearn"] . "</td>";
			echo "<td style='text-align:center;'>" . (int) $sia3["battlecount"] . "</td>";
			echo "<td style='text-align:center;'>" . (int) $sia3["DVPoinTotal"] . "</td>";
			echo "<td style='text-align:center;' title='{$sia3["DVLevelTotalDecimal"]}'>" . (int) $sia3["DVLevelTotal"] . "</td>";
			echo "<td style='text-align:center;'>" . (int) $sia3["DVPointTotalLeft"] . "</td>";
			echo "<td style='text-align:center;'>" . (int) $sia3["EXPTotal"] . "</td>";
			echo "</tr>";
		}
		echo "</tbody></table></div>";
	}
}
ob_end_flush();
?>
</div>