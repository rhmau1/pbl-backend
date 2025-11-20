CREATE TABLE struktur_organisasi (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jabatan VARCHAR(255) NOT NULL,
    keahlian VARCHAR(255) NOT NULL,
    minat_penelitian JSON,
    sosial JSON,
    foto VARCHAR(255),
    parent_id INT,
    position INT,
    FOREIGN KEY (parent_id) REFERENCES struktur_organisasi(id) ON DELETE SET NULL
);