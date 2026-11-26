<?php
/** Entity Kandidat: representasi satu baris tabel kandidat (hanya menyimpan data). */
class Kandidat {
    private ?int $id;
    private string $nama;
    private string $posisi;
    private string $domisili;
    private int $skorTes;
    private int $pengalamanTahun;

    /**
     * Konstruktor: mengisi semua atribut kandidat.
     * @param int|null $id id kandidat (null untuk data baru)
     */
    public function __construct(
        ?int $id,
        string $nama,
        string $posisi,
        string $domisili,
        int $skorTes,
        int $pengalamanTahun
    ) {
        $this->id = $id;
        $this->nama = $nama;
        $this->posisi = $posisi;
        $this->domisili = $domisili;
        $this->skorTes = $skorTes;
        $this->pengalamanTahun = $pengalamanTahun;
    }

    /** Mengambil nilai atribut Id milik Kandidat. */
    public function getId(): ?int { return $this->id; }
    /** Mengambil nilai atribut Nama milik Kandidat. */
    public function getNama(): string { return $this->nama; }
    /** Mengambil nilai atribut Posisi milik Kandidat. */
    public function getPosisi(): string { return $this->posisi; }
    /** Mengambil nilai atribut Domisili milik Kandidat. */
    public function getDomisili(): string { return $this->domisili; }
    /** Mengambil nilai atribut SkorTes milik Kandidat. */
    public function getSkorTes(): int { return $this->skorTes; }
    /** Mengambil nilai atribut PengalamanTahun milik Kandidat. */
    public function getPengalamanTahun(): int { return $this->pengalamanTahun; }

    /** Mengubah nilai atribut Nama milik Kandidat. */
    public function setNama(string $nama): void { $this->nama = $nama; }
    /** Mengubah nilai atribut Posisi milik Kandidat. */
    public function setPosisi(string $posisi): void { $this->posisi = $posisi; }
    /** Mengubah nilai atribut Domisili milik Kandidat. */
    public function setDomisili(string $domisili): void { $this->domisili = $domisili; }
    /** Mengubah nilai atribut SkorTes milik Kandidat. */
    public function setSkorTes(int $skorTes): void { $this->skorTes = $skorTes; }
    /** Mengubah nilai atribut PengalamanTahun milik Kandidat. */
    public function setPengalamanTahun(int $pengalamanTahun): void { $this->pengalamanTahun = $pengalamanTahun; }
}
?>
