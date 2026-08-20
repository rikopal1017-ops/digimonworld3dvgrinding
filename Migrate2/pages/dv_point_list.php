<?php
// pages/dv_point_list.php

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
    <h2 style="color: #38bdf8; margin-bottom: 15px; border-bottom: 2px solid #334155; padding-bottom: 8px;">
        <i class="fa-solid fa-list-ol"></i> DV Point Table & Information
    </h2>

	<?php
	/*
    <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 20px;">
        Jumlah DV Point yang diperoleh per kenaikan level dan konsumsi poin evolusi:
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="background: #0f172a; border: 1px solid #334155; padding: 15px; border-radius: 8px;">
            <h4 style="color: #faae1c; margin-bottom: 8px;">Champion Level</h4>
            <p style="color: #cbd5e1; font-size: 0.9rem;">Membuka skill & stat awal. Perlu ~5 skill point per skill.</p>
        </div>
        <div style="background: #0f172a; border: 1px solid #334155; padding: 15px; border-radius: 8px;">
            <h4 style="color: #faae1c; margin-bottom: 8px;">Ultimate Level</h4>
            <p style="color: #cbd5e1; font-size: 0.9rem;">Meningkatkan statistik serangan secara drastis saat Lv.20+.</p>
        </div>
        <div style="background: #0f172a; border: 1px solid #334155; padding: 15px; border-radius: 8px;">
            <h4 style="color: #faae1c; margin-bottom: 8px;">Mega Level</h4>
            <p style="color: #cbd5e1; font-size: 0.9rem;">Tingkat evolusi tertinggi dengan konsumsi DV point maksimal.</p>
        </div>
    </div>
	*/
	?>
    <div style="background-color: rgba(30, 41, 59, 0.7); border-left: 4px solid #38bdf8; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px; font-size: 0.9rem; color: #cbd5e1; line-height: 1.5;">
        📌 <strong>Sumber Informasi:</strong><br />
        Data tabel DV Point ini dirangkum berdasarkan referensi diskusi dari komunitas <a href="https://gamefaqs.gamespot.com/ps/562323-digimon-world-3/faqs/78509" target="_blank" style="color: #38bdf8; text-decoration: underline;">GameFAQs - Digimon World 3</a>.
    </div>

<div style="line-height: 1.6; margin-bottom: 20px;">
    <h3 style="color: #38bdf8; margin-bottom: 12px; font-size: 1.1rem;">
        📌 Panduan Membaca Tabel DV EXP &amp; EXP Point
    </h3>
    <p style="text-align: justify; margin-bottom: 15px;">
        Tabel ini digunakan untuk melihat perolehan <strong>DV EXP</strong> dan <strong>EXP biasa</strong> yang didapatkan setelah memenangkan pertarungan. Berikut cara membacanya:
    </p>

    <ul style="margin-left: 20px; margin-bottom: 15px;">
        <li style="margin-bottom: 8px;">
            <strong>Kolom Paling Kiri (Digimon):</strong> Menampilkan nama musuh Digimon yang kamu lawan.
        </li>
        <li style="margin-bottom: 8px;">
            <strong>Baris Header (Angka DV Point 10 – 1):</strong> Menunjukkan jumlah nilai DV EXP yang bisa kamu peroleh dari pertarungan tersebut.
        </li>
        <li style="margin-bottom: 8px;">
            <strong>Isi Kolom (Batas Level Rookie):</strong> Angka di dalam sel menunjukkan <strong>batas level MAKSIMUM</strong> bagi Digimon Rookie kamu untuk mendapatkan poin di kolom tersebut.
            <br />
            <span style="font-size: 0.9em; color: #94a3b8;">
                <em>*Catatan Penalti:* Jika level Rookie kamu melebihi batas maksimum tersebut, perolehan poin akan berkurang dan berpindah ke kolom sebelah kanannya, hingga levelnya melampaui batas berikutnya.</em>
            </span>
        </li>
        <li style="margin-bottom: 8px;">
            <strong>Kolom Paling Kanan (EXP Point):</strong> Menunjukkan jumlah EXP biasa yang diperoleh untuk menaikkan level Rookie.
        </li>
    </ul>

    <div style="background-color: rgba(56, 189, 248, 0.1); border-left: 4px solid #38bdf8; padding: 12px 15px; border-radius: 4px; font-size: 0.88rem; color: #cbd5e1;">
        ℹ️ <em>Data pada tabel ini masih dalam proses melengkapi khususnya pada bagian 'EXP Point', mohon maklum jika terdapat beberapa informasi yang belum sepenuhnya terisi.</em>
    </div>
</div>




	<div class="dv-table-container">
		<table class="dv-table">
			<thead>
				<tr>
					<th rowspan="2">Digimon</th>
					<th colspan="10">DV Point</th>
					<th rowspan="2">EXP Point</th>
				</tr>
				<tr>
					<?php for ($a = 10; $a >= 1; $a--): ?>
						<th><?= $a; ?></th>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$sql_sia = "SELECT a.* FROM expdvpoin a ORDER BY a.num";
				$result = $db->query($sql_sia);

				foreach ($result as $sia):
				?>
					<tr>
						<td><?= htmlspecialchars($sia["digimon"]); ?></td>
						<?php for ($a = 10; $a >= 1; $a--): ?>
							<?php $angkadisplaydv = ($sia["dv" . $a] == 0) ? '-' : $sia["dv" . $a]; ?>
							<td><?= $angkadisplaydv; ?></td>
						<?php endfor; ?>
						<?php $angkadisplayexp = ($sia["exp"] == 0) ? '-' : $sia["exp"]; ?>
						<td><?= $angkadisplayexp; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>


	<p style="text-align: justify; line-height: 1.6;">
		<strong>Studi Kasus:</strong> Karakter Rookie berada pada <strong>Level 31</strong> dan melakukan pertarungan sebanyak <strong>20 kali</strong> melawan tiga jenis musuh berikut:
	</p>

	<ol style="margin-left: 20px; line-height: 1.8;">
		<li>
			<strong>Musyamon</strong>
			<br />
			<strong>DV Point per Battle:</strong> 10 Poin (karena level 31 masih masuk batas maksimum untuk mendapat 10 poin penuh).
			<br />
			<strong>EXP per Battle:</strong> 180 EXP.
			<br />
			<em>Total (20x Battle):</em> 200 DV Point &amp; 3.600 EXP.
		</li>
		<li style="margin-top: 10px;">
			<strong>Numemon (Blue)</strong>
			<br />
			<strong>DV Point per Battle:</strong> 10 Poin (level 31 masih masuk batas maksimum 10 poin).
			<br />
			<strong>EXP per Battle:</strong> 222 EXP.
			<br />
			<em>Total (20x Battle):</em> 200 DV Point &amp; 4.440 EXP.
		</li>
		<li style="margin-top: 10px;">
			<strong>Gesomon</strong>
			<br />
			<strong>DV Point per Battle:</strong> 8 Poin (mengalami penalti/pengurangan poin karena level Rookie sudah menyentuh level 31. Untuk mendapat 10 poin penuh dari Gesomon, Rookie maksimal harus level 27).
			<br />
			<strong>EXP per Battle:</strong> 136 EXP.
			<br />
			<em>Total (20x Battle):</em> 160 DV Point &amp; 2.720 EXP.
		</li>
	</ol>

	<p style="text-align: justify; margin-top: 20px; line-height: 1.6;">
		<strong>Perhitungan Kenaikan DV Level (Berdasarkan Tier Digimon)</strong>
	</p>

	<p style="text-align: justify; line-height: 1.6;">
		Perolehan DV Level dari akumulasi DV Point sangat dipengaruhi oleh <strong>Tier Digimon</strong>. 
		Sebagai contoh, jika Rookie (Level 31) menggunakan Digivolution <strong>SkullGreymon (Tier 2)</strong>, aturan konversinya adalah:
	</p>

	<ul style="margin-left: 20px; line-height: 1.8;">
		<li><strong>DV Level 01 – 95:</strong> Membutuhkan <strong>10 DVEXP</strong> / Level.</li>
		<li><strong>DV Level 95 – 99:</strong> Membutuhkan <strong>50 DVEXP</strong> / Level (kebutuhan poin naik 5x lipat).</li>
	</ul>

	<p style="text-align: justify; margin-top: 15px; line-height: 1.6;">
		Maka, simulasi kenaikan level Digivolution setelah <strong>20x pertarungan</strong> adalah sebagai berikut:
	</p>

	<ol style="margin-left: 20px; line-height: 1.8;">
		<li>
			<strong>Melawan Musyamon (200 DV Point &amp; 3.600 EXP)</strong>
			<br />
			• Jika DV Level masih <strong>&lt; 95</strong> (200 / 10) = <strong>Naik 20 Level</strong>.
			<br />
			• Jika DV Level sudah <strong>&ge; 95</strong> (200 / 50) = <strong>Naik 4 Level</strong>.
		</li>
		<li style="margin-top: 10px;">
			<strong>Melawan Numemon (Blue) (200 DV Point &amp; 4.440 EXP)</strong>
			<br />
			• Jika DV Level masih <strong>&lt; 95</strong> (200 / 10) = <strong>Naik 20 Level</strong>.
			<br />
			• Jika DV Level sudah <strong>&ge; 95</strong> (200 / 50) = <strong>Naik 4 Level</strong>.
		</li>
		<li style="margin-top: 10px;">
			<strong>Melawan Gesomon (160 DV Point &amp; 2.720 EXP)</strong>
			<br />
			• Jika DV Level masih <strong>&lt; 95</strong> (160 / 10) = <strong>Naik 16 Level</strong>.
			<br />
			• Jika DV Level sudah <strong>&ge; 95</strong> (160 / 50) = <strong>Naik 3 Level</strong> (sisa 10 DV Point).
		</li>
	</ol>

	<div style="background-color: #1e293b; border-left: 4px solid #38bdf8; padding: 15px; margin-top: 25px; border-radius: 6px;">
		<p style="text-align: justify; margin: 0; color: #f8fafc; font-size: 0.95rem; line-height: 1.6;">
			💡 <strong>Kesimpulan &amp; Tips Grinding:</strong><br />
			Jika target utama kamu adalah mengoleksi seluruh evolusi Digimon yang membutuhkan syarat DV Level, strategi terbaik adalah mencari musuh dengan <strong>rasio DV Point tinggi namun memberikan EXP se-minimum mungkin</strong>.
			<br /><br />
			Tujuannya adalah agar DV Level bisa bertambah cepat tanpa memicu penambahan Level Rookie yang berlebihan. Jika Level Rookie naik terlalu tinggi, jumlah perolehan DV Point per pertarungan akan berkurang (seperti pada contoh kasus Gesomon & numemon). Namun, semua kembali ke gaya bermain masing-masing: ingin cepat naik level Rookie atau fokus efisiensi grinding DV Level!
		</p>
	</div>

</div>