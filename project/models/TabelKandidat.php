<?php
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/KontrakModel.php';
require_once __DIR__ . '/Kandidat.php';

/** Model Kandidat: mengakses tabel kandidat lewat DB (PDO) dan mengikuti KontrakModel. */
class TabelKandidat extends DB implements KontrakModel {

    /** Konstruktor: meneruskan kredensial database ke kelas induk DB. */
    public function __construct(
        string $host = "localhost",
        string $db_name = "mvp_kandidat_db",
        string $username = "root",
        string $password = ""
    ) {
        parent::__construct($host, $db_name, $username, $password);
    }

    /** Helper: mengubah satu baris hasil query (array) menjadi objek Kandidat. */
    private function mapRowToKandidat(array $row): Kandidat {
        return new Kandidat(
            (int)$row['id'],
            $row['nama'],
            $row['posisi'],
            $row['domisili'],
            (int)$row['skorTes'],
            (int)$row['pengalamanTahun']
        );
    }

    /** Mengambil semua kandidat, diurutkan skorTes tertinggi lalu id terkecil. */
    public function getAllKandidat(): array {
        $query = "SELECT * FROM kandidat ORDER BY skorTes DESC, id ASC";
        $this->executeQuery($query);
        $rows = $this->getAllResult();

        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->mapRowToKandidat($row);
        }
        return $list;
    }

    /** Mengambil satu kandidat berdasarkan id; mengembalikan null jika tidak ditemukan. */
    public function getKandidatById(int $id): ?Kandidat {
        $query = "SELECT * FROM kandidat WHERE id = :id LIMIT 1";
        $this->executeQuery($query, [':id' => $id]);
        $rows = $this->getAllResult();
        if (empty($rows)) {
            return null;
        }
        return $this->mapRowToKandidat($rows[0]);
    }

    /** INSERT kandidat baru memakai prepared statement. */
    public function addKandidat(string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $query = "INSERT INTO kandidat (nama, posisi, domisili, skorTes, pengalamanTahun)
                VALUES (:nama, :posisi, :domisili, :skorTes, :pengalamanTahun)";
        $this->executeQuery($query, [
            ':nama'            => $nama,
            ':posisi'          => $posisi,
            ':domisili'        => $domisili,
            ':skorTes'         => $skorTes,
            ':pengalamanTahun' => $pengalamanTahun,
        ]);
    }

    /** UPDATE data kandidat berdasarkan id memakai prepared statement. */
    public function updateKandidat(int $id, string $nama, string $posisi, string $domisili, int $skorTes, int $pengalamanTahun): void {
        $query = "UPDATE kandidat
                  SET nama = :nama, posisi = :posisi, domisili = :domisili,
                      skorTes = :skorTes, pengalamanTahun = :pengalamanTahun
                  WHERE id = :id";
        $this->executeQuery($query, [
            ':nama'            => $nama,
            ':posisi'          => $posisi,
            ':domisili'        => $domisili,
            ':skorTes'         => $skorTes,
            ':pengalamanTahun' => $pengalamanTahun,
            ':id'              => $id,
        ]);
    }

    /** DELETE kandidat berdasarkan id memakai prepared statement. */
    public function deleteKandidat(int $id): void {
        $query = "DELETE FROM kandidat WHERE id = :id";
        $this->executeQuery($query, [':id' => $id]);
    }
}
?>
