<?php
/** Kontrak Presenter Kandidat: operasi yang dipanggil index.php (tampilan & aksi CRUD). */
interface KontrakPresenter {
    /** Menyiapkan data lalu mengembalikan HTML halaman daftar kandidat. */
    public function tampilkanKandidat(): string;
    /** Mengembalikan HTML form; $id null = form tambah, terisi = form ubah (prefill). */
    public function tampilkanFormKandidat(?int $id = null): string;
    /** Memvalidasi lalu menyimpan kandidat baru, kemudian redirect. */
    public function tambahKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void;
    /** Memvalidasi lalu memperbarui kandidat, kemudian redirect. */
    public function ubahKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void;
    /** Menghapus kandidat berdasarkan id, kemudian redirect. */
    public function hapusKandidat(int $id): void;
}
?>
