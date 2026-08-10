// ============================================
// FUNGSI DETAIL PRODUK (Modal)
// ============================================
function detailProduk(nama, harga, kategori, deskripsi) {
    document.getElementById('detail-nama').textContent = nama;
    document.getElementById('detail-harga').textContent = 'Rp ' + harga;
    document.getElementById('detail-kategori').textContent = kategori || '-';
    document.getElementById('detail-deskripsi').textContent = deskripsi || '-';
    
    // Tampilkan modal detail
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
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
    
    // Reset preview gambar
    document.getElementById('preview-gambar').innerHTML = '';
    
    // Tampilkan modal edit
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

// ============================================
// PREVIEW GAMBAR SAAT UPLOAD
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Preview untuk input gambar di form tambah
    const fileInput = document.querySelector('input[name="gambar"]');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            previewImage(e.target, 'preview-tambah');
        });
    }
    
    // Preview untuk input gambar di modal edit
    const editFileInput = document.querySelector('input[name="edit_gambar"]');
    if (editFileInput) {
        editFileInput.addEventListener('change', function(e) {
            previewImage(e.target, 'preview-gambar');
        });
    }
});

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" 
                     style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #ddd;">
            `;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}