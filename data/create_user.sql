CREATE USER 'wiki-backend-app'@'%' IDENTIFIED BY 'kUUTyU7LssSc';

CREATE DATABASE wiki_backend;
GRANT ALL ON wiki_backend.* TO 'wiki-backend-app'@'%';
