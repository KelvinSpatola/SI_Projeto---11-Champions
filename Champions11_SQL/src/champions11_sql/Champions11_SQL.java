package champions11_sql;

/**
 *
 * @author Kelvin Clark Spátola
 */

import java.io.File;
import java.util.ArrayList;
import java.util.List;
import processing.core.PApplet;
import static champions11_sql.Util.*;
import java.util.Random;
import java.util.HashMap;


public class Champions11_SQL {
    
    public static final String ANO = "2020";
    public static int JOGO_ID = 1 + 306 + 306 + 306; // somar mais 306 a cada nova temporada
    public static final String TEMPORADA_ID = "4";
    public static final int jornadaAtual = 13; // meter 35 para o caso de toda a temporada já estar terminada!!!
    
    public static void main(String[] args) {
        //createEquipas();
        //createJogadores();
        createCampeonato();
    }
    
    private static void createEquipas() {
        // EQUIPA VALUES: id(PK), nome, emblema_link, equipamento_link, formacao_link.
        
        File equipaFile = new File("data\\equipas\\LISTA_EQUIPAS.txt");
        String[] equipas = PApplet.loadStrings(equipaFile);
        String[] outputFile1 = new String[equipas.length+1];
        
        outputFile1[0] = "INSERT INTO equipa (nome, emblema_link, equipamento_link, formacao_link) VALUES";
        
        for (int i = 0; i < equipas.length; i++) {
            String[] partes = splitParts(equipas[i]); // [nome], [emblema], [equipamento], [formação]
            outputFile1[i+1] = createSqlInsertValues(i == equipas.length-1, partes[0], partes[1], partes[2], partes[3]);
        }
        PApplet.saveStrings(new File("output\\2_Equipas\\1_Equipas_inserts_SQL.sql"), outputFile1);
        System.out.println("Equipas_inserts_SQL.sql - FINISHED!\n");
    }
    
    private static void createJogadores() {
        // JOGADOR VALUES: id(PK), nome, posicao, ca, cv, foto_link, equipa_id(FK).
        
        File pasta = new File("data\\jogadores");
        File[] files = pasta.listFiles();
        
        List<String>  resultadoJogadores = new ArrayList();
        resultadoJogadores.add("INSERT INTO jogador (nome, posicao, foto_link, equipa_id) VALUES\n");
        
        for (int i = 0; i < files.length; i++) {
            String[] nomes = PApplet.loadStrings(files[i]);
            
            String nomeEquipa = files[i].getName().toUpperCase();
            nomeEquipa = nomeEquipa.substring(0, nomeEquipa.indexOf('.'));
            
            resultadoJogadores.add("/* ---------- " + nomeEquipa + " ---------- */");
            
            for (int j = 0; j < nomes.length; j++) {
                String[] partes = splitParts(nomes[j]); // [nome], [posicao], [foto_link]
                
                boolean isLastInsert = i == files.length-1 && j == nomes.length-1;
                String resultado = createSqlInsertValues(isLastInsert, partes[0], partes[1], partes[2], Integer.toString(i+1));
                resultadoJogadores.add(resultado);
            }
            resultadoJogadores.add("");
        }
        String[] outputFile2 = new String[resultadoJogadores.size()];
        PApplet.saveStrings(new File("output\\2_Equipas\\2_Jogadores_inserts_SQL.sql"), resultadoJogadores.toArray(outputFile2));
        System.out.println("Jogadores_inserts_SQL - FINISHED!\n");
    }
    
    private static void createCampeonato() {
        
        List<String> listaJogos = new ArrayList();
        
        listaJogos.add("/* ---------- TEMPORADA ---------- */");
        
        // TEMPORADA VALUES: id(PK), ano.
        String temporadaID = TEMPORADA_ID;
        String anoTemporada = ANO;
        
        String resultadoTemporada = createSqlInsert("temporada", temporadaID, anoTemporada);
        listaJogos.add(resultadoTemporada);
        listaJogos.add("\n");
        
        
        // JOGO VALUES: id(PK), n_jornada, n_jogo, data, terminado, resultado_casa, resultado_fora, equipa_casa(FK), equipa_fora(FK), temporada_id(FK)
        listaJogos.add("INSERT INTO jogo (n_jornada, n_jogo, data, terminado, equipa_casa, equipa_fora, temporada_id) VALUES");
        listaJogos.add("");
        
        
        // JOGO_EVENTO VALUES: id(PK), ocorrencia, minuto, penalti, jogador_id(FK), jogo_id(FK), equipa_id(FK)
        List<String> listaEventos = new ArrayList();
        listaEventos.add("INSERT INTO jogo_evento (ocorrencia, minuto, penalti, jogador_id, jogo_id, equipa_id) VALUES");
        listaEventos.add("");
        
        
        // STATUS_EQUIPA VALUES: id(PK), n_jogos, pontos, ranking, vitorias, derrotas, empates, golos_marcados, golos_sofridos, equipa_id(FK), temporada_id(FK)
        List<String> listaStatus = new ArrayList();
        listaStatus.add("INSERT INTO status_equipa (n_jogos, pontos, ranking, vitorias, derrotas, empates, golos_marcados, golos_sofridos, equipa_id, temporada_id) VALUES");
        listaStatus.add("");
        
        
        final int totalJornadas = 34;
        final int totalJogos = 9;
        
        
        File jogosFile = new File("data\\jogos\\Matriz_jogos.txt");
        String[] matrizJogos = PApplet.loadStrings(jogosFile);
        int jogoIndex = 0;
        
        
        String[] correnciaTipo = {"'golo'", "'ca'", "'cv'", "'nulo'"};
        
        //********* STATUS_EQUIPA *********
        int[] ranking_equipas = new int[18];
        int[] n_jogos_counter = new int[18];
        int[] pontos_counter = new int[18];
        int[] vitorias_counter = new int[18];
        int[] derrotas_counter = new int[18];
        int[] empates_counter = new int[18];
        int[] golos_marcados_counter = new int[18];
        int[] golos_sofridos_counter = new int[18];
        
        for(int i = 0; i < 18; i++){
            n_jogos_counter[i] = 0;
            pontos_counter[i] = 0;
            vitorias_counter[i] = 0;
            derrotas_counter[i] = 0;
            empates_counter[i] = 0;
            golos_marcados_counter[i] = 0;
            golos_sofridos_counter[i] = 0;
        }
        
        for(int n_jornada = 1; n_jornada <= totalJornadas; n_jornada++){ // EM CADA JORNADA...
            
            listaJogos.add("/* ---------- JORNADA " + n_jornada + " ---------- */");
            listaEventos.add("/* ============ JORNADA " + n_jornada + " ============*/");
            
            
            String data = "'" + createTimestamp(Integer.valueOf(anoTemporada), 2, n_jornada * 7 - 6, 16) + "'";
            
            for(int n_jogo = 1; n_jogo <= totalJogos; n_jogo++) { // EM CADA JOGO...
                String[] parts = matrizJogos[jogoIndex].split(",");
                boolean isLastInsert = n_jornada == totalJornadas && n_jogo == totalJogos;
                
                listaEventos.add("-- JOGO #ID " + JOGO_ID);
                
                String equipaCasa = parts[0];
                String equipaFora = parts[1];
                
                
                if(n_jornada < jornadaAtual){
                    int resultadoCasa = 0;
                    int resultadoFora = 0;
                    
                    Random r = new Random();
                    
                    short count = 0; // 0 - casa | 1 - fora
                    while(count < 2){
                        String equipaID = (count == 0) ? equipaCasa : equipaFora;
                        
                        int totalGolos = (int)(0f + r.nextGaussian() * 2f);
                        if (totalGolos < 1) totalGolos = 1;
                        
                        r = new Random();
                        int totalCA = (int)(0f + r.nextGaussian() * 1.6f);
                        if (totalCA < 0) totalCA = 0;
                        
                        r = new Random();
                        int totalCV = (int)(0f + r.nextGaussian() * 0.75f);
                        if (totalCV < 0) totalCV = 0;
                        
                        if(count == 0) {
                            resultadoCasa = totalGolos;
                        } else {
                            resultadoFora = totalGolos;
                            
                            golos_marcados_counter[Integer.valueOf(equipaCasa)-1] += resultadoCasa;
                            golos_sofridos_counter[Integer.valueOf(equipaCasa)-1] += resultadoFora;
                            
                            golos_marcados_counter[Integer.valueOf(equipaFora)-1] += resultadoFora;
                            golos_sofridos_counter[Integer.valueOf(equipaFora)-1] += resultadoCasa;
                        }
                        
                        int totalOcorrencias = totalGolos + totalCA + totalCV;
                        List<String> ocorrenciasJogo = new ArrayList();
                        
                        for(int i = 0; i < totalGolos; i++) ocorrenciasJogo.add(correnciaTipo[0]);
                        for(int i = 0; i < totalCA; i++)    ocorrenciasJogo.add(correnciaTipo[1]);
                        for(int i = 0; i < totalCV; i++)    ocorrenciasJogo.add(correnciaTipo[2]);
                        
                        for(int i = 0; i < totalOcorrencias; i++) {
                            String ocorrencia = ocorrenciasJogo.get(i);
                            String minuto = Integer.toString(randomInt(90) + 1);
                            String penalti = ocorrencia.equals(correnciaTipo[0]) ? (randomInt(10) > 7 ? "TRUE" : "FALSE") : "FALSE";
                            
                            int min = (Integer.valueOf(equipaID) * 11) - 6;
                            int max = (Integer.valueOf(equipaID) * 11) + 1;
                            
                            String jogadorID = Integer.toString(randomInt(min, max));
                            
                            
                            boolean isLast = isLastInsert && count == 1 && i == totalOcorrencias-1;
                            String resultadoEventos = createSqlInsertValues(isLast, ocorrencia, minuto, penalti, jogadorID, Integer.toString(JOGO_ID), equipaID) + ((count == 0) ? " --casa" : " --fora");
                            listaEventos.add(resultadoEventos);
                            
                        }
                        ocorrenciasJogo.clear();
                        count++; // troca de equipa
                        
                        n_jogos_counter[Integer.valueOf(equipaID)-1] += 1;
                    }
                    
                    if(resultadoCasa == resultadoFora) {
                        pontos_counter[Integer.valueOf(equipaCasa)-1] += 1;
                        pontos_counter[Integer.valueOf(equipaFora)-1] += 1;
                        empates_counter[Integer.valueOf(equipaCasa)-1] += 1;
                        empates_counter[Integer.valueOf(equipaFora)-1] += 1;
                    }
                    else {
                        int vencedor = (resultadoCasa > resultadoFora) ? Integer.valueOf(equipaCasa)-1 : Integer.valueOf(equipaFora)-1;
                        int perdedor = (resultadoCasa < resultadoFora) ? Integer.valueOf(equipaCasa)-1 : Integer.valueOf(equipaFora)-1;
                        
                        pontos_counter[vencedor] += 3;
                        vitorias_counter[vencedor] += 1;
                        derrotas_counter[perdedor] += 1;
                    }
                    listaEventos.add("");
                }
                
                String resultadoJogos = "";
                if(n_jornada < jornadaAtual) resultadoJogos = createSqlInsertValues(isLastInsert, Integer.toString(n_jornada), Integer.toString(n_jogo), data, "TRUE", equipaCasa, equipaFora, temporadaID);
                else resultadoJogos = createSqlInsertValues(isLastInsert, Integer.toString(n_jornada), Integer.toString(n_jogo), data, "FALSE", equipaCasa, equipaFora, temporadaID);
                listaJogos.add(resultadoJogos);
                
                jogoIndex++;
                JOGO_ID++;
            }
            listaJogos.add("");
            listaEventos.add("");
        }
        // ID      PTS desordenado
        HashMap<Integer, Integer> ranking = new HashMap<>();
        
        for(int i = 0; i < pontos_counter.length; i++){
            ranking.put(i+1, pontos_counter[i]);
        }
        // ID      PTS   ordenados(PTS)
        ranking = (HashMap<Integer, Integer>) sortByValue(ranking, false);
        
        int k = 0;
        for (Integer key : ranking.keySet()) {
            ranking_equipas[k] = (key);
            k++;
        }
        
        for(int i = 0; i < 18; i++){
            boolean isLast = i == 17;
            
            int index = ranking_equipas[i]-1;
            
            String n_jogos = Integer.toString(n_jogos_counter[index]);
            String pontos = Integer.toString(pontos_counter[index]);
            
            String pos = Integer.toString(i+1);
            
            String vitorias = Integer.toString(vitorias_counter[index]);
            String derrotas = Integer.toString(derrotas_counter[index]);
            String empates = Integer.toString(empates_counter[index]);
            String golos_marcados = Integer.toString(golos_marcados_counter[index]);
            String golos_sofridos = Integer.toString(golos_sofridos_counter[index]);
            String equipa_id = Integer.toString(index+1);
            String temporada_id = temporadaID;
            
            String resultadoStatus = createSqlInsertValues(isLast, n_jogos, pontos, pos, vitorias, derrotas, empates, golos_marcados, golos_sofridos, equipa_id, temporada_id);
            listaStatus.add(resultadoStatus);
        }
        
        
        String[] outputFile3 = new String[listaJogos.size()];
        PApplet.saveStrings(new File("output\\3_Campeonato"+ANO+"\\1_Campeonato_inserts_SQL.sql"), listaJogos.toArray(outputFile3));
        System.out.println("Campeonato_inserts_SQL - FINISHED!\n");
        
        
        String[] outputFile4 = new String[listaEventos.size()];
        PApplet.saveStrings(new File("output\\3_Campeonato"+ANO+"\\2_JogoEvento_inserts_SQL.sql"), listaEventos.toArray(outputFile4));
        System.out.println("JogoEvento_inserts_SQL - FINISHED!\n");
        
        String[] outputFile5 = new String[listaStatus.size()];
        PApplet.saveStrings(new File("output\\3_Campeonato"+ANO+"\\3_StatusEquipa_inserts_SQL.sql"), listaStatus.toArray(outputFile5));
        System.out.println("StatusEquipa_inserts_SQL - FINISHED!\n");
        
    }
}
