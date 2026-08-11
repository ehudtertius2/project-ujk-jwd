// ============================================
// FUNGSI DETAIL PRODUK (Modal)
// ============================================
function detailProduk(nama, harga, kategori, deskripsi, gambar) {
    const isi = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;   // aman walau elemen tidak ada
    };

    isi('detail-nama', nama);
    isi('detail-harga', 'Rp ' + harga);
    isi('detail-kategori', kategori || '-');
    isi('detail-deskripsi', deskripsi || '-');

    const img = document.getElementById('detail-gambar');
    if (img) {                            // <-- ini yang dulu bikin crash
        img.src = gambar || 'images/default.png';
        img.alt = nama || 'Gambar produk';
    }

    const modalEl = document.getElementById('detailModal');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

// ============================================
// FUNGSI EDIT PRODUK (Modal) - Admin Only
// ============================================
function editProduk(id, nama, harga, kategori, deskripsi) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-harga').value = harga;
    document.getElementById('edit-kategori').value = kategori || '';
    document.getElementById('edit-deskripsi').value = deskripsi || '';
    document.getElementById('preview-gambar').innerHTML = '';

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

// ============================================
// TOMBOL DETAIL — event delegation
// (tetap jalan walau cache lama / DOM berubah)
// ============================================
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;

    detailProduk(
        btn.dataset.nama,
        btn.dataset.harga,
        btn.dataset.kategori,
        btn.dataset.deskripsi,
        btn.dataset.gambar
    );
});

// ============================================
// PREVIEW GAMBAR SAAT UPLOAD
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('input[name="gambar"]');
    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            previewImage(e.target, 'preview-tambah');
        });
    }

    const editFileInput = document.querySelector('input[name="edit_gambar"]');
    if (editFileInput) {
        editFileInput.addEventListener('change', function (e) {
            previewImage(e.target, 'preview-gambar');
        });
    }
});

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #ddd;">`;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}