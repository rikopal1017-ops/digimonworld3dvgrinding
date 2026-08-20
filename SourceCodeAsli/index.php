<?php
// index.php - Main Page Layout
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digimon World 3 Support Tool - Riko Pal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Link CSS Terpisah -->
    <link rel="stylesheet" href="css/main.style.css">
    <link rel="stylesheet" href="css/inputelement.css">
    <link rel="stylesheet" href="css/dv.table.css">
    <link rel="stylesheet" href="css/digimontreecontainer.css">
</head>
<body>

	<header>
		<div class="header-container">
			<div class="brand">
				<img src="DIYRiko logo.png" alt="Riko Pal Avatar" class="avatar">
				<h1 class="channel-title">Riko Pal's Digimon<br />World 3 Webtool</h1>
			</div>
			
			<!-- Pembungkus Logo Big dengan Teks Posisi Tengah -->
			<div class="header-logo-wrapper">
				<a href="https://www.youtube.com/@rikopal" target="_blank" class="logo-link">
					<img src="Logo Big.png" alt="Riko Pal Logo" class="logo-big-img">
				</a>
			</div>
		</div>
	</header>

    <div class="main-container">
        <!-- Sidebar Menu Sebelah Kiri -->
        <aside class="sidebar">
            <h2 class="sidebar-title"><i class="fa-solid fa-list-check"></i> Menu Navigasi</h2>
            <ul class="nav-list">
                <li>
                    <a class="nav-link active" onclick="loadContent('pages/home.php', this)">
                        <span><i class="fa-solid fa-house"></i> Home</span>
                    </a>
                </li>
                
                <li>
                    <a class="nav-link" onclick="loadContent('pages/digivolve_req.php', this)">
                        <span><i class="fa-solid fa-dna"></i> Digivolve Requirement</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" onclick="loadContent('pages/dvcalculator.php', this)">
                        <span><i class="fa-solid fa-dna"></i> DV.Point Support Calculator</span>
                    </a>
                </li>

                <!-- Menu DV Grinding dengan Submenu -->
                <li class="has-submenu">
                    <div class="submenu-toggle" onclick="toggleSubmenu(this)">
                        <span><i class="fa-solid fa-bolt"></i> Digimon EXP & DV Info</span>
                        <i class="fa-solid fa-chevron-down arrow"></i>
                    </div>
                    <div class="submenu" id="dv-submenu">
                        <a class="submenu-link" onclick="loadContent('pages/rookie_exp.php', this)">
                            <i class="fa-solid fa-angles-right"></i> Rookie EXP
                        </a>
                        <a class="submenu-link" onclick="loadContent('pages/dv_point_list.php', this)">
                            <i class="fa-solid fa-angles-right"></i> DV Point
                        </a>
                    </div>
                </li>

                <li>
                    <a class="nav-link" onclick="loadContent('pages/about_me.php', this)">
                        <span><i class="fa-solid fa-user"></i> About Me</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Area Konten Utama Sebelah Kanan -->
        <main class="content-area" id="main-content">
            <div class="spinner" id="loading-spinner"></div>
            <div id="content-body">
                <!-- File PHP terpisah dipanggil di sini tanpa reload -->
            </div>
        </main>
    </div>

    <footer>
        <div class="footer-links">
            <a href="https://www.youtube.com/@rikopal" target="_blank" class="social-link yt">
                <i class="fa-brands fa-youtube"></i> YouTube
            </a>
            <a href="https://www.tiktok.com/@riko.pal" target="_blank" class="social-link tt">
                <i class="fa-brands fa-tiktok"></i> TikTok
            </a>
            <a href="https://saweria.co/rikopal" target="_blank" class="social-link sw">
                <i class="fa-solid fa-hand-holding-dollar"></i> Saweria
            </a>
        </div>
        <p class="copyright">&copy; 2026 Riko Pal. All rights reserved.</p>
    </footer>

    <script>
        // Buka / Tutup Dropdown Submenu
        function toggleSubmenu(element) {
            element.classList.toggle('open');
            const submenu = document.getElementById('dv-submenu');
            submenu.classList.toggle('show');
        }

        // Fungsi AJAX Fetch untuk Memanggil File PHP Tanpa Refresh
        function loadContent(pageUrl, element) {
            const contentBody = document.getElementById('content-body');
            const spinner = document.getElementById('loading-spinner');

            // Reset status aktif
            document.querySelectorAll('.nav-link, .submenu-link').forEach(link => {
                link.classList.remove('active');
            });

            if (element) {
                element.classList.add('active');
            }

            // Tampilkan animasi loading
            spinner.style.display = 'block';
            contentBody.style.opacity = '0.3';

            // Ambil file PHP secara async
            fetch(pageUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Halaman gagal dimuat (' + response.status + ')');
                    }
                    return response.text();
                })
                .then(html => {
                    contentBody.innerHTML = html;
                })
                .catch(error => {
                    contentBody.innerHTML = `
                        <div style="color: #ef4444; padding: 20px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; margin-bottom: 10px;"></i>
                            <h3>Gagal Memuat Konten</h3>
                            <p>${error.message}</p>
                        </div>
                    `;
                })
                .finally(() => {
                    spinner.style.display = 'none';
                    contentBody.style.opacity = '1';
                });
        }

        // Muat halaman default (Home) saat pertama kali buka
        document.addEventListener('DOMContentLoaded', () => {
            loadContent('pages/home.php', document.querySelector('.nav-link'));
        });





		function getRequirementData(elementid, phpaceess, variabelinput) {
			//alert(variabelinput);
			const outputDiv = document.getElementById(elementid);

			if (variabelinput === "") {
				outputDiv.innerHTML = "";
				return;
			}
			if (phpaceess === "") {
				outputDiv.innerHTML = "";
				return;
			}

			// Tampilkan indikator loading sederhana
			outputDiv.innerHTML = "<p style='color: #94a3b8;'><i class='fa-solid fa-spinner fa-spin'></i> Memuat data...</p>";

			// Mengirim request dengan Fetch (AJAX)
			fetch(phpaceess + '?' + variabelinput)
				.then(response => {
					if (!response.ok) {
						throw new Error('Gagal terhubung ke server');
					}
					return response.text();
				})
				.then(data => {
					outputDiv.innerHTML = data;
				})
				.catch(error => {
					outputDiv.innerHTML = "<p style='color: #ef4444;'>Error: " + error.message + "</p>";
				});
		}
	</script>


</body>
</html>