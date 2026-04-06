-- Migration: Add roles table and relations
-- Date: 2026-04-07

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE,
    description VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed roles
INSERT INTO roles (name, description) VALUES
('admin', 'Akses penuh dan dapat mengubah data'),
('siswa', 'Akses dashboard siswa (read-only)'),
('bk', 'Akses penuh halaman, boleh tambah pelanggaran & cetak laporan BK'),
('pengajar', 'Akses penuh halaman (read-only)')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Add role_id columns
ALTER TABLE guru ADD COLUMN role_id INT NULL;
ALTER TABLE siswa ADD COLUMN role_id INT NULL;

-- Normalize legacy role name "guru" -> "pengajar" for mapping
UPDATE guru SET role = 'pengajar' WHERE role = 'guru';

-- Backfill role_id for existing records
UPDATE guru g
JOIN roles r ON r.name = g.role
SET g.role_id = r.id
WHERE g.role_id IS NULL;

UPDATE siswa s
JOIN roles r ON r.name = 'siswa'
SET s.role_id = r.id
WHERE s.role_id IS NULL;

-- Add foreign keys (optional: ensure no orphan role_id)
ALTER TABLE guru
    ADD CONSTRAINT fk_guru_role
    FOREIGN KEY (role_id) REFERENCES roles(id);

ALTER TABLE siswa
    ADD CONSTRAINT fk_siswa_role
    FOREIGN KEY (role_id) REFERENCES roles(id);

-- Optional hardening (run after verifying all rows have role_id):
-- ALTER TABLE guru MODIFY role_id INT NOT NULL;
-- ALTER TABLE siswa MODIFY role_id INT NOT NULL;
