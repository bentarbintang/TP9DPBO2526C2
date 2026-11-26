<?php
/**
 * Kontrak View (Strict Passive View)
 * View tidak mengenal entitas domain, tidak melakukan loop, dan tidak menghitung agregat.
 *
 * $alertHtml  : potongan HTML pesan (sukses/error) yang sudah dirakit & di-escape oleh Presenter.
 * $formData   : array asosiatif berisi STRING/angka sederhana (bukan objek Entity).
 *               Kunci opsional 'alertHtml' berisi potongan HTML alert dari Presenter.
 */
interface KontrakView {
    /** Merender halaman daftar: mengganti placeholder baris tabel, total data, dan alert pada template. */
    public function renderDaftar(string $tableRowsHtml, int $totalData, string $alertHtml = ''): string;
    /** Merender halaman form (tambah/ubah) dari template dengan nilai pada $formData. */
    public function renderForm(string $title, string $action, array $formData = []): string;
}
?>
