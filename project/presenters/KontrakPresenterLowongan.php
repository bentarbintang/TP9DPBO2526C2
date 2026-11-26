<?php
/** Kontrak Presenter Lowongan: operasi yang dipanggil index.php (tampilan & aksi CRUD). */
interface KontrakPresenterLowongan {
    /** Menyiapkan data lalu mengembalikan HTML halaman daftar lowongan. */
    public function tampilkanLowongan(): string;
    /** Mengembalikan HTML form; $id null = form tambah, terisi = form ubah (prefill). */
    public function tampilkanFormLowongan(?int $id = null): string;
    /** Memvalidasi lalu menyimpan lowongan baru, kemudian redirect. */
    public function tambahLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    /** Memvalidasi lalu memperbarui lowongan, kemudian redirect. */
    public function ubahLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    /** Menghapus lowongan berdasarkan id, kemudian redirect. */
    public function hapusLowongan(int $id): void;
}
?>
