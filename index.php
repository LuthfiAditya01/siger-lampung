<?php
include 'koneksi.php';

// Inisialisasi variabel hasil
$hasil = null;
$pesan = '';

$query = "SELECT * FROM news ORDER BY tanggal_berita DESC LIMIT 10";
$result = mysqli_query($conn, $query);
$berita = [];

while ($row = mysqli_fetch_assoc($result)) {
  $berita[] = $row;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGER BANDAR LAMPUNG - Platform Data Statistik Provinsi Lampung</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="css/output.css" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

  <!-- Add Heroicons via CDN -->
  <link href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


  <!-- Chart.js harus ada sebelum script bikin grafik -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

  <style>
    html {
      scroll-behavior: smooth;
    }

    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
  <!-- stylechatbot -->
  <style>
    .chat-container {
      flex: 1;
      overflow-y: auto;
      padding: 15px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message {
      max-width: 70%;
      padding: 10px 15px;
      border-radius: 15px;
      line-height: 1.4;
      word-wrap: break-word;
    }

    .user-message {
      align-self: flex-end;
      background-color: #d1e7ff;
      /* biru muda */
      color: #000;
      border-bottom-right-radius: 5px;
    }

    .bot-message {
      align-self: flex-start;
      background-color: #e9ecef;
      /* abu-abu terang */
      color: #000;
      border-bottom-left-radius: 5px;
    }

    .input-container {
      display: flex;
      border-top: 1px solid #ccc;
      background: #fff;
      padding: 10px;
    }

    input {
      flex: 1;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    button {
      margin-left: 8px;
      padding: 8px 12px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background: #0056b3;
    }

    /* Efek loading */
    .loading {
      display: inline-block;
      font-size: 14px;
      color: #555;
    }

    .loading::after {
      content: '';
      animation: dots 1.5s steps(3, end) infinite;
    }

    @keyframes dots {

      0%,
      20% {
        content: '.';
      }

      40% {
        content: '..';
      }

      60% {
        content: '...';
      }

      80%,
      100% {
        content: '';
      }
    }

    .dot-typing {
      display: inline-block;
      position: relative;
      width: 20px;
      height: 8px;
    }

    .dot-typing::before,
    .dot-typing::after,
    .dot-typing div {
      content: '';
      position: absolute;
      top: 0;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #555;
      animation: dotTyping 1s infinite ease-in-out;
    }

    .dot-typing div {
      left: 7px;
      animation-delay: 0.2s;
    }

    .dot-typing::after {
      left: 14px;
      animation-delay: 0.4s;
    }

    @keyframes dotTyping {

      0%,
      80%,
      100% {
        transform: scale(0.6);
      }

      40% {
        transform: scale(1);
      }
    }
  </style>
  <!-- style berita -->
  <style>
    /* Animasi scroll horizontal */
    @keyframes scrollBerita {
      0% {
        transform: translateX(0);
      }

      100% {
        transform: translateX(-50%);
      }
    }

    .scroll-track {
      animation: scrollBerita 25s linear infinite;
    }

    .scroll-container:hover .scroll-track {
      animation-play-state: paused;
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">
        <h1>SIGER BANDAR LAMPUNG</h1>
      </div>

      <!-- Desktop Navigation -->
      <div class="nav-menu">
        <button class="nav-link" onclick="scrollToSection('hero')">
          Beranda
        </button>
        <!-- <button class="nav-link" onclick="scrollToSection('berita')">
          Berita
        </button> -->
        <button class="nav-link" onclick="scrollToSection('about')">
          Tentang
        </button>
        <button class="nav-link" onclick="scrollToSection('dashboard')">
          Dashboard
        </button>
        <button class="nav-link" onclick="scrollToSection('tabulasi')">
          TanyaDTSEN
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
      <!-- <button class="mobile-nav-link" onclick="scrollToSection('berita')">
        Berita
      </button> -->
      <button class="mobile-nav-link" onclick="scrollToSection('about')">
        Tentang
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('dashboard')">
        Dashboard
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('tabulasi')">
        TanyaDTSEN
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('pengaduan')">
        Pengaduan
      </button>
      <button class="mobile-nav-link" onclick="scrollToSection('contact')">
        Kontak
      </button>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="hero" class="hero-section">
    <div class="container" style="margin-top:2rem">
      <br>
      <div class="hero-grid">
        <div class="hero-content" style="padding-left:50px;">
          <h1 class="hero-title">Tabik Pun!</h1>
          <p class="hero-description">SIGER BANDAR LAMPUNG (Sinergi Gerak Bersama Bandar Lampung)
            adalah platform terintegrasi yang menyediakan data dan informasi megenai DTSEN untuk mendukung
            pengambilan keputusan yang akurat dan berbasis data di Kota Bandar Lampung Lampung.</p>
        </div>
        <div class="hero-image">
          <div class="hero-image-container">
            <div class="hero-logo">
              <img src="img/logo.jpg" alt="" />
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="overflow-x-hidden scroll-container">
        <div class="flex space-x-4 px-4 scroll-track">
          <?php foreach ($berita as $item): ?>
            <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"
              class="min-w-[250px] max-w-[250px] bg-white shadow-md rounded-lg p-4 hover:scale-105 transition-transform duration-300">
              <div class="text-sm text-gray-500 mb-2">
                <?= date("d M Y", strtotime($item['tanggal_berita'])) ?>
              </div>
              <h3 class="text-lg font-semibold text-gray-800 mb-1">
                <?= htmlspecialchars($item['nama']) ?>
              </h3>
              <div class="text-sm text-gray-600 italic">
                Sumber: <?= htmlspecialchars($item['sumber']) ?>
              </div>
            </a>
          <?php endforeach; ?>

          <!-- Duplikat biar scrolling mulus -->
          <?php foreach ($berita as $item): ?>
            <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"
              class="min-w-[250px] max-w-[250px] bg-white shadow-md rounded-lg p-4 hover:scale-105 transition-transform duration-300">
              <div class="text-sm text-gray-500 mb-2">
                <?= date("d M Y", strtotime($item['tanggal_berita'])) ?>
              </div>
              <h3 class="text-lg font-semibold text-gray-800 mb-1">
                <?= htmlspecialchars($item['nama']) ?>
              </h3>
              <div class="text-sm text-gray-600 italic">
                Sumber: <?= htmlspecialchars($item['sumber']) ?>
              </div>
            </a>
          <?php endforeach; ?>
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
            <h3 class="card-title">SIGER BANDAR LAMPUNG</h3>
            <ul class="card-list">
              <li>SIGER BANDAR LAMPUNG (Sinergi Gerak Bersama Bandar Lampung) adalah inisiatif BPS
                Kota Bandar Lampung untuk mendorong pembinaan statistik sektoral yang terstruktur dan berkelanjutan.
                Program ini melibatkan OPD agar bersama-sama menghasilkan statistik sektoral berkualitas sesuai Sistem
                Statistik Nasional (SSN), demi layanan yang akurat dan profesional.
              </li>
              <li> Melalui SIGER BANDAR LAMPUNG, BPS memperkuat kolaborasi lintas instansi guna mengatasi tantangan pembinaan
                statistik sektoral dan meningkatkan kualitas pelayanan, agar sejalan dengan standar nasional dan
                mendorong kinerja unggul.
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="partner-logos">

        <div class="logo-container">
          <img src="img/logo-kotabalam.PNG" alt="" />
        </div>
        <div class="logo-container">
          <img src="img/logo-bps.png" alt="" />
        </div>
        <div class="logo-container">
          <img src="img/logo-kemensos.png" alt="" />
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
        <div class='tableauPlaceholder' id='viz1754147765448' style='position: relative'><noscript><a href='#'><img alt=' ' src='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Da&#47;DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3&#47;1_rss.png' style='border: none' /></a></noscript><object class='tableauViz' style='display:none;'>
            <param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' />
            <param name='embed_code_version' value='3' />
            <param name='site_root' value='' />
            <param name='name' value='DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3' />
            <param name='tabs' value='yes' />
            <param name='toolbar' value='yes' />
            <param name='static_image' value='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Da&#47;DashboardSIGERLAMPUNG-BPS1871&#47;Dashboard3&#47;1.png' />
            <param name='animate_transition' value='yes' />
            <param name='display_static_image' value='yes' />
            <param name='display_spinner' value='yes' />
            <param name='display_overlay' value='yes' />
            <param name='display_count' value='yes' />
            <param name='language' value='en-US' />
          </object></div>
        <script type='text/javascript'>
          var divElement = document.getElementById('viz1754147765448');
          var vizElement = divElement.getElementsByTagName('object')[0];
          if (divElement.offsetWidth > 800) {
            vizElement.style.minWidth = '1024px';
            vizElement.style.maxWidth = '1400px';
            vizElement.style.width = '100%';
            vizElement.style.minHeight = '650px';
            vizElement.style.maxHeight = '950px';
            vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
          } else if (divElement.offsetWidth > 500) {
            vizElement.style.minWidth = '1024px';
            vizElement.style.maxWidth = '1400px';
            vizElement.style.width = '100%';
            vizElement.style.minHeight = '650px';
            vizElement.style.maxHeight = '950px';
            vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
          } else {
            vizElement.style.width = '100%';
            vizElement.style.height = '1350px';
          }
          var scriptElement = document.createElement('script');
          scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
          vizElement.parentNode.insertBefore(scriptElement, vizElement);
        </script>
      </div>

    </div>
  </section>

  <!-- tanya DTSEN Section -->
  <section id="tabulasi" class="tabulasi-section">
    <div class="container">
      <h2 class="section-title" style="color: #006a9f">
        TANYA DTSEN
      </h2>

      <div class="tabulasi-card">

        <!-- <div class="max-w-8xl mx-auto px-4">
          <div class="bg-white rounded-xl shadow-md p-6 h-[400px] overflow-hidden flex flex-col mx-auto max-h-[400px]">
            <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 mb-4 flex flex-col">
            </div>
            <form id="chat-form" class="flex gap-2" onsubmit="kirimPesan(event)">
              <input type="text" id="user-input" class="flex-1 border border-gray-300 rounded-lg px-4 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis pertanyaan kamu disini...">
              <button type="submit" class="bg-blue-600 text-white px-4 py-4 rounded-lg hover:bg-blue-700">Kirim</button>
            </form>
          </div>
        </div> -->
        <div class="max-w-8xl mx-auto px-4">
          <div class="bg-white rounded-xl shadow-md p-6 h-[400px] overflow-hidden flex flex-col mx-auto max-h-[400px]">
            <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 mb-4 flex flex-col">
              <!-- tempat chat bubble -->
            </div>
            <form id="chat-form" class="flex gap-2 relative" onsubmit="kirimPesan(event)">
              <div class="flex-1 relative">
                <input
                  type="text"
                  id="user-input"
                  class="w-full border border-gray-300 rounded-lg px-4 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Tulis pertanyaan kamu disini..."
                  autocomplete="off">
                <!-- kotak suggestion -->
                <div id="suggestions" class="absolute left-0 right-0 bottom-full bg-white border border-gray-200 rounded-lg mb-1 shadow-lg hidden max-h-40 overflow-y-auto z-10"></div>
              </div>
              <button type="submit" class="bg-blue-600 text-white px-4 py-4 rounded-lg hover:bg-blue-700">Kirim</button>
            </form>
          </div>
        </div>

      </div>

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
          <span class="footer-brand">SIGER BANDAR LAMPUNG DTSEN</span>
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
  <script>
    function kirimPesan(event) {
      event.preventDefault();

      const input = document.getElementById("user-input");
      const pesan = input.value.trim();
      if (!pesan) return;

      const chatMessages = document.getElementById("chat-messages");

      // Pesan user di kanan
      const userWrapper = document.createElement("div");
      userWrapper.className = "flex justify-end px-4 py-2 mb-2";
      const userMessage = document.createElement("div");
      userMessage.className = "bg-blue-100 px-4 py-3 m-2 rounded-md shadow max-w-[80%]";
      userMessage.textContent = pesan;
      userWrapper.appendChild(userMessage);
      chatMessages.appendChild(userWrapper);

      chatMessages.scrollTop = chatMessages.scrollHeight;
      input.value = "";

      // Loading bubble bot di kiri
      const botWrapper = document.createElement("div");
      botWrapper.className = "flex justify-start px-4 py-2 mb-2";
      const typingMessage = document.createElement("div");
      typingMessage.className = "bg-gray-100 px-4 py-3 m-2 rounded-md shadow max-w-[80%] flex items-center gap-2";
      typingMessage.innerHTML = `<span class="dot-typing"></span>`;
      botWrapper.appendChild(typingMessage);
      chatMessages.appendChild(botWrapper);
      chatMessages.scrollTop = chatMessages.scrollHeight;

      fetch("http://localhost:8000/chat", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            message: pesan
          })
        })
        .then(response => response.json())
        .then(data => {
          botWrapper.remove();
          const botMessageWrapper = document.createElement("div");
          botMessageWrapper.className = "flex justify-start px-4 py-2 mb-2";
          const botMessage = document.createElement("div");
          botMessage.className = "bg-gray-200 px-4 py-3 m-2 rounded-md shadow max-w-[80%]";
          botMessage.textContent = data.answer;
          botMessageWrapper.appendChild(botMessage);
          chatMessages.appendChild(botMessageWrapper);
          chatMessages.scrollTop = chatMessages.scrollHeight;
        })
        .catch(error => {
          botWrapper.remove();
          const errorWrapper = document.createElement("div");
          errorWrapper.className = "flex justify-start text-red-500";
          errorWrapper.textContent = "❌ Gagal kirim: " + error;
          chatMessages.appendChild(errorWrapper);
        });
    }
  </script>
  <!-- Pertanyaan saran -->
  <script>
    let pertanyaanList = [];

    // Ambil data history.json
    fetch("ai-backend/history.json")
      .then(res => res.json())
      .then(data => {
        // hitung frekuensi tiap pertanyaan
        let freq = {};
        data.forEach(item => {
          let q = item[0].trim();
          let key = q.toLowerCase();
          if (!freq[key]) {
            freq[key] = {
              text: q,
              count: 0
            };
          }
          freq[key].count++;
        });

        // convert ke array lalu sort berdasarkan frekuensi
        pertanyaanList = Object.values(freq).sort((a, b) => b.count - a.count);

        // console.log("Pertanyaan berdasarkan frekuensi:", pertanyaanList);
      })
      .catch(err => console.error("Gagal load history.json:", err));

    const userInput = document.getElementById("user-input");
    const suggestionsBox = document.getElementById("suggestions");

    userInput.addEventListener("input", () => {
      const query = userInput.value.toLowerCase();
      suggestionsBox.innerHTML = "";

      if (!query) {
        suggestionsBox.classList.add("hidden");
        return;
      }

      // filter pertanyaan berdasarkan input user
      const filtered = pertanyaanList
        .filter(p => p.text.toLowerCase().includes(query))
        .slice(0, 5); // ambil max 5

      if (filtered.length === 0) {
        suggestionsBox.classList.add("hidden");
        return;
      }

      filtered.forEach(item => {
        const div = document.createElement("div");
        div.textContent = `${item.text}`;
        div.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer";
        div.onclick = () => {
          userInput.value = item.text;
          suggestionsBox.classList.add("hidden");
          userInput.focus();
        };
        suggestionsBox.appendChild(div);
      });

      suggestionsBox.classList.remove("hidden");
    });

    // biar suggestions hilang pas klik di luar
    document.addEventListener("click", (e) => {
      if (!e.target.closest("#chat-form")) {
        suggestionsBox.classList.add("hidden");
      }
    });
  </script>

  <!-- carousel js -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const container = document.querySelector(".overflow-x-auto");
      let scrollAmount = 0;
      let step = 1; // pixel geser per interval
      let maxScroll = container.scrollWidth - container.clientWidth;

      function autoScroll() {
        scrollAmount += step;

        // kalau sudah mentok kanan/balik ke awal
        if (scrollAmount >= maxScroll) {
          scrollAmount = 0;
        }

        container.scrollLeft = scrollAmount; // langsung geser tanpa smooth
      }

      setInterval(autoScroll, 10); // geser tiap 10ms (halus)
    });
  </script>




</body>

</html>