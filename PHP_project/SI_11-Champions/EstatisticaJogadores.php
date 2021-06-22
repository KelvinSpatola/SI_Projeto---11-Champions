<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Classificações do Campeonato</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="stylemenu.css">
</head>

<body>

<div class="logincontainer">
    <?php
    if (isset($_SESSION['id']) && isset($_SESSION['nome'])) {
    ?>
    <p style="color: fff; padding-right 10px; margin-right: 10px;"> Hello, <?php echo $_SESSION['nome']; ?></p>
    <a href='logout.php'>Logout</a>

</div>
<?php
} else {
    echo "<a href='login.php'>Login</a>";
}
?>
</div>

<div class="topmenu">
    <div style="width: 55vw; align-items: center;">
        <div class="logo">
            <a href="#">
                <a href="home.php"> <img border="0" alt="11Champions" src="img/logo.svg"></a>
            </>
        </div>
        <div class="wrapper" style="width: 40vw; align-items: center; margin-left: 7vw;">
            <nav>
                <input type="radio" name="tab" id="navBtn1">
                <input type="radio" name="tab" id="navBtn2">
                <input type="radio" name="tab" id="navBtn3">
                <input type="radio" name="tab" id="navBtn4" checked>
                <input type="radio" name="tab" id="navBtn5">

                <label for="navBtn1" class="navBtn1" onclick="window.location.href='home.php'"><a href="#">Home</a></label>
                <label for="navBtn2" class="navBtn2" onclick="window.location.href='ListaJornadas.php'"><a href="#">Jornadas</a></label>
                <label for="navBtn3" class="navBtn3" onclick="window.location.href='Classificacoes.php'"><a href="#">Classificações</a></label>
                <label for="navBtn4" class="navBtn4" onclick="window.location.href='EstatisticaJogadores.php'"><a
                            href="#">Estatística</a></label>
                <label for="navBtn5" class="navBtn5" onclick="window.location.href='ListaClubes.php'"><a
                            href="#">Clubes</a></label>
                <div class="tab">
                </div>
            </nav>
        </div>
    </div>
</div>
<div class="content">
    <div class="pageinfo">

        <?php
        $str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
        $conn = pg_connect($str) or die("Erro na ligacao");

        $queryListaTemporadasDropdown = pg_query($conn, "SELECT DISTINCT t.ano FROM temporada as t ORDER BY t.ano DESC") or die;

        $arrayOrderBySELECT = array("mais golos", "menos golos", "mais cartoes amarelos", "menos cartoes amarelos", "mais cartoes vermelhos", "menos cartoes vermelhos");

        if (isset($_POST['PesquisaJogador']) or (isset($_POST['temporada']) && isset($_POST['orderByClassClubes']))) {
            $ano = $_POST['temporada'];
            $queryRecebida = $_POST['orderByClassClubes'];
            if (empty($_POST['PesquisaJogador'])) {


                echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: space-between;' name='myform3' action='EstatisticaJogadores.php' method='POST'>";

                echo "<div class='selection' style='padding: 10px; padding-bottom: 20px; margin-right: 60px;  display: inline-block; '>";
                echo "<label style='padding: 20px';>Pesquise pela jornada ou ocorrencia:</label></br>";
                echo "<select class='selection' style='margin: 10px;' name='temporada'>";
                while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                    $temporada_ano = $rows['ano'];
                    echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
                }
                echo "</select>";
                echo "<select class='selection' name='orderByClassClubes'>";
                for ($i = 0; $i < count($arrayOrderBySELECT); $i++) {
                    echo "<option class='selection' value='$arrayOrderBySELECT[$i]'> $arrayOrderBySELECT[$i] </option>";
                }
                echo "</select>";
                echo "<input style='width: 200px;' type='submit' name='submit' value='Ver as estatísticas'>";
                echo "</div>";

                echo "<div class='selection' style=' margin-bottom: 10px; padding: 20px; display: inline-block; '>";
                echo "<label>Pesquise pelo nome do jogador:</label>";
                echo "<input style=' width: 300px; height: 35px' type='text'  name='PesquisaJogador'>";
                echo "<input style='width: 200px;' type='submit' name='submit' value='Pesquisar'>";
                echo "</div>";

                echo "</form>";

                echo "<h2 class='titulo'>TOP 20 jogadores com " . $queryRecebida . ", da temporada: " . $ano . " </h2>";

                if ($queryRecebida == $arrayOrderBySELECT[0]) {
                    $a = "GOLOS";
                    $stringPrefixo = "select 
                                           jo.id as jid, 
                                           jo.foto_link as foto,
                                           jo.nome as jnome, 
                                           e.emblema_link as emblemalink, 
                                           e.nome AS clube, 
                                           count(ev.ocorrencia) as ocorr
                                    from jogador as jo
                                    join jogo_evento as ev on ev.jogador_id = jo.id
                                    join equipa as e on e.id = ev.equipa_id
                                    JOIN jogo AS j ON j.id = ev.jogo_id
                                    JOIN temporada AS t ON t.id = j.temporada_id
                                    where ev.ocorrencia = 'golo' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr desc limit 20";
                }
                if ($queryRecebida == $arrayOrderBySELECT[1]) {
                    $a = "GOLOS";
                    $stringPrefixo = "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'golo' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr asc limit 20";
                }
                if ($queryRecebida == $arrayOrderBySELECT[2]) {
                    $a = "CA";
                    $stringPrefixo = "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'ca' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr desc limit 20";
                }
                if ($queryRecebida == $arrayOrderBySELECT[3]) {
                    $a = "CA";
                    $stringPrefixo = "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'ca' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr asc limit 20";
                }
                if ($queryRecebida == $arrayOrderBySELECT[4]) {
                    $a = "CV";
                    $stringPrefixo = "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'cv' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr desc limit 20";
                }
                if ($queryRecebida == $arrayOrderBySELECT[5]) {
                    $a = "CV";
                    $stringPrefixo = "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'cv' and t.ano= ";
                    $stringSufixo = " group by jid, foto, jnome, emblemalink, clube order by ocorr asc limit 20";
                }
                $stringNovaQuery = $stringPrefixo . $ano . $stringSufixo;


                $queryNova = pg_query($conn, "$stringNovaQuery");
                $totalRows = pg_affected_rows($queryNova);

                $marcador = array();
                $list_marcadores = null;
                for ($i = 0; $i < $totalRows; $i++) {
                    $marcador = pg_fetch_array($queryNova, $i);
                    $list_marcadores[$i][0] = $marcador['jid'];
                    $list_marcadores[$i][1] = $marcador['foto'];
                    $list_marcadores[$i][2] = $marcador['jnome'];
                    $list_marcadores[$i][3] = $marcador['emblemalink'];
                    $list_marcadores[$i][4] = $marcador['clube'];
                    $list_marcadores[$i][5] = $marcador['ocorr'];
                }
                echo build_table_marcadores($list_marcadores, $a);



            }else{










                function validate($data)
                {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }

                $queryRecebidaPesquisaJogador = validate($_POST['PesquisaJogador']);
                $StringPesquisaJogador = "select jo.id as jid, jo.foto_link as foto, jo.nome as jnome, 
	e.emblema_link as emblemalink, e.nome AS clube,
	
	(select count(ev.ocorrencia)
from jogador as jo 
join jogo_evento as ev on ev.jogador_id = jo.id
join jogo as j on j.id = ev.jogo_id
join temporada as t on t.id = j.temporada_id
where ev.ocorrencia = 'golo' and jo.nome= '$queryRecebidaPesquisaJogador' and t.ano = $ano
group by jo.nome limit 1) as golos,

(select count(ev.ocorrencia)
from jogador as jo 
join jogo_evento as ev on ev.jogador_id = jo.id 
join jogo as j on j.id = ev.jogo_id
join temporada as t on t.id = j.temporada_id
where ev.ocorrencia = 'ca' and jo.nome= '$queryRecebidaPesquisaJogador' and t.ano = $ano
group by jo.nome limit 1) as ca,

(select count(ev.ocorrencia)
from jogador as jo 
join jogo_evento as ev on ev.jogador_id = jo.id 
join jogo as j on j.id = ev.jogo_id
join temporada as t on t.id = j.temporada_id
where ev.ocorrencia = 'cv' and jo.nome= '$queryRecebidaPesquisaJogador' and t.ano = $ano
group by jo.nome limit 1) as cv

from jogador as jo
join jogo_evento as ev on jo.id= ev.jogador_id
join equipa as e on e.id = ev.equipa_id
join jogo as j on j.id = ev.jogo_id
join temporada as t on t.id = j.temporada_id
where jo.nome= '$queryRecebidaPesquisaJogador' and t.ano = $ano
Order by golos ASC
limit 1";
                //echo $ano . "</br>" . $queryRecebida . "</br>" . $queryRecebidaPesquisaJogador . "</br>" . $StringPesquisaJogador;


                echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: space-between;' name='myform3' action='EstatisticaJogadores.php' method='POST'>";

                echo "<div class='selection' style='padding: 10px; padding-bottom: 20px; margin-right: 60px;  display: inline-block; '>";
                echo "<label style='padding: 20px';>Pesquise pela jornada ou ocorrencia:</label></br>";
                echo "<select class='selection' style='margin: 10px;' name='temporada'>";
                while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                    $temporada_ano = $rows['ano'];
                    echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
                }
                echo "</select>";
                echo "<select class='selection' name='orderByClassClubes'>";
                for ($i = 0; $i < count($arrayOrderBySELECT); $i++) {
                    echo "<option class='selection' value='$arrayOrderBySELECT[$i]'> $arrayOrderBySELECT[$i] </option>";
                }
                echo "</select>";
                echo "<input style='width: 200px;' type='submit' name='submit' value='Ver as estatísticas'>";
                echo "</div>";

                echo "<div class='selection' style=' margin-bottom: 10px; padding: 20px; display: inline-block; '>";
                echo "<label>Pesquise pelo nome do jogador:</label>";
                echo "<input style=' width: 300px; height: 35px' type='text'  name='PesquisaJogador'>";
                echo "<input style='width: 200px;' type='submit' name='submit' value='Pesquisar'>";
                echo "</div>";

                echo "</form>";

                echo "<h2 class='titulo2'>Estatistica do jogador :". " " . $queryRecebidaPesquisaJogador . "na temporada" . $ano . "</h2>";

                $queryPesquisaJogador = pg_query($conn, "$StringPesquisaJogador");
                $totalRows = pg_affected_rows($queryPesquisaJogador);

                $marcador = array();
                $list_marcadores = null;
                for ($i = 0; $i < $totalRows; $i++) {
                    $marcador = pg_fetch_array($queryPesquisaJogador, $i);
                    $list_marcadores[$i][0] = $marcador['jid'];
                    $list_marcadores[$i][1] = $marcador['foto'];
                    $list_marcadores[$i][2] = $marcador['jnome'];
                    $list_marcadores[$i][3] = $marcador['emblemalink'];
                    $list_marcadores[$i][4] = $marcador['clube'];
                    $list_marcadores[$i][5] = $marcador['golos'];
                    $list_marcadores[$i][6] = $marcador['ca'];
                    $list_marcadores[$i][7] = $marcador['cv'];
                }
                echo build_table_pesquisajogador($list_marcadores);














            }

        } else {


            echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: space-between;' name='myform3' action='EstatisticaJogadores.php' method='POST'>";

            echo "<div class='selection' style='padding: 10px; padding-bottom: 20px; margin-right: 60px;  display: inline-block; '>";
            echo "<label style='padding: 20px';>Pesquise pela jornada ou ocorrencia:</label></br>";
            echo "<select class='selection' style='margin: 10px;' name='temporada'>";
            while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                $temporada_ano = $rows['ano'];
                echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
            }
            echo "</select>";
            echo "<select class='selection' name='orderByClassClubes'>";
            for ($i = 0; $i < count($arrayOrderBySELECT); $i++) {
                echo "<option class='selection' value='$arrayOrderBySELECT[$i]'> $arrayOrderBySELECT[$i] </option>";
            }
            echo "</select>";
            echo "<input style='width: 200px;' type='submit' name='submit' value='Ver as estatísticas'>";
            echo "</div>";

            echo "<div class='selection' style=' margin-bottom: 10px; padding: 20px; display: inline-block; '>";
            echo "<label>Pesquise pelo nome do jogador:</label>";
            echo "<input style=' width: 300px; height: 35px' type='text'  name='PesquisaJogador'>";
            echo "<input style='width: 200px;' type='submit' name='submit' value='Pesquisar'>";
            echo "</div>";

            echo "</form>";

            echo "<h2 class='titulo2'>TOP 20 jogadores com mais golos, da temporada: 2020</h2>";


            $a = "GOLOS";
            $queryPagInicial = pg_query($conn, "select 
       jo.id as jid, 
       jo.foto_link as foto,
       jo.nome as jnome, 
       e.emblema_link as emblemalink, 
       e.nome AS clube, 
       count(ev.ocorrencia) as ocorr
from jogador as jo
join jogo_evento as ev on ev.jogador_id = jo.id
join equipa as e on e.id = ev.equipa_id
JOIN jogo AS j ON j.id = ev.jogo_id
JOIN temporada AS t ON t.id = j.temporada_id
where ev.ocorrencia = 'golo' and t.ano= 2020
group by jid, foto, jnome, emblemalink, clube
order by ocorr desc
limit 20");
            $totalRows = pg_affected_rows($queryPagInicial);



            $marcador = array();
            $list_marcadores = null;
            for ($i = 0; $i < $totalRows; $i++) {
                $marcador = pg_fetch_array($queryPagInicial, $i);
                $list_marcadores[$i][0] = $marcador['jid'];
                $list_marcadores[$i][1] = $marcador['foto'];
                $list_marcadores[$i][2] = $marcador['jnome'];
                $list_marcadores[$i][3] = $marcador['emblemalink'];
                $list_marcadores[$i][4] = $marcador['clube'];
                $list_marcadores[$i][5] = $marcador['ocorr'];
            }
            echo build_table_marcadores($list_marcadores, $a);



        }
        pg_close($conn);
        ?>
        <?php

        function build_table($array)
        {
            echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

            echo "<thead>";

            echo "<tr>";
            echo "<th>" . "RANKING" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "NOME" . "</th>";
            echo "<th>" . "PTS" . "</th>";
            echo "<th>" . "V" . "</th>";
            echo "<th>" . "E" . "</th>";
            echo "<th>" . "D" . "</th>";
            echo "<th>" . "GM" . "</th>";
            echo "<th>" . "GS" . "</th>";
            echo "<th>" . "DG " . "</th>";
            echo "<th>" . "MGM" . "</th>";
            echo "<th>" . "MGS" . "</th>";
            echo "</tr>";

            echo "</thead>";

            echo "<tbody>"; // Table body
            for ($i = 0; $i < count($array); $i++) { // linhas (9 linhas)
                $imgSrc1 = $array[$i][1];

                echo "<tr class='linha' style='height: 80px;'>"; // cria linhas para cada Clube
                echo "<td>" . " " . $array[$i][0] . " " . "</td>"; //ranking
                echo "<td>" . "<img src= $imgSrc1 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema
                echo "<td>" . " " . $array[$i][2] . " " . "</td>"; // nome
                echo "<td>" . " " . $array[$i][3] . " " . "</td>"; // pts
                echo "<td>" . " " . $array[$i][4] . " " . "</td>"; // v
                echo "<td>" . " " . $array[$i][5] . " " . "</td>"; // e
                echo "<td>" . " " . $array[$i][6] . " " . "</td>"; // d
                echo "<td>" . " " . $array[$i][7] . " " . "</td>"; // gm
                echo "<td>" . " " . $array[$i][8] . " " . "</td>"; // gs
                $dg = $array[$i][7] - $array[$i][8];
                echo "<td>" . " " . $dg . " " . "</td>"; // dg
                $mgm = 34 / $array[$i][7];
                echo "<td>" . " " . round($mgm, 1) . " " . "</td>"; // mgm
                $mgs = 34 / $array[$i][8];
                echo "<td>" . " " . round($mgs, 1) . " " . "</td>"; // mgs
                echo "</tr>";
            }
            echo "</tbody>";

            echo "</table>";
            echo "</div>";
        }

        ?>
        <?php

        function build_table_marcadores($array, $a)
        {
            echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

            echo "<thead>";

            echo "<tr>";
            echo "<th>" . "POS" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "NOME" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "CLUBE" . "</th>";
            echo "<th>" . "$a" . "</th>";
            echo "</tr>";

            echo "</thead>";

            echo "<tbody>"; // Table body
            for ($i = 0; $i < count($array); $i++) { // linhas (9 linhas)
                echo "<tr class='linha' style='height: 80px;'>"; // cria linhas para cada Clube
                $imgSrc1 = $array[$i][1];
                $imgSrc2 = $array[$i][3];
                $n_pos = $i +1;
                echo "<td>" . " " . $n_pos . " " . "</td>"; //id
                echo "<td>" . "<img src= $imgSrc1 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema
                echo "<td>" . " " . $array[$i][2] . " " . "</td>"; // nome
                echo "<td>" . "<img src= $imgSrc2 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema
                echo "<td>" . " " . $array[$i][4] . " " . "</td>"; // clube
                echo "<td>" . " " . $array[$i][5] . " " . "</td>"; // golos
                echo "</tr>";
            }
            echo "</tbody>";

            echo "</table>";
            echo "</div>";
        }
        function build_table_pesquisajogador($array)
        {
            echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

            echo "<thead>";

            echo "<tr>";
            echo "<th>" . "ID" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "NOME" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "CLUBE" . "</th>";
            echo "<th>" . "GOLOS" . "</th>";
            echo "<th>" . "CA" . "</th>";
            echo "<th>" . "CV" . "</th>";
            echo "</tr>";

            echo "</thead>";

            echo "<tbody>"; // Table body
            for ($i = 0; $i < count($array); $i++) { // linhas (9 linhas)
                echo "<tr class='linha' style='height: 80px;'>"; // cria linhas para cada Clube
                $imgSrc1 = $array[$i][1];
                $imgSrc2 = $array[$i][3];
                echo "<td>" . " " . $array[$i][0]. " " . "</td>"; //id
                echo "<td>" . "<img src= $imgSrc1 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema
                echo "<td>" . " " . $array[$i][2] . " " . "</td>"; // nome
                echo "<td>" . "<img src= $imgSrc2 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema
                echo "<td>" . " " . $array[$i][4] . " " . "</td>"; // clube
                echo "<td>" . " " . $array[$i][5] . " " . "</td>"; // golos
                echo "<td>" . " " . $array[$i][6] . " " . "</td>"; // ca
                echo "<td>" . " " . $array[$i][7] . " " . "</td>"; // cv
                echo "</tr>";
            }
            echo "</tbody>";

            echo "</table>";
            echo "</div>";
        }

        ?>
    </div>
</div>
<div class="footer">
    Trabalho desenvolvido por Artem Basok e Kelvin Clark,<br>
    no âmbito da disciplina de Sistemas Informáticos, 2020
</div>
</body>
</html>
