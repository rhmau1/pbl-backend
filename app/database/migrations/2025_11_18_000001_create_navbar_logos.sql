CREATE TABLE navbar_logos (
    id SERIAL PRIMARY KEY,
    logo_url VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_active INT DEFAULT 0
);