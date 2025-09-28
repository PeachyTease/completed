This package adds Programs and Stories public pages and admin CRUD.
Note: schema.sql was not modified; create 'programs' and 'stories' tables in your DB to use these features.

Tables expected:
CREATE TABLE programs (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT, image VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE stories (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, content TEXT, image VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

Upload built frontend assets into /assets/ and ensure inc/config.php DB credentials are set.
