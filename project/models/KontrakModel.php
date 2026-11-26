<?php
/** Kontrak Model Kandidat: daftar operasi data yang wajib dimiliki Model. */
interface KontrakModel {
    /**
     * Mengambil seluruh data kandidat sebagai array objek Kandidat.
     * @return Kandidat[]
     */
    public function getAllKandidat(): array;

    /**
     * Mengambil satu data kandidat berdasarkan ID.
     */
    public function getKandidatById(int $id): ?Kandidat;

    /** Menambah kandidat baru ke database (INSERT). */
    public function addKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void;
    /** Mengubah data kandidat berdasarkan id (UPDATE). */
    public function updateKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void;
    /** Menghapus kandidat berdasarkan id (DELETE). */
    public function deleteKandidat(int $id): void;
}
?>
