<?php
// pages/digivolve_req_process.php

if (isset($_GET['rookie'])) {
    $rookiename = trim($_GET['rookie']);
    $lokasifoldercsv = "../datacsv/";

    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->sqliteCreateFunction('floor', 'floor', 1);

    function importCsvToSqlite($db, $tableName, $csvFile, $columns, $createTableSql) {
        $db->exec($createTableSql);
        
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            fgetcsv($handle, 1000, ";");
            
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

    // Fungsi Rekursif Membuat Pohon Cabang Kiri ke Kanan
    function bacadigimonawal($db, $rookie, $namadigimon) {
        $stmt = $db->prepare("SELECT a.* FROM digimon_evolution_requirements a WHERE a.Rookie = :rookie AND (a.Syarat1 LIKE :namadigimon or a.Syarat2 LIKE :namadigimon)");
        $stmt->execute(array(
            ':rookie' => $rookie,
            ':namadigimon' => $namadigimon . '%'
        ));
        $result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result3)) {
            echo "<ul class='tree-branch'>";
            foreach ($result3 as $sia) {
                $target = htmlspecialchars($sia['Target']);
                $syarat1 = htmlspecialchars($sia['Syarat1']);
                $syarat2 = htmlspecialchars($sia['Syarat2']);

                echo "<li class='tree-node'>";
                
                // === KOTAK NODE EVOLUSI ===
                echo "<div class='node-card'>";
                    // Tag Nama Evolusi
					if (preg_match('/LV/i', $syarat2)) {
						$namaOnly = trim(preg_replace('/\s+LV.*/i', '', $syarat2));
					}
                    echo "<div class='target-badge' id='digimon-{$target}'>";
                        echo htmlspecialchars($namadigimon) . " ➔ <strong>{$target}!</strong>";
                    echo "</div>";

                    // List Syarat di dalam Kotak
                    echo "<div class='req-box'>";
                        echo "<ul>";
                            echo "<li><strong>Syarat 1:</strong> ";
							if (preg_match('/LV/i', $syarat1)) {
								$namaOnly = trim(preg_replace('/\s+LV.*/i', '', $syarat1));
								echo "<a href='#digimon-{$namaOnly}'>{$syarat1}</a>";
							}
							else echo "{$syarat1}";
							echo "</li>";

                            if (!empty($syarat2)) {
                                if (preg_match('/LV/i', $syarat2)) {
                                    $namaOnly = trim(preg_replace('/\s+LV.*/i', '', $syarat2));
                                    echo "<li><strong>Syarat 2:</strong> <a href='#digimon-{$namaOnly}'>{$syarat2}</a></li>";
                                } else {
                                    echo "<li><strong>Syarat 2:</strong> {$syarat2}</li>";
                                }
                            }
                        echo "</ul>";
                    echo "</div>"; // end req-box
                echo "</div>"; // end node-card

                // Panggilan Rekursif untuk Membuat Cabang Sebelah Kanan
				bacadigimonawal($db, $rookie, $target);

                echo "</li>";
            }
            echo "</ul>";
        }
    }

    // Container Output Utama
    echo "<div style='background-color: #1e293b; border: 1px solid #334155; padding: 20px; border-radius: 8px; color: #f8fafc;'>";
    echo "<h3 style='margin-bottom: 15px; color: #38bdf8;'><i class='fa-solid fa-sitemap'></i> Pohon Evolusi: " . htmlspecialchars($rookiename) . "</h3>";
    
    echo "<div class=\"digimon-tree-horizontal\">";
    bacadigimonawal($db, $rookiename, $rookiename);
    echo "</div>";

    echo "</div>";

} else {
    echo "<p style='color: #ef4444;'>Tidak ada data Rookie yang dipilih.</p>";
}
?>