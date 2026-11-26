<?php
require_once __DIR__ . '/KontrakView.php';

/**
 * View pasif untuk Kandidat: hanya membaca file template HTML lalu mengganti placeholder.
 * Tidak ada loop, tidak ada count(), dan tidak mengenal Model/Entity.
 */
class ViewKandidat implements KontrakView {
    private string $skinPath;
    private string $formPath;

    /**
     * Konstruktor: menentukan lokasi file template daftar (skin) dan form.
     * @param string $skinPath path template daftar (kosong = default)
     * @param string $formPath path template form (kosong = default)
     */
    public function __construct(string $skinPath = '', string $formPath = '') {
        $this->skinPath = $skinPath ?: __DIR__ . '/../template/skin.html';
        $this->formPath = $formPath ?: __DIR__ . '/../template/form.html';
    }

    /**
     * Membaca template daftar lalu mengganti placeholder alert, baris tabel, dan total data.
     * @param string $tableRowsHtml potongan <tr> siap pakai dari Presenter
     * @param int    $totalData     jumlah data (sudah dihitung Presenter)
     * @param string $alertHtml      potongan HTML alert (boleh kosong)
     * @return string HTML lengkap
     */
    public function renderDaftar(string $tableRowsHtml, int $totalData, string $alertHtml = ''): string {
        $template = file_get_contents($this->skinPath);

        $tokens = [
            '{{ALERT}}'      => $alertHtml,
            '{{TABLE_ROWS}}' => $tableRowsHtml,
            '{{TOTAL_DATA}}' => (string)$totalData,
        ];

        return str_replace(array_keys($tokens), array_values($tokens), $template);
    }

    /**
     * Membaca template form lalu mengganti placeholder dengan nilai pada $formData.
     * Setiap nilai teks di-escape dengan htmlspecialchars; potongan HTML dari Presenter dipakai apa adanya.
     * @param string $title    judul form
     * @param string $action   "tambah" atau "ubah"
     * @param array  $formData nilai sederhana (bukan objek Entity)
     * @return string HTML lengkap
     */
    public function renderForm(string $title, string $action, array $formData = []): string {
        $template = file_get_contents($this->formPath);

        $tokens = [
            '{{ALERT}}'            => (string)($formData['alertHtml'] ?? ''),
            '{{FORM_TITLE}}'       => htmlspecialchars($title),
            '{{ACTION}}'           => htmlspecialchars($action),
            '{{ID}}'               => htmlspecialchars((string)($formData['id'] ?? '')),
            '{{NAMA}}'             => htmlspecialchars((string)($formData['nama'] ?? '')),
            '{{POSISI}}'           => htmlspecialchars((string)($formData['posisi'] ?? '')),
            '{{DOMISILI}}'         => htmlspecialchars((string)($formData['domisili'] ?? '')),
            '{{SKOR_TES}}'         => htmlspecialchars((string)($formData['skorTes'] ?? 0)),
            '{{PENGALAMAN_TAHUN}}' => htmlspecialchars((string)($formData['pengalamanTahun'] ?? 0)),
        ];

        return str_replace(array_keys($tokens), array_values($tokens), $template);
    }
}
?>
