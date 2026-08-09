<?php
include 'config/database.php';

// ============================================
// PROSES CRUD
// ============================================

// CREATE - Tambah produk
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $gambar_default = 'default.png';
    $sql = "INSERT INTO produk (nama, harga, kategori, deskripsi) 
            VALUES ('$nama', $harga, '$kategori', '$deskripsi')";
    
    if ($conn->query($sql)) {
        $success = "Produk berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan produk: " . $conn->error;
    }
}

// UPDATE - Edit produk
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $sql = "UPDATE produk SET 
            nama='$nama', 
            harga=$harga, 
            kategori='$kategori', 
            deskripsi='$deskripsi' 
            WHERE id=$id";
    
    if ($conn->query($sql)) {
        $success = "Produk berhasil diupdate!";
    } else {
        $error = "Gagal update produk: " . $conn->error;
    }
}

// DELETE - Hapus produk
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $sql = "DELETE FROM produk WHERE id=$id";
    
    if ($conn->query($sql)) {
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
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- ===== HEADER ===== -->
        <div class="text-center mb-4">
            <h2><i class="fas fa-tshirt text-primary me-2"></i>Produk Rajut Kami</h2>
            <p class="text-muted">Karya tangan berkualitas dari pengrajin lokal</p>
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

        <!-- ===== FORM TAMBAH PRODUK (CREATE) ===== -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-plus-circle me-2"></i>Tambah Produk Baru
            </div>
            <div class="card-body">
                <form method="POST" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Produk" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="kategori" class="form-control" placeholder="Kategori">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi singkat">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" name="tambah" class="btn btn-success w-100">
                            <i class="fas fa-save me-1"></i>Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== DAFTAR PRODUK (READ) ===== -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="images/<?= $row['gambar'] ?? 'default.png' ?>" 
                             class="card-img-top" alt="<?= $row['nama'] ?>"
                             style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
                            <p class="text-danger fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            <span class="badge bg-secondary"><?= htmlspecialchars($row['kategori']) ?></span>
                            <p class="mt-2 small text-muted"><?= htmlspecialchars($row['deskripsi']) ?></p>
                            
                            <div class="mt-3">
                                <!-- Tombol Detail (JavaScript) -->
                                <button class="btn btn-info btn-sm" onclick="detailProduk('<?= htmlspecialchars($row['nama']) ?>', '<?= number_format($row['harga'], 0, ',', '.') ?>', '<?= htmlspecialchars($row['kategori']) ?>', '<?= htmlspecialchars($row['deskripsi']) ?>')">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </button>
                                
                                <!-- Tombol Edit (Modal) -->
                                <button class="btn btn-warning btn-sm" onclick="editProduk(<?= $row['id'] ?>, '<?= addslashes($row['nama']) ?>', <?= $row['harga'] ?>, '<?= addslashes($row['kategori']) ?>', '<?= addslashes($row['deskripsi']) ?>')">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                
                                <!-- Tombol Hapus -->
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus produk ini?')">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </a>
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
        </div>
    </div>

    <!-- ===== MODAL EDIT (UPDATE) ===== -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
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

    <!-- ===== MODAL DETAIL (JavaScript) ===== -->
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
            <p class="mb-0">&copy; 2024 - Nama Kamu | Toko Rajut</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</php>