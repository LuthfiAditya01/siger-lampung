<?php
include 'koneksi.php';

// Inisialisasi variabel hasil
$hasil = null;
$pesan = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Jika tombol Reset ditekan
  if (isset($_POST['reset'])) {
    // Kosongkan hasil pencarian dan pesan
    $hasil = null;
    $pesan = '';
    $_POST['kk'] = '';
    $_POST['nik'] = '';
  } else {
    $kk = $_POST['kk'] ?? '';
    $nik = $_POST['nik'] ?? '';

    if (strlen($kk) !== 16 || strlen($nik) !== 16) {
      $pesan = "Nomor KK dan NIK harus 16 digit.";
    } else {
      $query = "SELECT nomor_kartu_keluarga, nomor_induk_kependudukan, desil_nasional 
                      FROM dtsen 
                      WHERE nomor_kartu_keluarga = ? AND nomor_induk_kependudukan = ? 
                      LIMIT 1";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $kk, $nik);
      $stmt->execute();
      $result = $stmt->get_result();
      $hasil = $result->fetch_assoc();

      if (!$hasil) {
        $pesan = "Data Tidak Ditemukan";
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGER LAMPUNG - Platform Data Statistik Provinsi Lampung</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="css/output.css" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <!-- Add Heroicons via CDN -->
  <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">
        <h1>SIGER LAMPUNG</h1>
      </div>

      <!-- Desktop Navigation -->
      <div class="nav-menu">
        <button class="nav-link" onclick="scrollToSection('hero')">
          Beranda
        </button>
        <button class="nav-link" onclick="scrollToSection('about')">
          Tentang
        </button>
        <button class="nav-link" onclick="scrollToSection('dashboard')">
          Dashboard
        </button>
        <button class="nav-link" onclick="scrollToSection('tabulasi')">
          Pencarian
        </button>
        <button class="nav-link" onclick="scrollToSection('pengaduan')">
          Pengaduan
        </button>
        <button class="nav-link" onclick="scrollToSection('contact')">
          Kontak
        </button>
      </div>

      <!-- Mobile menu button -->
      <div class="mobile-menu-btn">
        <button onclick="toggleMobileMenu()" aria-label="Toggle menu">
          <i class="fas fa-bars" id="menu-icon"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="mobile-menu" id="mobile-menu">
      <button class="mobile-nav-link" onclick="scrollToSection('hero')">
        Beranda
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('about')">
        DTSEN
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('dashboard')">
        Dashboard
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('data')">
        Data
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('pengaduan')">
        Pengaduan
      </button>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="hero" class="hero-section">
    <div class="container">
      <div class="hero-grid">
        <div class="hero-content">
          <h1 class="hero-title">Selamat Datang</h1>
          <p class="hero-description">SIGER LAMPUNG (Sinergi Gerak Bersama Untuk Layanan Akurat Menuju Performas Unggu)
            adalah platform terintegrasi yang menyediakan data dan informasi statistik terkini untuk mendukung
            pengambilan keputusan yang akurat dan berbasis data di Provinsi Lampung.</p>
        </div>
        <div class="hero-image">
          <div class="hero-image-container">
            <div class="hero-logo">
              <img src="img/logo.png" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="about-section">
    <div class="container">
      <h2 class="section-title">TENTANG</h2>

      <div class="about-grid">
        <div class="about-card-1">
          <div class="card-content">
            <h3 class="card-title">DTSEN</h3>
            <ul class="card-list">
              <li>DTSEN adalah basis data terpadu yang menggabungkan DTKS, Regsosek, dan P3KE untuk menghadirkan data
                sosial ekonomi yang lebih akurat dan tepat sasaran. </li>
              <li>Mendukung program pemerintah dalam pengentasan kemiskinan melalui data yang valid dan terkini</li>
            </ul>
          </div>
        </div>

        <div class="about-card-1">
          <div class="card-content">
            <h3 class="card-title">SIGER LAMPUNG</h3>
            <ul class="card-list">
              <li>SIGER LAMPUNG (Sinergi Gerak Bersama untuk Layanan Akurat Menuju Performa Unggul) adalah inisiatif BPS
                Kota Bandar Lampung untuk mendorong pembinaan statistik sektoral yang terstruktur dan berkelanjutan.
                Program ini melibatkan OPD agar bersama-sama menghasilkan statistik sektoral berkualitas sesuai Sistem
                Statistik Nasional (SSN), demi layanan yang akurat dan profesional.
              </li>
              <li> Melalui SIGER LAMPUNG, BPS memperkuat kolaborasi lintas instansi guna mengatasi tantangan pembinaan
                statistik sektoral dan meningkatkan kualitas pelayanan, agar sejalan dengan standar nasional dan
                mendorong kinerja unggul.
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="partner-logos">
        <div class="logo-container">
          <img src="img/logo-kemensos.png" alt="" />
        </div>
        <div class="logo-container">
          <img src="img/logo-bps.png" alt="" />
        </div>
        <div class="logo-container">
          <img src="img/logo_tnp2k.png" alt="" />
        </div>
      </div>
    </div>
  </section>

  <!-- Dashboard Section -->
  <section id="dashboard" class="dashboard-section">
    <div class="container">

      <!-- Dashboard Tableau - Full Width -->
      <div class="dashboard-tableau">
        <div class='tableauPlaceholder' id='viz1753325516855' style='width: 100%; height: 100%; position: relative;'>
          <noscript>
            <a href='#'>
              <img alt=' '
                src='https://public.tableau.com/static/images/Da/DashboardSIGERLAMPUNG-BPS1871/Wilayah_1/1_rss.png'
                style='border: none' />
            </a>
          </noscript>
          <object class='tableauViz' style='width: 100%; height: 100%;'>
            <param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' />
            <param name='embed_code_version' value='3' />
            <param name='site_root' value='' />
            <param name='name' value='DashboardSIGERLAMPUNG-BPS1871&#47;Wilayah_1' />
            <param name='tabs' value='yes' />
            <param name='toolbar' value='yes' />
            <param name='static_image'
              value='https://public.tableau.com/static/images/Da/DashboardSIGERLAMPUNG-BPS1871/Wilayah_1/1.png' />
            <param name='animate_transition' value='yes' />
            <param name='display_static_image' value='yes' />
            <param name='display_spinner' value='yes' />
            <param name='display_overlay' value='yes' />
            <param name='display_count' value='yes' />
            <param name='language' value='en-US' />
          </object>
        </div>
      </div>

      <script type='text/javascript'>
        var divElement = document.getElementById('viz1753325516855');
        var vizElement = divElement.getElementsByTagName('object')[0];
        vizElement.style.width = '100%';
        vizElement.style.height = '100%';
        var scriptElement = document.createElement('script');
        scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
        vizElement.parentNode.insertBefore(scriptElement, vizElement);
      </script>

    </div>
  </section>

  <!-- Pencarian Section -->
  <section id="tabulasi" class="tabulasi-section">
    <div class="container">
      <h2 class="section-title" style="color: #006a9f">
        PENCARIAN
      </h2>

      <div class="tabulasi-card">
        <div class="dashboard-tableau" style="max-height:500px; background-color: #0093dd;">
          <!-- Pencarian -->
          <div style="width:100%;">
            <form action="" method="post" style="width:100%;">
              <div>
                <label class="block mb-2 text-sm text-white font-medium text-gray-900">Masukkan Nomor KK</label>
                <input type="number" name="kk"
                  value="<?= isset($_POST['reset']) ? '' : htmlspecialchars($_POST['kk'] ?? '') ?>"
                  class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                  required>
              </div>
              <br>
              <div class="mt-4">
                <label class="block mb-2 text-sm text-white font-medium text-gray-900">Masukkan Nomor NIK Kepala Keluarga</label>
                <input type="number" name="nik"
                  value="<?= isset($_POST['reset']) ? '' : htmlspecialchars($_POST['nik'] ?? '') ?>"
                  class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                  required>
              </div>

              <div class="mt-6">
                <button type="submit" class="custom-btn-f">Cari</button>
                <button type="submit" name="reset" value="1"
                  class="custom-btn-f bg-gray-200 text-gray-700 hover:bg-gray-300">
                  Reset
                </button>
              </div>
            </form>

            <?php if ($hasil): ?>
              <div class="mt-6 text-sm text-gray-800 bg-green-50 p-4 rounded">
                <p><strong>Nomor KK:</strong> <?= htmlspecialchars($hasil['nomor_kartu_keluarga']) ?></p>
                <p><strong>NIK:</strong> <?= htmlspecialchars($hasil['nomor_induk_kependudukan']) ?></p>
                <p><strong>Desil Nasional:</strong> <?= htmlspecialchars($hasil['desil_nasional']) ?></p>
              </div>
            <?php elseif ($pesan): ?>
              <div class="mt-6 text-sm text-red-600 bg-red-50 p-4 rounded">
                <?= $pesan ?>
              </div>
            <?php endif; ?>
          </div>


          <!-- </div> -->
        </div>

      </div>
      <!-- </div> -->
  </section>

  <!-- Pengaduan Section -->
  <section id="pengaduan" class="pengaduan-section">
    <div class="container">
      <div class="pengaduan-header">
        <h2 class="section-title">PENGADUAN</h2>
      </div>

      <div class="about-grid">
        <p class="deskripsipengaduan" style="color: white; text-align: center;" ;>Dapat kami sampaikan bahwa dalam
          menjaga <b>Zona Integritas</b> menuju <b>Wilayah Bebas Korupsi</b> serta <b>Wilayah Birokrasi Bersih dan
            Melayani</b>, BPS Kota Bandar Lampung berkomitmen untuk selalu menjaga integritas. Pelanggaran atas
          ketentuan tersebut, dapat dilaporkan melalui <i>whistle blowing system</i> BPS melalui beberapa cara sebagai
          berikut</p>
        <div class="about-card">
          <div class="card-content">
            <h3 class="card-title">Pengaduan Langsung</h3>
            <p><b>Kantor BPS Kota Bandar Lampung</b> <br />
              Jl. Sutan Syahrir No. 30 Pahoman Bandar Lampung
              35215</p>
          </div>
        </div>

        <div class="about-card">
          <div class="card-content">
            <h3 class="card-title">Pengaduan Tidak Langsung</h3>
            <ul class="card-list">
              <li>bps1871@bps.go.id</li>
              <li>(0721) 255980 / 08528 1871871</li>
              <li><a href="https://s.bps.go.id/PengaduanBPSKotaBandarLampung" target="_blank">Formulir Online</a></li>
            </ul>
          </div>
        </div>
        <div class="about-card">
          <div class="card-content">
            <h3 class="card-title">SP4N LAPOR</h3>
            <p>SP4N LAPOR adalah Sistem Pengelolaan Pengaduan Pelayanan Publik Nasional / Layanan Aspirasi dan Pengaduan
              Online Rakyat yang dibangun oleh Kementerian Pendayagunaan Aparatur Negara dan Reformasi Birokrasi
              (Kementerian PANRB).</p>
            <p>Kunjungi <a href="https://www.lapor.go.id/" target="_blank">lapor.go.id</a>
              untuk melakukan pengaduan melalui SP4N LAPOR!</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact-section">
    <div class="container">
      <div class="contact-header">
        <h2 class="section-title" style="color: white">
          KONTAK
        </h2>
        <p class="section-description" style="color: white">
          Untuk informasi lebih lanjut, Anda dapat menghubungi kontak di bawah ini
        </p>
      </div>

      <div class="contact-grid">
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15887.463331200715!2d105.27799865!3d-5.4373384499999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40dbc995555555%3A0x99d597a84cdf184d!2sThe%20Central%20Bureau%20of%20Statistics%20of%20Bandar%20Lampung!5e0!3m2!1sen!2sid!4v1751444508053!5m2!1sen!2sid"
            width="100%" height="100%" style="border: 0; border-radius: 1rem; display: block" allowfullscreen
            loading="lazy" referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>

        <div class="contact-info">
          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="contact-details">
              <h3>Telepon / WA (SIADIN)</h3>
              <p>(0721) 255980 / 08528 1871871</p>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="contact-details">
              <h3>Email</h3>
              <p>bps1871@bps.go.id</p>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="contact-details">
              <h3>Alamat</h3>
              <p>Badan Pusat Statistik<br />Kota Bandar Lampung <br />Jl. Sutan Syahrir No. 30 Pahoman Bandar Lampung
                35215</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <p class="footer-text">
          HAK CIPTA &copy;
          <span class="footer-brand">SIGER LAMPUNG DTSEN</span>
        </p>
        <p class="footer-copyright">&copy; 2024 Semua hak dilindungi undang-undang</p>
      </div>
    </div>
  </footer>

  <script>
    let isMobileMenuOpen = false;

    function toggleMobileMenu() {
      const mobileMenu = document.getElementById("mobile-menu");
      const menuIcon = document.getElementById("menu-icon");

      isMobileMenuOpen = !isMobileMenuOpen;

      if (isMobileMenuOpen) {
        mobileMenu.classList.add("active");
        menuIcon.className = "fas fa-times";
      } else {
        mobileMenu.classList.remove("active");
        menuIcon.className = "fas fa-bars";
      }
    }

    function scrollToSection(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({
          behavior: "smooth",
        });
      }
      // Close mobile menu if open
      if (isMobileMenuOpen) {
        toggleMobileMenu();
      }
    }

    function showTabulation() {
      const tabulationType = document.getElementById("tabulation-select").value;
      const region = document.getElementById("tabulation-region").value;

      if (tabulationType && region) {
        alert(`Menampilkan tabulasi: ${tabulationType} untuk wilayah: ${region}`);
      } else {
        alert("Silakan pilih jenis tabulasi dan wilayah terlebih dahulu");
      }
    }

    function resetTabulation() {
      document.getElementById("tabulation-select").value = "";
      document.getElementById("tabulation-region").value = "";
    }

    function submitPengaduan(event) {
      event.preventDefault();
      const nama = document.getElementById("nama").value;
      const pengaduan = document.getElementById("pengaduan").value;

      if (!nama || !pengaduan) {
        alert("Mohon isi nama dan isi pengaduan");
        return;
      }

      alert("Pengaduan berhasil dikirim! Terima kasih atas masukan Anda.");

      // Reset form
      document.getElementById("nama").value = "";
      document.getElementById("alamat").value = "";
      document.getElementById("email").value = "";
      document.getElementById("pengaduan").value = "";
    }

    // Add scroll effect to navbar
    window.addEventListener("scroll", function() {
      const navbar = document.querySelector(".navbar");
      if (window.scrollY > 50) {
        navbar.style.background =
          "linear-gradient(135deg, rgba(249, 115, 22, 0.9) 0%, rgba(154, 52, 18, 0.95) 100%)";
        navbar.style.backdropFilter = "blur(10px)";
      } else {
        navbar.style.background =
          "linear-gradient(135deg, rgba(249, 115, 22, 0.95) 0%, rgba(154, 52, 18, 0.98) 100%)";
        navbar.style.backdropFilter = "none";
      }
    });

    // Add animation to chart cards on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver(function(entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
        }
      });
    }, observerOptions);

    document.addEventListener("DOMContentLoaded", function() {
      const chartCards = document.querySelectorAll(".chart-card");
      chartCards.forEach((card) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(card);
      });
    });
  </script>
  <script>
    function validasiForm() {
      const kk = document.getElementById("kk").value;
      const nik = document.getElementById("nik").value;

      if (kk.length !== 16 || nik.length !== 16) {
        alert("Nomor KK dan NIK harus 16 digit.");
        return false;
      }
      return true;
    }
  </script>
  <script>
    // Simpan posisi scroll saat keluar halaman
    window.addEventListener('beforeunload', () => {
      localStorage.setItem('scrollPos', window.scrollY);
    });

    // Saat load, scroll ke posisi sebelumnya
    window.addEventListener('load', () => {
      const scrollPos = localStorage.getItem('scrollPos');
      if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
      }
    });
  </script>

</body>

</html>