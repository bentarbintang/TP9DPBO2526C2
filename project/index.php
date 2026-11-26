<?php
/**
 * Front controller aplikasi MVP (Strict Passive View).
 *
 * Routing:
 *   GET  index.php?module=kandidat|lowongan&screen=list|add|edit[&id=N]
 *   POST index.php  (field: module, action = tambah | ubah | hapus)
 *
 * index.php hanya merangkai Model -> View -> Presenter lalu meneruskan
 * request ke Presenter. Tidak ada logika bisnis maupun HTML di sini.
 */
// Muat komponen modul Kandidat (Model, View, Presenter).
require_once __DIR__ . '/models/TabelKandidat.php';
require_once __DIR__ . '/views/ViewKandidat.php';
require_once __DIR__ . '/presenters/PresenterKandidat.php';

// Muat komponen modul Lowongan (Model, View, Presenter).
require_once __DIR__ . '/models/TabelLowongan.php';
require_once __DIR__ . '/views/ViewLowongan.php';
require_once __DIR__ . '/presenters/PresenterLowongan.php';

// Konfigurasi koneksi database (sesuaikan dengan MySQL Laragon/XAMPP).
const DB_HOST = 'localhost';
const DB_NAME = 'mvp_kandidat_db';
const DB_USER = 'root';
const DB_PASS = '';

/** Ambil nilai string dari array input (default '' jika tidak ada / bukan string). */
function inputStr(array $source, string $key): string {
    return isset($source[$key]) && is_string($source[$key]) ? $source[$key] : '';
}

/** Ambil nilai integer dari array input (default 0 jika kosong / bukan angka). */
function inputInt(array $source, string $key): int {
    $value = filter_var(inputStr($source, $key), FILTER_VALIDATE_INT);
    return $value === false ? 0 : $value;
}

// Seluruh proses dibungkus try/catch agar error (mis. database belum di-import)
// ditampilkan sebagai halaman error sederhana.
try {
    // Tentukan modul aktif (POST atau GET); nilai selain "lowongan" dianggap "kandidat".
    $module = $_POST['module'] ?? $_GET['module'] ?? 'kandidat';
    if ($module !== 'lowongan') {
        $module = 'kandidat';
    }

    if ($module === 'lowongan') {
        // ---------- Modul LOWONGAN ----------
        // Rakit komponen MVP modul Lowongan: Model -> View -> Presenter.
        $model = new TabelLowongan(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $view = new ViewLowongan();
        $presenter = new PresenterLowongan($model, $view);

        // Request POST: jalankan aksi tambah / ubah / hapus lalu redirect ke daftar.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = inputStr($_POST, 'action');
            if ($action === 'tambah') {
                $presenter->tambahLowongan(
                    inputStr($_POST, 'judul_posisi'),
                    inputStr($_POST, 'departemen'),
                    inputStr($_POST, 'lokasi'),
                    inputInt($_POST, 'kuota'),
                    inputStr($_POST, 'status')
                );
            } elseif ($action === 'ubah') {
                $presenter->ubahLowongan(
                    inputInt($_POST, 'id'),
                    inputStr($_POST, 'judul_posisi'),
                    inputStr($_POST, 'departemen'),
                    inputStr($_POST, 'lokasi'),
                    inputInt($_POST, 'kuota'),
                    inputStr($_POST, 'status')
                );
            } elseif ($action === 'hapus') {
                $presenter->hapusLowongan(inputInt($_POST, 'id'));
            }
            header("Location: index.php?module=lowongan");
            exit();
        }

        // Request GET: pilih tampilan (daftar, form tambah, atau form ubah).
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormLowongan();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormLowongan((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanLowongan();
        }
    } else {
        // ---------- Modul KANDIDAT (default) ----------
        // Rakit komponen MVP modul Kandidat: Model -> View -> Presenter.
        $model = new TabelKandidat(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $view = new ViewKandidat();
        $presenter = new PresenterKandidat($model, $view);

        // Request POST: jalankan aksi tambah / ubah / hapus lalu redirect ke daftar.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = inputStr($_POST, 'action');
            if ($action === 'tambah') {
                $presenter->tambahKandidat(
                    inputStr($_POST, 'nama'),
                    inputStr($_POST, 'posisi'),
                    inputStr($_POST, 'domisili'),
                    inputInt($_POST, 'skorTes'),
                    inputInt($_POST, 'pengalamanTahun')
                );
            } elseif ($action === 'ubah') {
                $presenter->ubahKandidat(
                    inputInt($_POST, 'id'),
                    inputStr($_POST, 'nama'),
                    inputStr($_POST, 'posisi'),
                    inputStr($_POST, 'domisili'),
                    inputInt($_POST, 'skorTes'),
                    inputInt($_POST, 'pengalamanTahun')
                );
            } elseif ($action === 'hapus') {
                $presenter->hapusKandidat(inputInt($_POST, 'id'));
            }
            header("Location: index.php");
            exit();
        }

        // Request GET: pilih tampilan (daftar, form tambah, atau form ubah).
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormKandidat();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormKandidat((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanKandidat();
        }
    }
// Terjadi error: tampilkan halaman error sederhana (pesan di-escape).
} catch (Throwable $e) {
    // Halaman error sederhana (cocok untuk lingkungan praktikum/lokal).
    // Di produksi, jangan tampilkan detail pesan; cukup catat ke log.
    http_response_code(500);
    echo "<!doctype html><html lang='id'><head><meta charset='utf-8'><title>Terjadi Kesalahan</title></head>"
        . "<body style='font-family:system-ui;max-width:640px;margin:48px auto;'>"
        . "<h2>Terjadi kesalahan</h2>"
        . "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>"
        . "<p style='color:#6b7280;font-size:14px'>Pastikan MySQL menyala dan <code>mvp_db.sql</code> sudah di-import.</p>"
        . "</body></html>";
}
?>
