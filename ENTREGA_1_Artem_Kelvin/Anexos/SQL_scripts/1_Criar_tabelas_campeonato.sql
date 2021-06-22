CREATE TABLE equipa (
	id		 BIGINT,
	nome		 CHAR(60) UNIQUE NOT NULL,
	n_jogos		 INTEGER NOT NULL,
	ranking		 INTEGER NOT NULL,
	pontos		 INTEGER NOT NULL DEFAULT 0,
	vitorias	 INTEGER NOT NULL DEFAULT 0,
	derrotas	 INTEGER NOT NULL DEFAULT 0,
	empates		 INTEGER NOT NULL,
	gm		 INTEGER NOT NULL,
	gs		 INTEGER NOT NULL,
	dg		 INTEGER NOT NULL,
	mgm		 FLOAT(8) NOT NULL,
	mgs		 FLOAT(8) NOT NULL,
	emblema_link	 CHAR(255) UNIQUE NOT NULL,
	equipamento_link CHAR(255) UNIQUE NOT NULL,
	formacao_link	 CHAR(255) UNIQUE NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE temporada (
	id	 BIGINT,
	ano INTEGER NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE jogo (
	id		 BIGINT,
	n_jornada	 INTEGER NOT NULL,
	data	 TIMESTAMP NOT NULL,
	terminado	 BOOL NOT NULL,
	equipa_casa	 BIGINT NOT NULL,
	equipa_fora	 BIGINT NOT NULL,
	favorito	 CHAR(60),
	temporada_id BIGINT NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE jogador (
	id	 BIGINT,
	nome	 CHAR(60) NOT NULL,
	posicao	 CHAR(5) NOT NULL,
	ca	 INTEGER NOT NULL DEFAULT 0,
	cv	 INTEGER NOT NULL DEFAULT 0,
	foto_link CHAR(255) NOT NULL,
	equipa_id BIGINT NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE golo (
	id	 BIGINT,
	minuto	 INTEGER NOT NULL,
	penalti	 BOOL NOT NULL,
	jogo_id	 BIGINT NOT NULL,
	jogador_id BIGINT NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE temporada_equipa (
	temporada_id BIGINT NOT NULL,
	equipa_id	 BIGINT,
	PRIMARY KEY(equipa_id)
);

ALTER TABLE jogo ADD CONSTRAINT jogo_fk1 FOREIGN KEY (temporada_id) REFERENCES temporada(id);
ALTER TABLE jogo ADD CONSTRAINT jogo_fk2 FOREIGN KEY (equipa_casa) REFERENCES equipa(id);
ALTER TABLE jogo ADD CONSTRAINT jogo_fk3 FOREIGN KEY (equipa_fora) REFERENCES equipa(id);

ALTER TABLE jogador ADD CONSTRAINT jogador_fk1 FOREIGN KEY (equipa_id) REFERENCES equipa(id);
ALTER TABLE golo ADD CONSTRAINT golo_fk1 FOREIGN KEY (jogo_id) REFERENCES jogo(id);
ALTER TABLE golo ADD CONSTRAINT golo_fk2 FOREIGN KEY (jogador_id) REFERENCES jogador(id);
ALTER TABLE temporada_equipa ADD CONSTRAINT temporada_equipa_fk1 FOREIGN KEY (temporada_id) REFERENCES temporada(id);
ALTER TABLE temporada_equipa ADD CONSTRAINT temporada_equipa_fk2 FOREIGN KEY (equipa_id) REFERENCES equipa(id);






