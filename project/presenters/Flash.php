<?php
/**
 * Flash: utilitas kecil untuk menyimpan pesan (sukses/error) dan input lama
 * di session satu kali pakai, supaya bisa dibawa lewat redirect (pola PRG:
 * Post-Redirect-Get). Dipakai oleh Presenter, TIDAK pernah oleh View/Model.
 */
class Flash {
    /** Memastikan session PHP sudah aktif sebelum membaca/menulis $_SESSION. */
    private static function boot(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Menyimpan satu pesan flash. @param string $type "success" atau "error"; @param string $message isi pesan (belum di-escape) */
    public static function set(string $type, string $message): void {
        self::boot();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /** Ambil pesan lalu hapus (sekali tampil). */
    public static function pull(): ?array {
        self::boot();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /** Menyimpan input form sebelumnya per modul (dipakai untuk prefill setelah validasi gagal). */
    public static function setOld(string $module, array $input): void {
        self::boot();
        $_SESSION['old'][$module] = $input;
    }

    /** Ambil input lama (untuk prefill form setelah validasi gagal) lalu hapus. */
    public static function pullOld(string $module): ?array {
        self::boot();
        $old = $_SESSION['old'][$module] ?? null;
        unset($_SESSION['old'][$module]);
        return $old;
    }

    /** Merakit HTML alert dari flash (sudah di-escape). Dipanggil oleh Presenter. */
    public static function toHtml(?array $flash): string {
        if ($flash === null) {
            return '';
        }
        $class = $flash['type'] === 'error' ? 'alert-error' : 'alert-success';
        $msg = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
        $msg = str_replace("\n", '<br>', $msg);
        return "<div class='alert {$class}'>{$msg}</div>";
    }
}
?>
