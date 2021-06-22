

/************** HOME (Jogos da jornada X) **************/
--Jornada atual:
select min(n_jornada) from jogo where terminado = false; --buscar jornada atual
select max(n_jornada) from jogo; --buscar total de jornada por temporada

--jogos da jornada atual
SELECT j.n_jogo AS n_jogo, j.data, casa.nome AS casa, 
	j.resultado_casa, j.resultado_fora, fora.nome AS fora,
	casa.emblema_link AS emblema_casa, fora.emblema_link AS emblema_fora
    FROM jogo AS j 
	LEFT JOIN equipa AS casa ON casa.id = j.equipa_casa
	LEFT JOIN equipa AS fora ON fora.id = j.equipa_fora
WHERE j.n_jornada = '1'
ORDER BY j.n_jogo ASC
WHERE j.n_jornada = '1' --variavel para a jornada atual
ORDER BY j.n_jogo ASC


/************** LISTA DE JORNADAS **************/
SELECT DISTINCT j.n_jornada, t.ano
FROM jogo AS j
JOIN temporada as t ON t.id = j.temporada_id
ORDER BY n_jornada ASC



/************** LISTA DE EQUIPAS **************/
SELECT e.nome, e.emblema_link
FROM equipa AS e
ORDER BY e.nome ASC


/************** PERFIL DO CLUBE **************/
SELECT e.nome AS clube, e.emblema_link, e.equipamento_link, e.formacao_link,
	jg.posicao, jg.foto_link, jg.nome
FROM equipa AS e
JOIN jogador as jg ON jg.equipa_id = e.id
WHERE e.nome = 'FC Porto'; --variavel para a equipa selecionada

-- esta query terá de ser corrida num ciclo usando um array?
select count(ev.ocorrencia) as golos from jogo_evento as ev
join jogador as jo on jo.id = ev.jogador_id
where jo.nome = 'Gilberto' AND ev.ocorrencia = 'golo'; --variavel para a ocorrencia selecionada


/************** CLASSIFICAÇÕES **************/
-- falta a temporada

SELECT  s.ranking AS Pos, e.emblema_link, e.nome AS equipa, 
		s.pontos AS Pts, s.vitorias AS V, s.empates AS E, s.derrotas AS D,
		s.golos_marcados AS GM, s.golos_sofridos AS GS, s.golos_diferenca AS DG, s.media_marcados AS MGM, s.media_sofridos AS MGS
FROM status_equipa AS s
JOIN equipa AS e ON e.id = s.equipa_id
ORDER BY s.ranking ASC

-- golos marcados por equipa 
SELECT  e.nome AS clube, count(ev.ocorrencia) AS golos
FROM equipa AS e
JOIN jogo_evento AS ev ON ev.equipa_id = e.id
JOIN jogo AS jo ON jo.id = ev.jogo_id
JOIN temporada as t ON t.id = jo.temporada_id
where ev.ocorrencia = 'golo' and t.ano = 2020
GROUP BY clube
ORDER BY golos DESC


/************** ESTATISTICAS (AINDA EM TESTES!!!!!!!!!!!!!) **************/ 
select j.foto_link, j.nome, 
	e.emblema_link, e.nome AS clube,
	(select count(ev.ocorrencia) as golos
	 from jogo_evento as ev
	 join jogador as j on j.id = ev.jogador_id
	 where ev.ocorrencia = 'golo' and j.id = 50
	 group by j.nome)
	/*(select count(ev.ocorrencia) as ca
	 from jogo_evento as ev
	 join jogador as j on j.id = ev.jogador_id
	 where ev.ocorrencia = 'ca'
	 group by j.nome),
	(select count(ev.ocorrencia) as cv
	 from jogo_evento as ev
	 join jogador as j on j.id = ev.jogador_id
	 where ev.ocorrencia = 'cv'
	 group by j.nome) */
from jogo_evento as ev
join jogador as j on j.id = ev.jogador_id
join equipa as e on e.id = ev.equipa_id
group by j.nome, j.foto_link, e.emblema_link, e.nome;


--MELHORES MARCADORES
select jo.nome, jo.posicao as pos, count(ev.ocorrencia) as golos
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
where ev.ocorrencia = 'golo'
group by jo.nome, pos
order by golos desc
offset 10 limit 10

		
		
/************** JOGO **************/ 

		--VERIFICAR O RESULTADO DE UM JOGO *LINHA*		
select e.nome as clube, count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.id = '2' and ev.ocorrencia = 'golo' and t.ano = 2020
group by e.nome


		--VERIFICAR O RESULTADO DE UM JOGO *COLUNA*	
select c.nome as casa, 

(select count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.id = '3' and ev.ocorrencia = 'golo' and t.ano = ano 
group by e.nome limit 1
) as resultadoCasa, 

f.nome as fora, 

(select count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.id = '3' and ev.ocorrencia = 'golo' and t.ano = ano 
group by e.nome offset 1 limit 1
) as resultadoFora

from jogo as j
join equipa as c on c.id = j.equipa_casa
join equipa as f on f.id = j.equipa_fora
join jogo_evento as ev on ev.jogo_id = j.id
join temporada as t on t.id = j.temporada_id
where j.id = '3' and ev.ocorrencia = 'golo'  and t.ano = ano 
limit 1


/************** LISTA DE JOGOS **************/ 

--VERIFICAR OS RESULTADOS DOS JOGOS DE UMA JORNADA
select j.n_jornada as jornada, e.nome as clube, count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.n_jornada = '1' and ev.ocorrencia = 'golo' and t.ano = 2020
group by j.n_jornada, e.nome

