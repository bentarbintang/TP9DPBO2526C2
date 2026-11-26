<?php
/** Kontrak Model Lowongan: daftar operasi data yang wajib dimiliki Model. */
interface KontrakModelLowongan {
    /**
     * Mengambil seluruh data lowongan sebagai array objek Lowongan.
     * @return Lowongan[]
     */
    public function getAllLowongan(): array;

    /**
     * Mengambil satu data lowongan berdasarkan ID (null jika tidak ada).
     */
    public function getLowonganById(int $id): ?Lowongan;

    /** Menambah lowongan baru ke database (INSERT). */
    public function addLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    /** Mengubah data lowongan berdasarkan id (UPDATE). */
    public function updateLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void;
    /** Menghapus lowongan berdasarkan id (DELETE). */
    public function deleteLowongan(int $id): void;
}
?>
