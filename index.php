<?php
// load config.php
include "config/config.php";

$hasil = null;
$keyword = "";

// Cek apakah ada kata kunci pencarian
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    if ($keyword !== "") {
        // Encode kata kunci untuk URL
        $query = urlencode($keyword);
        // URL API OpenLibrary untuk pencarian
        $url = "https://openlibrary.org/search.json?q={$query}";
        
        // Melakukan request HTTP (asumsi fungsi http_request_get ada di config.php)
        $response = http_request_get($url);
        
        // Konversi data JSON ke array PHP
        $hasil = json_decode($response, true);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RESTClient Buku (OpenLibrary API)</title>
    <!-- CSS Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { 
            background-color: #B9DDFF; 
            padding-top: 75px; 
        }

        .card-img-top-cover {
            height: 250px; 
            object-fit: contain; 
            width: 100%;
            border-bottom: 1px solid rgba(0,0,0,.125);
            background-color: #ffffff; 
        }
        /* Style text "RestClient */
        .navbar-brand {
            color: #adb5bd !important; 
            font-weight: bold;
            font-size: 1.5rem;
        }
        /* Style untuk tombol cari */
        .btn-info[type="submit"] {
            color: #adb5bd !important;
            font-weight: bold;
        }
        .btn-info[type="submit"]:hover,
        .btn-info[type="submit"]:focus {
            color: #ffffff !important;
            background-color: #17a2b8; 
            border-color: #17a2b8;
        }
        .container {
            color: #19354F !important;
            font-weight: bold;
            max-width: 85% !important; 
        }
    </style>
</head>
<body>

<!-- navbar -->
<nav class="navbar fixed-top navbar-dark px-4" style="background-color: #19354F !important;">
        <a class="navbar-brand" href="#">RESTClient Buku</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Cari Buku <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- navbar -->

<div class="container p-4">
    <h2 class="mb-4">Cari Buku (Source OpenLibrary)</h2>
    
    <!-- Form Pencarian -->
    <form method="get" class="mb-5">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Masukkan judul buku/nama author..." value="<?= htmlspecialchars($keyword) ?>">
            <div class="input-group-append">
                <button class="btn btn-info" style="background-color: #19354F !important" type="submit">Cari</button>
            </div>
        </div>
    </form>
    <!-- End Form Pencarian -->

    <div class="row">
    <?php if ($hasil !== null): ?>
        <?php if (isset($hasil['docs']) && count($hasil['docs']) > 0): ?>
            <!-- Looping hasil data (maksimal 12 buku) -->
            <?php foreach (array_slice($hasil['docs'], 0, 12) as $buku): ?>
                <?php
                    // Ambil data buku dengan nilai default jika tidak ada
                    $judul = $buku['title'] ?? "Judul Tidak Diketahui";
                    $penulis = isset($buku['author_name']) ? implode(", ", array_slice($buku['author_name'], 0, 2)) : "Tidak ada penulis"; // Batasi 2 penulis
                    $tahun = $buku['first_publish_year'] ?? "-";
                    // URL cover buku (OpenLibrary Cover ID)
                    $cover_id = $buku['cover_i'] ?? null;
                    $cover_url = $cover_id ? "https://covers.openlibrary.org/b/id/{$cover_id}-M.jpg" : "https://via.placeholder.com/180x250?text=No+Cover";
                    // URL ke halaman buku di OpenLibrary (menggunakan Open Library ID pertama)
                    $ol_id = isset($buku['lending_edition_s']) ? $buku['lending_edition_s'] : (isset($buku['key']) ? str_replace('/works/', 'ol_id', $buku['key']) : null);
                    $book_url = $ol_id ? "https://openlibrary.org/works/" . str_replace('/works/', '', $buku['key']) : "#";
                ?>
                <div class="col-md-4 col-lg-3 mb-4"> <!-- Gunakan col-lg-3 untuk 4 kolom di layar besar, atau col-md-4 untuk 3 kolom di layar sedang -->
                    <div class="card h-100"> <!-- h-100 untuk tinggi card yang sama -->
                        <img src="<?= htmlspecialchars($cover_url) ?>" class="card-img-top-cover" alt="Cover Buku: <?= htmlspecialchars($judul) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($judul) ?></h5>
                            <p class="card-text text-muted mb-1">
                                <small><strong>Penulis:</strong> <?= htmlspecialchars($penulis) ?></small>
                            </p>
                            <p class="card-text text-muted mb-3">
                                <small><strong>Tahun:</strong> <?= htmlspecialchars($tahun) ?></small>
                            </p>
                            <p class="text-right mt-auto"> 
                                <a href="<?= htmlspecialchars($book_url) ?>" class="btn btn-sm btn-outline-info" target="_blank">Detail Buku</a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <p class="mb-0">Tidak ditemukan hasil untuk "<b><?= htmlspecialchars($keyword) ?></b>".</p>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                <p class="mb-0">Masukkan kata kunci di atas untuk mulai mencari buku.</p>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
