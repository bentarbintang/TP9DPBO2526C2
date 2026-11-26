<?php
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/KontrakModelLowongan.php';
require_once __DIR__ . '/Lowongan.php';

/** Model Lowongan: mengakses tabel lowongan lewat DB (PDO) dan mengikuti KontrakModelLowongan. */
class TabelLowongan extends DB implements KontrakModelLowongan {

    /** Konstruktor: meneruskan kredensial database ke kelas induk DB. */
    public function __construct(
        string $host = "localhost",
        string $db_name = "mvp_kandidat_db",
        string $username = "root",
        string $password = ""
    ) {
        parent::__construct($host, $db_name, $username, $password);
    }

    /** Helper: mengubah satu baris hasil query (array) menjadi objek Lowongan. */
    private function mapRowToLowongan(array $row): Lowongan {
        return new Lowongan(
            (int)$row['id'],
            $row['judul_posisi'],
            $row['departemen'],
            $row['lokasi'],
            (int)$row['kuota'],
            $row['status']
        );
    }

    /** Mengambil semua lowongan, diurutkan berdasarkan id. */
    public function getAllLowongan(): array {
        $query = "SELECT * FROM lowongan ORDER BY id ASC";
        $this->executeQuery($query);
        $rows = $this->getAllResult();

        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->mapRowToLowongan($row);
        }
        return $list;
    }

    /** Mengambil satu lowongan berdasarkan id; mengembalikan null jika tidak ditemukan. */
    public function getLowonganById(int $id): ?Lowongan {
        $query = "SELECT * FROM lowongan WHERE id = :id LIMIT 1";
        $this->executeQuery($query, [':id' => $id]);
        $rows = $this->getAllResult();
        if (empty($rows)) {
            return null;
        }
        return $this->mapRowToLowongan($rows[0]);
    }

    /** INSERT lowongan baru memakai prepared statement. */
    public function addLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $query = "INSERT INTO lowongan (judul_posisi, departemen, lokasi, kuota, status)
                  VALUES (:judul_posisi, :departemen, :lokasi, :kuota, :status)";
        $this->executeQuery($query, [
            ':judul_posisi' => $judulPosisi,
            ':departemen'   => $departemen,
            ':lokasi'       => $lokasi,
            ':kuota'        => $kuota,
            ':status'       => $status,
        ]);
    }

    /** UPDATE data lowongan berdasarkan id memakai prepared statement. */
    public function updateLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $query = "UPDATE lowongan
                  SET judul_posisi = :judul_posisi, departemen = :departemen, lokasi = :lokasi,
                      kuota = :kuota, status = :status
                  WHERE id = :id";
        $this->executeQuery($query, [
            ':judul_posisi' => $judulPosisi,
            ':departemen'   => $departemen,
            ':lokasi'       => $lokasi,
            ':kuota'        => $kuota,
            ':status'       => $status,
            ':id'           => $id,
        ]);
    }

    /** DELETE lowongan berdasarkan id memakai prepared statement. */
    public function deleteLowongan(int $id): void {
        $query = "DELETE FROM lowongan WHERE id = :id";
        $this->executeQuery($query, [':id' => $id]);
    }
}
?>
