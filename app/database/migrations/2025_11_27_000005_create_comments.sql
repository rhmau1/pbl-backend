CREATE TABLE IF NOT EXISTS comments (
  id SERIAL PRIMARY KEY,
  entity_type VARCHAR(255) NOT NULL,
  entity_id INT NOT NULL,
  author INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  email VARCHAR(255) NOT NULL,
  rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  content TEXT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- Index for filtering comments by entity
CREATE INDEX idx_comments_entity ON comments(entity_type, entity_id);

-- Index for author (user id)
CREATE INDEX idx_comments_author ON comments(author);

-- Index for status to quickly filter pending/approved
CREATE INDEX idx_comments_status ON comments(status);