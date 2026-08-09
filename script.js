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
// FUNGSI EDIT PRODUK (Modal)
// ============================================
function editProduk(id, nama, harga, kategori, deskripsi) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-harga').value = harga;
    document.getElementById('edit-kategori').value = kategori || '';
    document.getElementById('edit-deskripsi').value = deskripsi || '';
    
    // Tampilkan modal edit
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}