<?php
session_start();
include 'config/database.php';

// Cek login - jika belum login, redirect ke login.php
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

// Cek role user
$isAdmin = ($_SESSION['role'] === 'admin');

$kategori_list = [
    'Aksesoris' => ' Aksecoris',
    'Pakaian' => ' Pakaian',
    'Dekorasi' => ' Dekorasi',
    'Mainan' => ' Mainan',
    'Tas & Dompet' => ' Tas & Dompet',
    'Perlengkapan Bayi' => ' Perlengkapan Bayi',
    'Lainnya' => ' Lainnya'
];

// ============================================
// PROSES CRUD (HANYA UNTUK ADMIN)
// ============================================

// CREATE - Tambah produk (Admin only)
if (isset($_POST['tambah']) && $isAdmin) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $gambar = 'default.png'; // default jika tidak upload
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $target_dir = "images/";
        
        // Buat folder images jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate nama file unik
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validasi file gambar
        $check = getimagesize($_FILES['gambar']['tmp_name']);
        if ($check !== false) {
            // Batasi ukuran file (maks 5MB)
            if ($_FILES['gambar']['size'] <= 5 * 1024 * 1024) {
                // Batasi tipe file
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array(strtolower($file_extension), $allowed_types)) {
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                        $gambar = $new_filename;
                    } else {
                        $error = "Gagal upload gambar!";
                    }
                    } else {
                        $error = "Tipe file tidak diizinkan! (JPG, PNG, GIF, WEBP)";
                    }
                    } else {
                        $error = "Ukuran file terlalu besar! Maksimal 5MB";
                    }
                    } else {
                        $error = "File bukan gambar yang valid!";
                    }
    }
    
    // Jika tidak ada error, simpan ke database
    if (!isset($error)) {
        $sql = "INSERT INTO produk (nama, harga, kategori, deskripsi, gambar) 
                VALUES ('$nama', $harga, '$kategori', '$deskripsi', '$gambar')";
        
        if ($conn->query($sql)) {
            $success = "Produk berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan produk: " . $conn->error;
        }
    }
}


// UPDATE - Edit produk (Admin only)
if (isset($_POST['update']) && $isAdmin) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $result_old = $conn->query("SELECT gambar FROM produk WHERE id=$id");
    $old_data = $result_old->fetch_assoc();
    $gambar = $old_data['gambar'] ?? 'default.png';
    
    // ===== PROSES UPLOAD GAMBAR BARU =====
    if (isset($_FILES['edit_gambar']) && $_FILES['edit_gambar']['error'] === 0) {
        $target_dir = "images/";
        
        // Buat folder images jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate nama file unik
        $file_extension = pathinfo($_FILES['edit_gambar']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validasi file gambar
        $check = getimagesize($_FILES['edit_gambar']['tmp_name']);
        if ($check !== false) {
            if ($_FILES['edit_gambar']['size'] <= 5 * 1024 * 1024) {
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array(strtolower($file_extension), $allowed_types)) {
                    if (move_uploaded_file($_FILES['edit_gambar']['tmp_name'], $target_file)) {
                        // Hapus gambar lama jika bukan default
                        if ($gambar !== 'default.png' && file_exists($target_dir . $gambar)) {
                            unlink($target_dir . $gambar);
                        }
                        $gambar = $new_filename;
                        } else {
                            $error = "Gagal upload gambar!";
                        }
                        } else {
                            $error = "Tipe file tidak diizinkan! (JPG, PNG, GIF, WEBP)";
                        }
                        } else {
                            $error = "Ukuran file terlalu besar! Maksimal 5MB";
                        }
                        } else {
                            $error = "File bukan gambar yang valid!";
                        }
                    }
        
    // Jika tidak ada error, update database
    if (!isset($error)) {
        $sql = "UPDATE produk SET 
                nama='$nama', 
                harga=$harga, 
                kategori='$kategori', 
                deskripsi='$deskripsi',
                gambar='$gambar'
                WHERE id=$id";
        
        if ($conn->query($sql)) {
            $success = "Produk berhasil diupdate!";
        } else {
            $error = "Gagal update produk: " . $conn->error;
        }
    }
}


// DELETE - Hapus produk (Admin only)
if (isset($_GET['hapus']) && $isAdmin) {
    $id = (int)$_GET['hapus'];

    $result_img = $conn->query("SELECT gambar FROM produk WHERE id=$id");
    $data_img = $result_img->fetch_assoc();

    $sql = "DELETE FROM produk WHERE id=$id";

    if ($conn->query($sql)) {
        $gambar = trim((string)($data_img['gambar'] ?? ''));
        $target_dir = "images/";

        if (
            $gambar !== '' &&
            $gambar !== 'default.png' &&
            basename($gambar) === $gambar &&
            is_file($target_dir . $gambar)
        ) {
            unlink($target_dir . $gambar);
        }

        $success = "Produk berhasil dihapus!";
    } else {
        $error = "Gagal hapus produk: " . $conn->error;
    }
}


// READ - Ambil semua produk
$result = $conn->query("SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Rajut - Produk Karya Tangan</title>
    <link rel="icon" type="image/png" href="images/pngwing.com.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.html">
                <i class="fas fa-user-circle me-2"></i>MyPortfolio
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-home me-1"></i>Profil
                </a>
                <a class="nav-link active" href="toko.php">
                    <i class="fas fa-store me-1"></i>Toko Rajut
                </a>
                <a class="nav-link text-warning" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- ===== HEADER ===== -->
        <div class="text-center mb-4">
            <h2><i class="fas fa-tshirt text-primary me-2"></i>Produk Rajut Kami</h2>
            <p class="text-muted">Karya tangan berkualitas dari pengrajin lokal</p>
            
            <!-- Info User -->
            <div class="d-inline-block bg-light px-3 py-2 rounded">
                <?php if ($isAdmin): ?>
                    <span class="badge bg-warning text-dark ms-1">
                        <i class="fas fa-cogs me-1"></i>Full Akses
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary ms-1">
                        <i class="fas fa-eye me-1"></i>Hanya Lihat
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== NOTIFIKASI ===== -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ===== FORM TAMBAH PRODUK (CREATE) - HANYA ADMIN ===== -->
        <?php if ($isAdmin): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-plus-circle me-2"></i>Tambah Produk Baru
            </div>
            <div class="card-body">
                <form method="POST" class="row g-2" enctype="multipart/form-data">
                    <div class="col-md-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Produk" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                    </div>
                    <div class="col-md-3">
                        <select name="kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori_list as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi singkat">
                    </div>
                    <!-- ===== INPUT GAMBAR ===== -->
                    <div class="col-md-6">
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF, WEBP | Maks: 5MB</small>
                        <!-- Preview gambar -->
                        <div id="preview-tambah" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="tambah" class="btn btn-success w-100">
                            <i class="fas fa-save me-1"></i>Tambah Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Pesan untuk user biasa -->
        <div class="alert alert-info text-center mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Anda login sebagai <strong>Pengunjung</strong>. Anda hanya bisa melihat produk.
            <?php if (!isset($_SESSION['login'])): ?>
                <a href="login.php" class="alert-link">Login sebagai admin</a> untuk mengelola produk.
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ===== DAFTAR PRODUK (READ) ===== -->
<div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <!-- ===== TAMPILAN GAMBAR ===== -->
                <?php 
                $gambar_path = 'images/' . ($row['gambar'] ?? 'default.png');
                if (!file_exists($gambar_path)) {
                    $gambar_path = 'images/default.png';
                }
                ?>
                <img src="<?= $gambar_path ?>" 
                     class="card-img-top" alt="<?= htmlspecialchars($row['nama']) ?>"
                     style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
                    <p class="text-danger fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    <span class="badge bg-secondary"><?= htmlspecialchars($row['kategori']) ?></span>
                    <p class="mt-2 small text-muted"><?= htmlspecialchars($row['deskripsi']) ?></p>
                    
                    <div class="mt-3">
                        <!-- Tombol Detail (Semua user bisa lihat) -->
                        <button class="btn btn-info btn-sm" onclick="detailProduk('<?= htmlspecialchars($row['nama']) ?>', '<?= number_format($row['harga'], 0, ',', '.') ?>', '<?= htmlspecialchars($row['kategori']) ?>', '<?= htmlspecialchars($row['deskripsi']) ?>')">
                            <i class="fas fa-eye me-1"></i>Detail
                        </button>
                        
                        <!-- Tombol Edit & Hapus - HANYA ADMIN -->
                        <?php if ($isAdmin): ?>
                        <button class="btn btn-warning btn-sm" onclick="editProduk(<?= $row['id'] ?>, '<?= addslashes($row['nama']) ?>', <?= $row['harga'] ?>, '<?= addslashes($row['kategori']) ?>', '<?= addslashes($row['deskripsi']) ?>')">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus produk ini?')">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </a>
                        <?php else: ?>
                        <span class="badge bg-secondary">
                            <i class="fas fa-lock me-1"></i>Login admin untuk edit
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>Belum ada produk. Tambahkan produk pertama!
            </div>
        </div>
    <?php endif; ?>
</div>
        
        <!-- ===== INFO JUMLAH PRODUK ===== -->
        <div class="text-center text-muted mt-3">
            <small>Total produk: <?= $result->num_rows ?></small>
            <?php if (!$isAdmin): ?>
                <span class="ms-3">
                    <i class="fas fa-lock me-1"></i>
                    <small>Login sebagai admin untuk mengelola produk</small>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== MODAL EDIT (UPDATE) - HANYA ADMIN ===== -->
    <?php if ($isAdmin): ?>
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        
                        <div class="mb-2">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="nama" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" id="edit-harga" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="kategori" id="edit-kategori" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" id="edit-deskripsi" class="form-control">
                        </div>
                    </div>
                        <div class="mb-2">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" name="edit_gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                        <div id="preview-gambar" class="mt-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== MODAL DETAIL (Semua user bisa lihat) ===== -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama:</strong> <span id="detail-nama"></span></p>
                    <p><strong>Harga:</strong> <span id="detail-harga"></span></p>
                    <p><strong>Kategori:</strong> <span id="detail-kategori"></span></p>
                    <p><strong>Deskripsi:</strong> <span id="detail-deskripsi"></span></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2024 - Ehud Tertius Simanjuntak | Toko Rajut</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>