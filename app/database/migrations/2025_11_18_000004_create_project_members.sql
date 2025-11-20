CREATE TABLE IF NOT EXISTS project_members (
    project_id INT NOT NULL,
    member_id INT NOT NULL,
    role ENUM('member', 'dosen') NOT NULL,    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_member (project_id, member_id)
);