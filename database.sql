CREATE TYPE size_type AS ENUM('XL','L','M','S');

CREATE TABLE Scratchers(
  ID SERIAL PRIMARY KEY     NOT NULL,
  item_name VARCHAR(50)     NOT NULL,
  item_description VARCHAR(200) NOT NULL,
  item_size size_type,
  item_cost DECIMAL(6,2) 
);