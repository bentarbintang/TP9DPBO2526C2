<?php
require_once __DIR__ . '/KontrakPresenterLowongan.php';
require_once __DIR__ . '/Flash.php';
require_once __DIR__ . '/../models/KontrakModelLowongan.php';
require_once __DIR__ . '/../views/KontrakViewLowongan.php';

/**
 * Presenter Lowongan: penghubung Model dan View untuk modul lowongan.
 * Berisi validasi, pemanggilan Model, perakitan baris tabel <tr> dan <option> status, serta redirect.
 */
class PresenterLowongan implements KontrakPresenterLowongan {
    /** Daftar status yang valid (whitelist) beserta kelas CSS badge-nya. */
    private const STATUS_BADGE = [
        'Dibuka'  => 'badge-open',
        'Ditutup' => 'badge-closed',
        'Ditunda' => 'badge-hold',
    ];
    private const STATUS_DEFAULT = 'Dibuka';

    private KontrakModelLowongan $model;
    private KontrakViewLowongan $view;

    /**
     * Konstruktor: menyimpan Model dan View (diberikan lewat interface).
     * @param KontrakModelLowongan $model sumber data lowongan
     * @param KontrakViewLowongan  $view  perender template
     */
    public function __construct(KontrakModelLowongan $model, KontrakViewLowongan $view) {
        $this->model = $model;
        $this->view = $view;
    }

    /** Helper merakit 1 baris HTML tabel lowongan (semua output di-escape). */
    private function buildRowHtml(int $no, Lowongan $lowongan): string {
        $id = (int)$lowongan->getId();
        $judul = htmlspecialchars($lowongan->getJudulPosisi(), ENT_QUOTES, 'UTF-8');
        $departemen = htmlspecialchars($lowongan->getDepartemen(), ENT_QUOTES, 'UTF-8');
        $lokasi = htmlspecialchars($lowongan->getLokasi(), ENT_QUOTES, 'UTF-8');
        $kuota = number_format($lowongan->getKuota()) . ' Orang';
        $status = htmlspecialchars($lowongan->getStatus(), ENT_QUOTES, 'UTF-8');
        $badgeClass = self::STATUS_BADGE[$lowongan->getStatus()] ?? 'badge-hold';

        return "<tr>
            <td class='col-id'>{$no}</td>
            <td><strong>{$judul}</strong></td>
            <td>{$departemen}</td>
            <td>{$lokasi}</td>
            <td>{$kuota}</td>
            <td><span class='badge {$badgeClass}'>{$status}</span></td>
            <td class='col-actions'>
                <a href='index.php?module=lowongan&screen=edit&id={$id}' class='btn btn-edit'>Edit</a>
                <form method='post' action='index.php' style='display:inline;' onsubmit=\"return confirm('Hapus lowongan ini?')\">
                    <input type='hidden' name='module' value='lowongan'>
                    <input type='hidden' name='action' value='hapus'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn btn-delete'>Hapus</button>
                </form>
            </td>
        </tr>";
    }

    /** Merakit <option> status; $terpilih ditandai selected. */
    private function buildStatusOptionsHtml(string $terpilih): string {
        $html = '';
        foreach (array_keys(self::STATUS_BADGE) as $status) {
            $selected = ($status === $terpilih) ? ' selected' : '';
            $nilai = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
            $html .= "<option value='{$nilai}'{$selected}>{$nilai}</option>";
        }
        return $html;
    }

    /** Panjang string (aman untuk UTF-8; fallback ke strlen jika mbstring tidak aktif). */
    private function panjang(string $teks): int {
        return function_exists('mb_strlen') ? mb_strlen($teks, 'UTF-8') : strlen($teks);
    }

    /** Mengarahkan browser ke $url lalu menghentikan eksekusi (pola Post-Redirect-Get). */
    private function redirect(string $url): void {
        header("Location: {$url}");
        exit();
    }

    /** @return string[] daftar pesan error (kosong = valid) */
    private function validasi(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): array {
        $errors = [];
        if ($judulPosisi === '' || $this->panjang($judulPosisi) > 100) {
            $errors[] = 'Judul posisi wajib diisi (maksimal 100 karakter).';
        }
        if ($departemen === '' || $this->panjang($departemen) > 100) {
            $errors[] = 'Departemen wajib diisi (maksimal 100 karakter).';
        }
        if ($lokasi === '' || $this->panjang($lokasi) > 100) {
            $errors[] = 'Lokasi wajib diisi (maksimal 100 karakter).';
        }
        if ($kuota < 1 || $kuota > 1000) {
            $errors[] = 'Kuota harus di antara 1 sampai 1000 orang.';
        }
        if (!array_key_exists($status, self::STATUS_BADGE)) {
            $errors[] = 'Status lowongan tidak valid.';
        }
        return $errors;
    }

    /**
     * Menampilkan daftar lowongan.
     * Alur: ambil data dari Model -> hitung total -> rakit baris <tr> -> ambil flash -> View::renderDaftar().
     * @return string HTML halaman daftar
     */
    public function tampilkanLowongan(): string {
        $listLowongan = $this->model->getAllLowongan();
        $totalLowongan = count($listLowongan);

        $rowsHtml = '';
        if (empty($listLowongan)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center;color:#6b7280;padding:24px;">Belum ada data lowongan.</td></tr>';
        } else {
            $no = 1;
            foreach ($listLowongan as $lowongan) {
                $rowsHtml .= $this->buildRowHtml($no, $lowongan);
                $no++;
            }
        }

        $alertHtml = Flash::toHtml(Flash::pull());
        return $this->view->renderDaftar($rowsHtml, $totalLowongan, $alertHtml);
    }

    /**
     * Menampilkan form tambah (tanpa $id) atau form ubah (dengan $id, data terisi).
     * Daftar <option> status dirakit di sini agar View tetap pasif.
     * Jika validasi sebelumnya gagal, input lama dari flash dipakai untuk mengisi ulang form.
     * @return string HTML halaman form
     */
    public function tampilkanFormLowongan(?int $id = null): string {
        $alertHtml = Flash::toHtml(Flash::pull());
        $old = Flash::pullOld('lowongan');

        if ($id !== null) {
            $lowongan = $this->model->getLowonganById($id);
            if ($lowongan === null) {
                Flash::set('error', 'Data lowongan tidak ditemukan.');
                $this->redirect('index.php?module=lowongan');
            }

            $formData = [
                'id'          => $lowongan->getId(),
                'judulPosisi' => $lowongan->getJudulPosisi(),
                'departemen'  => $lowongan->getDepartemen(),
                'lokasi'      => $lowongan->getLokasi(),
                'kuota'       => $lowongan->getKuota(),
                'status'      => $lowongan->getStatus(),
            ];
            if ($old !== null) {
                $formData = array_merge($formData, $old);
            }
            $formData['statusOptionsHtml'] = $this->buildStatusOptionsHtml((string)$formData['status']);
            $formData['alertHtml'] = $alertHtml;
            return $this->view->renderForm("Ubah Data Lowongan", "ubah", $formData);
        }

        $formData = $old ?? ['kuota' => 1, 'status' => self::STATUS_DEFAULT];
        $formData['statusOptionsHtml'] = $this->buildStatusOptionsHtml((string)($formData['status'] ?? self::STATUS_DEFAULT));
        $formData['alertHtml'] = $alertHtml;
        return $this->view->renderForm("Tambah Lowongan Baru", "tambah", $formData);
    }

    /**
     * Menambah lowongan: trim -> validasi -> Model::addLowongan() -> flash -> redirect.
     * Bila validasi gagal, simpan pesan error + input lama lalu kembali ke form tambah.
     */
    public function tambahLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $judulPosisi = trim($judulPosisi);
        $departemen = trim($departemen);
        $lokasi = trim($lokasi);
        $status = trim($status);

        $errors = $this->validasi($judulPosisi, $departemen, $lokasi, $kuota, $status);
        if (!empty($errors)) {
            Flash::set('error', implode("\n", $errors));
            Flash::setOld('lowongan', [
                'judulPosisi' => $judulPosisi, 'departemen' => $departemen, 'lokasi' => $lokasi,
                'kuota' => $kuota, 'status' => array_key_exists($status, self::STATUS_BADGE) ? $status : self::STATUS_DEFAULT,
            ]);
            $this->redirect('index.php?module=lowongan&screen=add');
        }

        try {
            $this->model->addLowongan($judulPosisi, $departemen, $lokasi, $kuota, $status);
            Flash::set('success', "Lowongan \"{$judulPosisi}\" berhasil ditambahkan.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal menyimpan data ke database.');
        }
        $this->redirect('index.php?module=lowongan');
    }

    /**
     * Mengubah lowongan: cek id ada -> trim -> validasi -> Model::updateLowongan() -> flash -> redirect.
     * Bila validasi gagal, kembali ke form ubah dengan pesan error dan input lama.
     */
    public function ubahLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $judulPosisi = trim($judulPosisi);
        $departemen = trim($departemen);
        $lokasi = trim($lokasi);
        $status = trim($status);

        if ($id <= 0 || $this->model->getLowonganById($id) === null) {
            Flash::set('error', 'Data lowongan tidak ditemukan.');
            $this->redirect('index.php?module=lowongan');
        }

        $errors = $this->validasi($judulPosisi, $departemen, $lokasi, $kuota, $status);
        if (!empty($errors)) {
            Flash::set('error', implode("\n", $errors));
            Flash::setOld('lowongan', [
                'judulPosisi' => $judulPosisi, 'departemen' => $departemen, 'lokasi' => $lokasi,
                'kuota' => $kuota, 'status' => array_key_exists($status, self::STATUS_BADGE) ? $status : self::STATUS_DEFAULT,
            ]);
            $this->redirect('index.php?module=lowongan&screen=edit&id=' . $id);
        }

        try {
            $this->model->updateLowongan($id, $judulPosisi, $departemen, $lokasi, $kuota, $status);
            Flash::set('success', "Lowongan \"{$judulPosisi}\" berhasil diperbarui.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal memperbarui data di database.');
        }
        $this->redirect('index.php?module=lowongan');
    }

    /**
     * Menghapus lowongan: cek id ada -> Model::deleteLowongan() -> flash -> redirect.
     * Bila data tidak ada atau gagal, tampilkan pesan error melalui flash.
     */
    public function hapusLowongan(int $id): void {
        $lowongan = $id > 0 ? $this->model->getLowonganById($id) : null;
        if ($lowongan === null) {
            Flash::set('error', 'Data lowongan tidak ditemukan.');
            $this->redirect('index.php?module=lowongan');
        }

        try {
            $this->model->deleteLowongan($id);
            Flash::set('success', "Lowongan \"{$lowongan->getJudulPosisi()}\" berhasil dihapus.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal menghapus data dari database.');
        }
        $this->redirect('index.php?module=lowongan');
    }
}
?>
