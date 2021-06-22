
CREATE TABLE utilizador (
	id	 SERIAL,
	nome	 VARCHAR(30) NOT NULL,
	username VARCHAR(20) NOT NULL,
	password VARCHAR(32) NOT NULL,
	PRIMARY KEY(id)
);

INSERT INTO utilizador (nome, username, password) VALUES
('Artem Basok', 'Artem', '123'),
('Kelvin Clark', 'Kelvin', '1234');