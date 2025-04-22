CREATE USER IF NOT EXISTS 'appuser'@'%' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON quizdb.* TO 'appuser'@'%';
CREATE USER 'alexistb2904'@'%' IDENTIFIED BY '2099';
GRANT ALL PRIVILEGES ON quizdb.* TO 'alexistb2904'@'%';
