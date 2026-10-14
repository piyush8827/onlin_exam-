
CREATE DATABASE  online_exam;
USE online_exam;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE result_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option CHAR(1),
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (result_id) REFERENCES results(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

INSERT INTO questions
(question, option_a, option_b, option_c, option_d, correct_option)
VALUES
('What does PHP stand for?',
 'Personal Home Page', 'Private Home Page',
 'PHP: Hypertext Preprocessor', 'Program Home Page', 'C'),

('Which symbol is used to start a PHP variable?',
 '#', '$', '@', '&', 'B'),

('Which language is used to style web pages?',
 'PHP', 'SQL', 'CSS', 'Python', 'C'),

('Which command displays output in PHP?',
 'echo', 'input', 'read', 'scan', 'A'),

('Which database is commonly used with PHP?',
 'MySQL', 'Paint', 'Notepad', 'PowerPoint', 'A'),

('Which function checks an email format in PHP?',
 'check_email()', 'filter_var()', 'email_check()', 'validate()', 'B'),

('Which superglobal receives form data using POST?',
 '$_GET', '$_SERVER', '$_POST', '$_FILES', 'C'),

('Which function securely hashes a password in PHP?',
 'password_hash()', 'encrypt_text()', 'md5_only()', 'secure_pass()', 'A'),

('Which HTML tag creates a hyperlink?',
 '<p>', '<a>', '<h1>', '<img>', 'B'),

('Which SQL command retrieves records?',
 'INSERT', 'UPDATE', 'DELETE', 'SELECT', 'D');
