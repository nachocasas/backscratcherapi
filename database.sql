CREATE DATABASE scatchbling;

USE scatchbling;

CREATE TABLE Scratchers (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
item_name varchar(45) ,
item_description varchar(200) ,
item_size ENUM('XL','L','M','S'),
item_cost DECIMAL(6,2) 
)ENGINE=InnoDB;