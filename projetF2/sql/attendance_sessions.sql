-- Exercice 5 : Table des sessions de présence

-- Créer la table attendance_sessions
CREATE TABLE attendance_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id VARCHAR(50) NOT NULL,
    group_id VARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    opened_by VARCHAR(100) NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    INDEX idx_course_group (course_id, group_id),
    INDEX idx_date (date),
    INDEX idx_status (status)
);

-- Insérer 2-3 sessions de test
INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES
('AWP', 'G1', '2025-11-26', 'Prof. Benali', 'open'),
('AWP', 'G2', '2025-11-25', 'Prof. Khelifi', 'closed'),
('BD', 'G1', '2025-11-24', 'Prof. Messaoudi', 'open');