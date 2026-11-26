<?php
/**
 * Kontrak View Lowongan (Strict Passive View)
 * View tidak mengenal entity Lowongan, tidak ada loop, tidak ada hitung-hitungan.
 *
 * $formData berisi nilai sederhana: id, judulPosisi, departemen, lokasi, kuota,
 * serta potongan HTML siap pakai dari Presenter: statusOptionsHtml & alertHtml.
 */
interface KontrakViewLowongan {
    /** Merender halaman daftar: mengganti placeholder baris tabel, total data, dan alert pada template. */
    public function renderDaftar(string $tableRowsHtml, int $totalData, string $alertHtml = ''): string;
    /** Merender halaman form (tambah/ubah) dari template dengan nilai pada $formData. */
    public function renderForm(string $title, string $action, array $formData = []): string;
}
?>
