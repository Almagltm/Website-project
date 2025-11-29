<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Laporan - Aksi Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* RESET & BASE STYLES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f7fc; /* Warna background abu-abu kebiruan muda */
            color: #333;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #1e3d8f;
        color: white;
        padding: 20px 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        width: 1170px;
        transform: translate(15%, 45%);
        }

        .back-btn {
            text-decoration: none;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            transition: 0.3s;
            font-size: 14px;
        }

        .header h1 {
            letter-spacing: 0.5px;
            font-size: 22px;
            font-weight: 700;
            flex-grow: 1; /* Agar teks di tengah jika mau, tapi di gambar rata kiri-tengah */
            text-align: center;
            margin-right: 40px; /* Kompensasi margin back button agar benar-benar tengah */
        }

        /* MAIN CONTAINER */
        .container {
            max-width: 1170px;
            margin: 30px auto;
            padding: 50px 0px;
        }

        .section-title {
            color: #2b4b94;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        /* CHART CARD CONTAINER */
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            gap: 40px;
            align-items: center;
            border: 2px solid #eef1f8; /* Border halus */
            margin-bottom: 40px;
        }

        /* CHART AREA (KIRI) */
        .chart-area {
            flex: 2;
            position: relative;
            height: 300px;
            border-left: 1px solid #eee;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            padding-bottom: 10px;
            padding-top: 30px; /* Ruang untuk angka di atas bar */
        }

        /* Garis horizontal latar belakang grafik */
        .grid-line {
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px solid #f0f0f0;
            z-index: 0;
        }

        /* BAR STYLING */
        .bar-container {
            position: relative;
            width: 45px; /* Lebar batang */
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
        }

        .bar {
            width: 100%;
            border-radius: 8px 8px 4px 4px;
            transition: height 1s ease-out;
        }

        /* Angka di atas batang */
        .bar-value {
            font-size: 12px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        /* Warna Batang Spesifik (Mirip Gambar) */
        .bar-1 { height: 250px; background-color: #ffeeb0; } /* Cream */
        .bar-2 { height: 135px; background-color: #ffcd57; } /* Kuning */
        .bar-3 { height: 210px; background-color: #eeb026; } /* Orange Emas */
        .bar-4 { height: 190px; background-color: #9e7c20; } /* Coklat Olive */
        .bar-5 { height: 50px;  background-color: #6b3e0e; } /* Coklat Tua */
        .bar-6 { height: 50px;  background-color: #000000; } /* Hitam */

        /* LEGEND (KANAN) */
        .legend {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 12px;
            color: #333;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Warna Dot Legend */
        .dot-1 { background-color: #ffeeb0; }
        .dot-2 { background-color: #ffcd57; }
        .dot-3 { background-color: #eeb026; }
        .dot-4 { background-color: #9e7c20; }
        .dot-5 { background-color: #6b3e0e; }
        .dot-6 { background-color: #000000; }

        /* SUMMARY BOX (BAWAH) */
        .summary-box {
            background: linear-gradient(90deg, #cda863 0%, #dcc186 100%); /* Gradasi Emas/Coklat */
            border-radius: 20px;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 10px 25px rgba(205, 168, 99, 0.4);
            margin-bottom: 50px;
        }

        .stat-item {
            display: flex;
            align-items: flex-start; /* Icon sejajar dengan baris pertama teks */
            gap: 15px;
            flex: 1;
        }

        /* Garis pembatas antar stat (opsional, visual separation) */
        .stat-item:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.3);
            margin-right: 20px;
        }

        .stat-icon-box {
            background: transparent;
            border: 2px solid white;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-info h4 {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .stat-info span {
            font-size: 20px;
            font-weight: 700;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .chart-card {
                flex-direction: column;
                align-items: stretch;
            }
            .chart-area {
                padding: 10px;
                gap: 5px;
            }
            .bar-container {
                width: 12%; /* Bar lebih kecil di HP */
            }
            .summary-box {
                flex-direction: column;
                gap: 30px;
                align-items: flex-start;
            }
            .stat-item {
                border-right: none !important;
                width: 100%;
            }
        }

        .main-footer {
  background: linear-gradient(165deg, #080e18 0%, #102647 70%, #9c7719 120%);
  color: #fff;
  padding: 60px 70px;
  font-family: Arial, sans-serif;
}

/* Header logo + nama */
.footer-top {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 40px;
}

.footer-logo {
  width: 75px;
  height: 55px;
  object-fit: cover;
}

/* Konten kolom */
.footer-content {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  margin-bottom: 45px;
  gap: 20px;
}

.footer-col {
  flex: 1;
  min-width: 200px;
}

.footer-col p {
  margin: 6px 0;
  color: #ccc;
  font-size: 15px;
}

.footer-col a {
  display: block;
  margin: 6px 0;
  color: #eee;
  text-decoration: none;
  font-size: 15px;
  transition: 0.2s;
}

.footer-col a:hover {
  color: #0077ff;
}

/* Ikon sosial */
.footer-social {
  display: flex;
  gap: 15px;
  margin-bottom: 35px;
}

.footer-social a {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #000;
  background: #fff;
  text-decoration: none;
  font-size: 18px;
  transition: 0.3s;
}

.footer-social a:hover {
  transform: translateY(-5px);
}

/* Copyright */
.footer-bottom {
  text-align: center;
  font-size: 14px;
  color: #ccc;
  margin-top: 10px;
}


@media (max-width: 800px) {
  .footer-content {
    flex-direction: column;
  }
  
  .footer-top {
    flex-direction: column;
    text-align: center;
  }
}

.footer {
    background: rgba(0, 0, 0, 0.674);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 80px;
    font-size: 14px;
    }

    .footer-container p {
    margin: 0;
    opacity: 0.9;
    }

    </style>
</head>
<body>

    <header class="header">
        <a href="BERANDA2.html" class="back-btn"><i class="fa-solid fa-chevron-left"></i></a>
        <h1>Statistik Laporan</h1>
    </header>

    <div class="container">
        
        <h2 class="section-title">Statistik Laporan Bulan Ini</h2>

        <div class="chart-card">
            
            <div class="chart-area">
                <div class="grid-line" style="bottom: 0;"></div>
                <div class="grid-line" style="bottom: 25%;"></div>
                <div class="grid-line" style="bottom: 50%;"></div>
                <div class="grid-line" style="bottom: 75%;"></div>
                <div class="grid-line" style="bottom: 100%;"></div>

                <div class="bar-container">
                    <span class="bar-value">65</span>
                    <div class="bar bar-1"></div>
                </div>

                <div class="bar-container">
                    <span class="bar-value">35</span>
                    <div class="bar bar-2"></div>
                </div>

                <div class="bar-container">
                    <span class="bar-value">55</span>
                    <div class="bar bar-3"></div>
                </div>

                <div class="bar-container">
                    <span class="bar-value">50</span>
                    <div class="bar bar-4"></div>
                </div>

                <div class="bar-container">
                    <span class="bar-value">12</span>
                    <div class="bar bar-5"></div>
                </div>

                <div class="bar-container">
                    <span class="bar-value">12</span>
                    <div class="bar bar-6"></div>
                </div>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <span class="dot dot-1"></span> Kerusakan jalan raya
                </div>
                <div class="legend-item">
                    <span class="dot dot-2"></span> Kerusakan tiang listrik
                </div>
                <div class="legend-item">
                    <span class="dot dot-3"></span> Kerusakan pipa
                </div>
                <div class="legend-item">
                    <span class="dot dot-4"></span> Kerusakan lampu jalan
                </div>
                <div class="legend-item">
                    <span class="dot dot-5"></span> Kerusakan halte
                </div>
                <div class="legend-item">
                    <span class="dot dot-6"></span> Kerusakan halte (Lainnya)
                </div>
            </div>
        </div>

        <div class="summary-box">
            
            <div class="stat-item">
                <div class="stat-icon-box">
                    <i class="fa-solid fa-check-square"></i>
                </div>
                <div class="stat-info">
                    <h4>Total laporan<br>bulan ini</h4>
                    <span>229</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon-box">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div class="stat-info">
                    <h4>Ditangani</h4>
                    <br> <span>171</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon-box">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h4>Rata-rata<br>waktu tanggap</h4>
                    <span>1 hari 8 jam</span>
                </div>
            </div>

        </div>

    </div>

    <footer class="main-footer">
  <div class="footer-top">
    <img src="ASSETS/LOGO.png" class="footer-logo" alt="AksiKita">
    <h3>Aksi Kita</h3>
  </div>

  <div class="footer-content">

    <!-- Kiri -->
    <div class="footer-col">
      <p>Jl. Bachireng No. 12, Indonesia</p>
      <p>0821 6888 9060</p>
      <p>info@aksikita.id</p>
    </div>

    <!-- Tengah -->
    <div class="footer-col">
      <a href="#">Unit Layanan Terpadu</a>
      <a href="#">Cara Kerja</a>
      <a href="#">FAQ</a>
      <a href="#">Aturan Penggunaan</a>
    </div>

    <!-- Kanan -->
    <div class="footer-col">
      <a href="#">Lapor</a>
      <a href="#">Survei</a>
      <a href="#">Peta Situs</a>
      <a href="#">Arsip</a>
    </div>

  </div>

  <div class="footer-social">
    <a href="#"><i class="fab fa-whatsapp"></i></a>
    <a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
    <a href="#"><i class="fab fa-tiktok"></i></a>
  </div>

  <div class="footer-bottom">
    © 2025 AksiKita. Semua Hak Dilindungi.
  </div>
</footer>
</body>
</html>