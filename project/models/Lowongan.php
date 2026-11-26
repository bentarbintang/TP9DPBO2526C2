<?php
/** Entity Lowongan: representasi satu baris tabel lowongan (hanya menyimpan data). */
class Lowongan {
    private ?int $id;
    private string $judulPosisi;
    private string $departemen;
    private string $lokasi;
    private int $kuota;
    private string $status;

    /**
     * Konstruktor: mengisi semua atribut lowongan.
     * @param int|null $id id lowongan (null untuk data baru)
     */
    public function __construct(
        ?int $id,
        string $judulPosisi,
        string $departemen,
        string $lokasi,
        int $kuota,
        string $status
    ) {
        $this->id = $id;
        $this->judulPosisi = $judulPosisi;
        $this->departemen = $departemen;
        $this->lokasi = $lokasi;
        $this->kuota = $kuota;
        $this->status = $status;
    }

    /** Mengambil nilai atribut Id milik Lowongan. */
    public function getId(): ?int { return $this->id; }
    /** Mengambil nilai atribut JudulPosisi milik Lowongan. */
    public function getJudulPosisi(): string { return $this->judulPosisi; }
    /** Mengambil nilai atribut Departemen milik Lowongan. */
    public function getDepartemen(): string { return $this->departemen; }
    /** Mengambil nilai atribut Lokasi milik Lowongan. */
    public function getLokasi(): string { return $this->lokasi; }
    /** Mengambil nilai atribut Kuota milik Lowongan. */
    public function getKuota(): int { return $this->kuota; }
    /** Mengambil nilai atribut Status milik Lowongan. */
    public function getStatus(): string { return $this->status; }

    /** Mengubah nilai atribut JudulPosisi milik Lowongan. */
    public function setJudulPosisi(string $judulPosisi): void { $this->judulPosisi = $judulPosisi; }
    /** Mengubah nilai atribut Departemen milik Lowongan. */
    public function setDepartemen(string $departemen): void { $this->departemen = $departemen; }
    /** Mengubah nilai atribut Lokasi milik Lowongan. */
    public function setLokasi(string $lokasi): void { $this->lokasi = $lokasi; }
    /** Mengubah nilai atribut Kuota milik Lowongan. */
    public function setKuota(int $kuota): void { $this->kuota = $kuota; }
    /** Mengubah nilai atribut Status milik Lowongan. */
    public function setStatus(string $status): void { $this->status = $status; }
}
?>
