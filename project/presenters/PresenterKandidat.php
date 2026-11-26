<?php
require_once __DIR__ . '/KontrakPresenter.php';
require_once __DIR__ . '/Flash.php';
require_once __DIR__ . '/../models/KontrakModel.php';
require_once __DIR__ . '/../views/KontrakView.php';

/**
 * Presenter Kandidat: penghubung Model dan View.
 * Berisi validasi, pemanggilan Model, perakitan baris tabel <tr>, dan redirect.
 * Hanya bergantung pada interface KontrakModel dan KontrakView.
 */
class PresenterKandidat implements KontrakPresenter {
    private KontrakModel $model;
    private KontrakView $view;

    /**
     * Konstruktor: menyimpan Model dan View (diberikan lewat interface).
     * @param KontrakModel $model sumber data kandidat
     * @param KontrakView  $view  perender template
     */
    public function __construct(KontrakModel $model, KontrakView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    /**
     * Helper untuk merakit 1 baris HTML tabel kandidat.
     * Panggil dari dalam tampilkanKandidat() saat melakukan foreach.
     */
    private function buildRowHtml(int $no, Kandidat $kandidat): string {
        $id = (int)$kandidat->getId();
        $nama = htmlspecialchars($kandidat->getNama());
        $posisi = htmlspecialchars($kandidat->getPosisi());
        $domisili = htmlspecialchars($kandidat->getDomisili());
        $skorTes = number_format($kandidat->getSkorTes()) . ' / 100';
        $pengalaman = number_format($kandidat->getPengalamanTahun()) . ' Tahun';

        return "<tr>
            <td class='col-id'>{$no}</td>
            <td><strong>{$nama}</strong></td>
            <td>{$posisi}</td>
            <td>{$domisili}</td>
            <td>{$skorTes}</td>
            <td>{$pengalaman}</td>
            <td class='col-actions'>
                <a href='index.php?screen=edit&id={$id}' class='btn btn-edit'>Edit</a>
                <form method='post' action='index.php' style='display:inline;' onsubmit=\"return confirm('Hapus kandidat ini?')\">
                    <input type='hidden' name='action' value='hapus'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn btn-delete'>Hapus</button>
                </form>
            </td>
        </tr>";
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

    /**
     * Validasi input dasar. Mengembalikan daftar pesan error (kosong = valid).
     * @return string[]
     */
    private function validasi(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): array {
        $errors = [];
        if ($nama === '' || $this->panjang($nama) > 100) {
            $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
        }
        if ($posisi === '' || $this->panjang($posisi) > 100) {
            $errors[] = 'Posisi dilamar wajib diisi (maksimal 100 karakter).';
        }
        if ($domisili === '' || $this->panjang($domisili) > 100) {
            $errors[] = 'Domisili wajib diisi (maksimal 100 karakter).';
        }
        if ($skorTes < 0 || $skorTes > 100) {
            $errors[] = 'Skor tes harus di antara 0 sampai 100.';
        }
        if ($pengalamanTahun < 0 || $pengalamanTahun > 60) {
            $errors[] = 'Pengalaman kerja harus di antara 0 sampai 60 tahun.';
        }
        return $errors;
    }

    /**
     * Menampilkan daftar kandidat.
     * Alur: ambil data dari Model -> hitung total -> rakit baris <tr> -> ambil flash -> View::renderDaftar().
     * @return string HTML halaman daftar
     */
    public function tampilkanKandidat(): string {
        $listKandidat = $this->model->getAllKandidat();
        $totalKandidat = count($listKandidat);

        $rowsHtml = '';
        if (empty($listKandidat)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center;color:#6b7280;padding:24px;">Belum ada data kandidat.</td></tr>';
        } else {
            $no = 1;
            foreach ($listKandidat as $kandidat) {
                $rowsHtml .= $this->buildRowHtml($no, $kandidat);
                $no++;
            }
        }

        $alertHtml = Flash::toHtml(Flash::pull());
        return $this->view->renderDaftar($rowsHtml, $totalKandidat, $alertHtml);
    }

    /**
     * Menampilkan form tambah (tanpa $id) atau form ubah (dengan $id, data terisi).
     * Jika validasi sebelumnya gagal, input lama dari flash dipakai untuk mengisi ulang form.
     * Jika $id tidak ditemukan: flash error lalu redirect ke daftar.
     * @return string HTML halaman form
     */
    public function tampilkanFormKandidat(?int $id = null): string {
        $alertHtml = Flash::toHtml(Flash::pull());
        $old = Flash::pullOld('kandidat'); // input sebelumnya jika validasi gagal

        if ($id !== null) {
            $kandidat = $this->model->getKandidatById($id);
            if ($kandidat === null) {
                Flash::set('error', 'Data kandidat tidak ditemukan.');
                $this->redirect('index.php');
            }

            $formData = [
                'id'              => $kandidat->getId(),
                'nama'            => $kandidat->getNama(),
                'posisi'          => $kandidat->getPosisi(),
                'domisili'        => $kandidat->getDomisili(),
                'skorTes'         => $kandidat->getSkorTes(),
                'pengalamanTahun' => $kandidat->getPengalamanTahun(),
            ];
            if ($old !== null) {
                $formData = array_merge($formData, $old);
            }
            $formData['alertHtml'] = $alertHtml;
            return $this->view->renderForm("Ubah Data Kandidat", "ubah", $formData);
        }

        $formData = $old ?? [];
        $formData['alertHtml'] = $alertHtml;
        return $this->view->renderForm("Tambah Kandidat Baru", "tambah", $formData);
    }

    /**
     * Menambah kandidat: trim -> validasi -> Model::addKandidat() -> flash -> redirect.
     * Bila validasi gagal, simpan pesan error + input lama lalu kembali ke form tambah.
     */
    public function tambahKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $nama = trim($nama);
        $posisi = trim($posisi);
        $domisili = trim($domisili);

        $errors = $this->validasi($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        if (!empty($errors)) {
            Flash::set('error', implode("\n", $errors));
            Flash::setOld('kandidat', [
                'nama' => $nama, 'posisi' => $posisi, 'domisili' => $domisili,
                'skorTes' => $skorTes, 'pengalamanTahun' => $pengalamanTahun,
            ]);
            $this->redirect('index.php?screen=add');
        }

        try {
            $this->model->addKandidat($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
            Flash::set('success', "Kandidat \"{$nama}\" berhasil ditambahkan.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal menyimpan data ke database.');
        }
        $this->redirect('index.php');
    }

    /**
     * Mengubah kandidat: cek id ada -> trim -> validasi -> Model::updateKandidat() -> flash -> redirect.
     * Bila validasi gagal, kembali ke form ubah dengan pesan error dan input lama.
     */
    public function ubahKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $nama = trim($nama);
        $posisi = trim($posisi);
        $domisili = trim($domisili);

        if ($id <= 0 || $this->model->getKandidatById($id) === null) {
            Flash::set('error', 'Data kandidat tidak ditemukan.');
            $this->redirect('index.php');
        }

        $errors = $this->validasi($nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
        if (!empty($errors)) {
            Flash::set('error', implode("\n", $errors));
            Flash::setOld('kandidat', [
                'nama' => $nama, 'posisi' => $posisi, 'domisili' => $domisili,
                'skorTes' => $skorTes, 'pengalamanTahun' => $pengalamanTahun,
            ]);
            $this->redirect('index.php?screen=edit&id=' . $id);
        }

        try {
            $this->model->updateKandidat($id, $nama, $posisi, $domisili, $skorTes, $pengalamanTahun);
            Flash::set('success', "Data kandidat \"{$nama}\" berhasil diperbarui.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal memperbarui data di database.');
        }
        $this->redirect('index.php');
    }

    /**
     * Menghapus kandidat: cek id ada -> Model::deleteKandidat() -> flash -> redirect.
     * Bila data tidak ada atau gagal, tampilkan pesan error melalui flash.
     */
    public function hapusKandidat(int $id): void {
        $kandidat = $id > 0 ? $this->model->getKandidatById($id) : null;
        if ($kandidat === null) {
            Flash::set('error', 'Data kandidat tidak ditemukan.');
            $this->redirect('index.php');
        }

        try {
            $this->model->deleteKandidat($id);
            Flash::set('success', "Kandidat \"{$kandidat->getNama()}\" berhasil dihapus.");
        } catch (RuntimeException $e) {
            Flash::set('error', 'Gagal menghapus data dari database.');
        }
        $this->redirect('index.php');
    }
}
?>
