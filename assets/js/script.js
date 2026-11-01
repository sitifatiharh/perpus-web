// Fungsi untuk memunculkan dan menyembunyikan Sidebar Mobile
function toggleSidebar() {
    var sidebar = document.getElementById("mobile-sidebar");
    if (sidebar.classList.contains("open")) {
        sidebar.classList.remove("open");
    } else {
        sidebar.classList.add("open");
    }
}


// Inisialisasi DataTables Buku
$(document).ready(function() {
    // Mengecek apakah elemen tabel dengan id 'tabelBuku' ada di halaman tersebut
    if ($('#tabelBuku').length) {
        $('#tabelBuku').DataTable ({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" 
            },
            // Mencegah error responsivitas lebar kolom
            "autoWidth": false 
        });
    }
});


// Konfirmasi sebelum mengubah status transaksi
function konfirmasiStatus(selectElement) {
    var status = selectElement.value;
    var pesan = "Apakah Anda yakin ingin mengubah status peminjaman ini?";

    if (status === "selesai") {
        pesan = "Yakin ingin menyelesaikan transaksi ini? Status yang sudah selesai tidak dapat diubah lagi.";
    } else if (status === "dibatalkan") {
        pesan = "Yakin ingin membatalkan transaksi ini? Status yang dibatalkan tidak dapat diubah lagi.";
    }

    if (confirm(pesan)) {
        selectElement.form.submit();
    } else {
        // Kembalikan ke pilihan sebelumnya jika batal
        window.location.reload();
    }
}

// Inisialisasi DataTables Peminjaman
$(document).ready(function() {
    if ($('#tabelPeminjaman').length) {
        $('#tabelPeminjaman').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "autoWidth": false
        });
    }
});


// Inisialisasi DataTables Aggota
$(document).ready(function() {
    if ($('#tabelAnggota').length) {
        $('#tabelAnggota').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "autoWidth": false
        });
    }
});


// USER Katalog
// Toggle Dropdown Kategori
function toggleCategoryMenu() {
    const menu = document.getElementById('categoryMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Close dropdown jika klik di luar elemen
window.addEventListener('click', function(e) {
    const btn = document.getElementById('categoryBtn');
    const menu = document.getElementById('categoryMenu');
    if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('show');
    }
});

// Toggle Accordion Kategori
function toggleAccordion(header) {
    const item = header.parentElement;
    item.classList.toggle('active');
}

// Jump ke Kategori dan Buka Accordionnya
function jumpToCategory(categoryId) {
    const menu = document.getElementById('categoryMenu');
    if (menu) menu.classList.remove('show');

    const targetItem = document.getElementById(categoryId);
    if (targetItem) {
        // Buka accordion jika belum terbuka
        if (!targetItem.classList.contains('active')) {
            targetItem.classList.add('active');
        }
        // Smooth scroll ke elemen kategori
        targetItem.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Live Search Handling (Meratakan Accordion saat Mengetik)
function filterBooks() {
    const input = document.getElementById('searchInput').value.toLowerCase().trim();
    const accordionContainer = document.getElementById('accordionContainer');
    const searchContainer = document.getElementById('searchResultsContainer');
    const searchGrid = document.getElementById('searchResultsGrid');

    if (input.length > 0) {
        // Sembunyikan Mode Accordion, Tampilkan Mode Search Grid
        accordionContainer.style.display = 'none';
        searchContainer.style.display = 'block';
        searchGrid.innerHTML = '';

        const allCards = document.querySelectorAll('.book-card-source');
        let matchCount = 0;

        allCards.forEach(card => {
            const title = card.getAttribute('data-judul').toLowerCase();
            const desc = card.getAttribute('data-deskripsi').toLowerCase();
            const category = card.getAttribute('data-kategori').toLowerCase();

            if (title.includes(input) || desc.includes(input) || category.includes(input)) {
                const clone = card.cloneNode(true);
                clone.style.display = 'flex';
                searchGrid.appendChild(clone);
                matchCount++;
            }
        });

        if (matchCount === 0) {
            searchGrid.innerHTML = '<p class="no-img" style="grid-column: 1/-1; text-align:center; padding: 40px 0;">Tidak ada buku yang sesuai dengan kata kunci pencarian.</p>';
        }
    } else {
        // Kembalikan ke Mode Accordion Normal
        accordionContainer.style.display = 'block';
        searchContainer.style.display = 'none';
        searchGrid.innerHTML = '';
    }
}


// DEATAIL BUKU
// Buka Accordion Otomatis jika terdapat Parameter Kategori di URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const catParam = urlParams.get('cat');
    
    if (catParam) {
        const accordionTitles = document.querySelectorAll('.accordion-title span:first-child');
        accordionTitles.forEach(span => {
            if (span.textContent.trim().toLowerCase() === catParam.toLowerCase()) {
                const item = span.closest('.accordion-item');
                if (item) {
                    if (!item.classList.contains('active')) {
                        item.classList.add('active');
                    }
                    setTimeout(() => {
                        item.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 200);
                }
            }
        });
    }
});

// Toggle Accordion Kategori & Simpan State ke sessionStorage
function toggleAccordion(header) {
    const item = header.parentElement;
    item.classList.toggle('active');
    simpanStateAccordion();
}

// Menyimpan daftar ID accordion yang sedang aktif/terbuka
function simpanStateAccordion() {
    const activeIds = [];
    document.querySelectorAll('.accordion-item.active').forEach(item => {
        activeIds.push(item.id);
    });
    sessionStorage.setItem('openAccordions', JSON.stringify(activeIds));
}

// Mengembalikan state accordion saat halaman katalog dimuat
// Mengembalikan state SEMUA accordion yang pernah dibuka + menangani parameter kategori
function pulihkanStateAccordion() {
    // 1. Selalu pulihkan SEMUA accordion yang sebelumnya sudah dibuka user dari sessionStorage
    const saved = sessionStorage.getItem('openAccordions');
    if (saved) {
        try {
            const activeIds = JSON.parse(saved);
            activeIds.forEach(id => {
                const item = document.getElementById(id);
                if (item) {
                    item.classList.add('active');
                }
            });
        } catch(e) {
            console.error(e);
        }
    }

    // 2. Jika datang dari klik Kategori biru (?cat=...), pastikan kategori tersebut JUGA dibuka & di-scroll
    const urlParams = new URLSearchParams(window.location.search);
    const catParam = urlParams.get('cat');

    if (catParam) {
        document.querySelectorAll('.accordion-item').forEach(item => {
            const titleText = item.querySelector('.accordion-title span:first-of-type').textContent.trim();
            if (titleText.toLowerCase() === catParam.trim().toLowerCase()) {
                item.classList.add('active');
                setTimeout(() => {
                    item.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 200);
            }
        });
    }

    // 3. Simpan kembali status gabungan seluruh accordion yang terbuka
    simpanStateAccordion();
}

// Jalankan otomatis saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('accordionContainer')) {
        pulihkanStateAccordion();
    }
});


// RIWAYAT PEMINJAMAN USER
// Filter Pencarian di Halaman Riwayat Peminjaman
function filterRiwayat() {
    const input = document.getElementById('searchRiwayatInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.riwayat-card-item');

    cards.forEach(card => {
        const title = card.getAttribute('data-judul').toLowerCase();
        if (title.includes(input)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}