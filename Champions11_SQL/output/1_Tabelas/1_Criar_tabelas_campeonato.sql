CREATE TABLE equipa (
	id	SERIAL,
	nome		     VARCHAR(30) UNIQUE NOT NULL,
	emblema_link	 CHAR(255) UNIQUE,
	equipamento_link CHAR(255) UNIQUE,
	formacao_link	 CHAR(255) UNIQUE,
	PRIMARY KEY(id)
);

CREATE TABLE temporada (
	id	SERIAL,
	ano SMALLINT NOT NULL DEFAULT 2020,
	PRIMARY KEY(id)
);

CREATE TABLE jogo (
	id	SERIAL,
	n_jornada	 SMALLINT NOT NULL,
	n_jogo	 	 SMALLINT NOT NULL,
	data		 TIMESTAMP NOT NULL,
	terminado	 BOOL NOT NULL,
	equipa_casa	 INTEGER NOT NULL,
	equipa_fora	 INTEGER NOT NULL,
	temporada_id	 INTEGER NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE jogador (
	id	 	  SERIAL,
	nome	  VARCHAR(30) NOT NULL,
	posicao	  VARCHAR(3) NOT NULL,
	foto_link CHAR(255) NOT NULL,
	equipa_id INTEGER NOT NULL,
	PRIMARY KEY(id)
);

CREATE TYPE ocorrencia AS ENUM ('golo', 'ca', 'cv');

CREATE TABLE jogo_evento (
	id			SERIAL,
	ocorrencia	ocorrencia,
	minuto		SMALLINT,
	penalti	 	BOOL,
	jogador_id 	INTEGER,
	jogo_id	 	INTEGER NOT NULL,
	equipa_id	INTEGER,
	PRIMARY KEY(id)
);

CREATE TABLE status_equipa (
	id			SERIAL,
	n_jogos	 	 SMALLINT NOT NULL DEFAULT 0,
	pontos		 SMALLINT NOT NULL DEFAULT 0,
	ranking	 	 SMALLINT NOT NULL DEFAULT 0,
	vitorias	 SMALLINT NOT NULL DEFAULT 0,
	derrotas	 SMALLINT NOT NULL DEFAULT 0,
	empates	 	 SMALLINT NOT NULL DEFAULT 0,
	golos_marcados	 SMALLINT NOT NULL DEFAULT 0,
	golos_sofridos	 SMALLINT NOT NULL DEFAULT 0,
	equipa_id	 INTEGER NOT NULL,
	temporada_id	 INTEGER NOT NULL,
	PRIMARY KEY(id)
);

ALTER TABLE jogo ADD CONSTRAINT jogo_fk1 FOREIGN KEY (equipa_casa) REFERENCES equipa(id);
ALTER TABLE jogo ADD CONSTRAINT jogo_fk2 FOREIGN KEY (equipa_fora) REFERENCES equipa(id);
ALTER TABLE jogo ADD CONSTRAINT jogo_fk3 FOREIGN KEY (temporada_id) REFERENCES temporada(id);

ALTER TABLE jogador ADD CONSTRAINT jogador_fk1 FOREIGN KEY (equipa_id) REFERENCES equipa(id);

ALTER TABLE jogo_evento ADD CONSTRAINT jogo_evento_fk1 FOREIGN KEY (jogador_id) REFERENCES jogador(id);
ALTER TABLE jogo_evento ADD CONSTRAINT jogo_evento_fk2 FOREIGN KEY (jogo_id) REFERENCES jogo(id);
ALTER TABLE jogo_evento ADD CONSTRAINT jogo_evento_fk3 FOREIGN KEY (equipa_id) REFERENCES equipa(id);

ALTER TABLE status_equipa ADD CONSTRAINT status_equipa_fk1 FOREIGN KEY (equipa_id) REFERENCES equipa(id);
ALTER TABLE status_equipa ADD CONSTRAINT status_equipa_fk2 FOREIGN KEY (temporada_id) REFERENCES temporada(id);


