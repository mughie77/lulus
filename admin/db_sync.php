<?php

function syncDatabase($pdo) {
    $log = "";

    // Define Master Schema
    $schema = [
        'admin' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'username' => 'VARCHAR(50) NOT NULL UNIQUE',
            'password' => 'VARCHAR(255) NOT NULL'
        ],
        'siswa' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'nisn' => 'VARCHAR(20) NOT NULL UNIQUE',
            'nama' => 'VARCHAR(100) NOT NULL',
            'link_skl' => 'TEXT NOT NULL',
            'status' => "ENUM('LULUS', 'TIDAK LULUS') DEFAULT 'LULUS'"
        ],
        'pengaturan' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'nama_sekolah' => "VARCHAR(100) DEFAULT 'SMA Negeri Contoh'",
            'alamat_sekolah' => 'TEXT',
            'logo' => "VARCHAR(255) DEFAULT 'default-logo.png'",
            'tgl_pengumuman' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]
    ];

    foreach ($schema as $table => $columns) {
        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $cols_sql = [];
            foreach ($columns as $name => $def) {
                $cols_sql[] = "$name $def";
            }
            $sql = "CREATE TABLE $table (" . implode(', ', $cols_sql) . ")";
            $pdo->exec($sql);
            $log .= "[+] Tabel '$table' berhasil dibuat.\n";
        } else {
            // Check each column
            $existing_cols = [];
            $stmt = $pdo->query("DESCRIBE $table");
            while ($row = $stmt->fetch()) {
                $existing_cols[] = $row['Field'];
            }

            foreach ($columns as $col_name => $col_def) {
                if (!in_array($col_name, $existing_cols)) {
                    $sql = "ALTER TABLE $table ADD $col_name $col_def";
                    $pdo->exec($sql);
                    $log .= "[+] Kolom '$col_name' berhasil ditambahkan ke tabel '$table'.\n";
                }
            }
        }
    }

    if (empty($log)) {
        $log = "Database sudah sinkron. Tidak ada perubahan yang diperlukan.";
    }

    return $log;
}
?>
