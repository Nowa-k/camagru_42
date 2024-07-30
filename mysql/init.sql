CREATE DATABASE IF NOT EXISTS MYSQL_DATABASE;

USE MYSQL_DATABASE;

CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    valide BOOLEAN DEFAULT 0,
    notif BOOLEAN DEFAULT 1
);

CREATE TABLE IF NOT EXISTS feed (
   id INT AUTO_INCREMENT PRIMARY KEY,
   filepath VARCHAR(255) NOT NULL,
   userid VARCHAR(50) NOT NULL, 
   likes INT DEFAULT 0,
   comments INT DEFAULT 0,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comments (
   id INT AUTO_INCREMENT PRIMARY KEY,
   idfile INT NOT NULL,
   comment VARCHAR(255) NOT NULL,
   iduser INT NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS likes (
   id INT AUTO_INCREMENT PRIMARY KEY,
   idfile INT NOT NULL,
   iduser INT NOT NULL
);