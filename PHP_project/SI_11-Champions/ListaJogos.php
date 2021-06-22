<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Lista de Jogos</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="stylemenu.css">
    <script type="text/javascript" src="scripts.js"></script>
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
                <input type="radio" name="tab" id="navBtn2" checked>
                <input type="radio" name="tab" id="navBtn3">
                <input type="radio" name="tab" id="navBtn4">
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
    <?php
    $str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
    $conn = pg_connect($str) or die("Erro na ligacao");

    $jornadaSelecionada = $_POST['jornada_selecionada'];
    echo "<h3 class='tituloextra'>Jornada selecionada: $jornadaSelecionada</h3>";

    $ano = $_POST['temporada'];
    //echo "ANO: $ano " . "JORNADA: $jogos_jornadaselecionada";

    $queryJogosJornadaAtual = pg_query($conn, "SELECT j.id, j.n_jogo AS n_jogo, j.data, casa.emblema_link AS emblema_casa, casa.nome AS casa, 
	fora.emblema_link AS emblema_fora, fora.nome AS fora
    FROM jogo AS j 
	JOIN equipa AS casa ON casa.id = j.equipa_casa
	JOIN equipa AS fora ON fora.id = j.equipa_fora
    JOIN temporada AS t ON t.id = j.temporada_id
    WHERE j.n_jornada = '$jornadaSelecionada' AND t.ano = '$ano'
    ORDER BY j.n_jogo ASC") or die;

    $JogosJornadaAtual = pg_affected_rows($queryJogosJornadaAtual);


    $temporada_id = 1;
    if($ano == 2017) $temporada_id = 1;
    if($ano == 2018) $temporada_id = 2;
    if($ano == 2019) $temporada_id = 3;
    if($ano == 2020) $temporada_id = 4;

    $primeiroID = ($jornadaSelecionada * 9 - 8) + ($temporada_id-1) * 306;

    $index = 0;

    for ($idJogo = $primeiroID; $idJogo < $primeiroID + 9; $idJogo++) {
        $queryResultadoJogos[$index] = pg_query($conn, "select j.id, c.nome as casa, 
                                                            (select count(ev.ocorrencia) as golos
                                                    from jogo_evento as ev
                                                    join jogo as j on j.id = ev.jogo_id
                                                    join equipa as e on e.id = ev.equipa_id
                                                    join temporada as t on t.id = j.temporada_id
                                                    where j.id = $idJogo and ev.ocorrencia = 'golo' and t.ano = ano 
                                                    group by e.nome limit 1
                                                    ) as resultadoCasa, 
                                                         f.nome as fora, 
                                                            (select count(ev.ocorrencia) as golos
                                                    from jogo_evento as ev
                                                    join jogo as j on j.id = ev.jogo_id
                                                    join equipa as e on e.id = ev.equipa_id
                                                    join temporada as t on t.id = j.temporada_id
                                                    where j.id = $idJogo and ev.ocorrencia = 'golo' and t.ano = ano 
                                                    group by e.nome offset 1 limit 1
                                                    ) as resultadoFora
                                                         from jogo as j
                                                    join equipa as c on c.id = j.equipa_casa
                                                    join equipa as f on f.id = j.equipa_fora
                                                    join jogo_evento as ev on ev.jogo_id = j.id
                                                    join temporada as t on t.id = j.temporada_id
                                                    where j.id = $idJogo and ev.ocorrencia = 'golo'  and t.ano = ano 
                                                    limit 1") or die;
        $index++;
    }

    $listaResultados = null;
    if($primeiroID < 1027){
        for ($i = 0; $i < 9; $i++) {
            $jogoRes = pg_fetch_array($queryResultadoJogos[$i]);
            $listaResultados[$i][0] = $jogoRes['resultadocasa'];
            $listaResultados[$i][1] = $jogoRes['resultadofora'];
        }
    }







    $jogo = array();
    $list_jogos = null;
    for ($i = 0; $i < $JogosJornadaAtual; $i++) { // $i => n_jogo que vai até 9 ($JogosJornadaAtual)
        $jogo = pg_fetch_array($queryJogosJornadaAtual);
        // para cada [n_jogo] cria 8 colunas
        $list_jogos[$i][0] = $jogo['id'];
        $list_jogos[$i][1] = $jogo['data'];
        $list_jogos[$i][2] = $jogo['emblema_casa'];
        $list_jogos[$i][3] = $jogo['casa'];
        $list_jogos[$i][4] = $jogo['emblema_fora'];
        $list_jogos[$i][5] = $jogo['fora'];
    }


    echo build_table($list_jogos, $listaResultados, $primeiroID < 1027);

    pg_close($conn);

    ?>
    <script>
        function RedirectJogo() {
            window.location.assign("Jogo.php")
        }
    </script>
    <?php
    function build_table($array, $golos, $bool)
    {
        echo "<div class='selection'>";
        echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

        echo "<thead>";
        echo "<tr>";
        echo "<th>" . "Nº" . "</th>";
        echo "<th>" . "DATA" . "</th>";
        echo "<th style='text-align: right;'>" . "CASA" . "</th>";
        echo "<th>" . "" . "</th>";
        echo "<th style='padding: 0px 20px 0px 20px;'>" . "" . "</th>";
        echo "<th style='padding: 0px 20px 0px 20px;'>" . "" . "</th>";
        echo "<th>" . " " . "</th>";
        echo "<th style='text-align: left;'>" . "FORA" . "</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tbody>"; // Table body
        $pagDestino = 'Jogo.php';
        $value = 'jogo';

        for ($i = 0; $i < count($array); $i++) { // linhas (9 linhas)
            $n_jogo = $i + 1;
            $imgSrc1 = $array[$i][2];//emblema_casa
            $imgSrc2 = $array[$i][4];//emblema_fora
            $id = $array[$i][0];

            echo "<tr onclick='redirectPage(\"$pagDestino\", \"$value\", \"$id\")' class='linha' style='height: 80px;'>"; // cria linhas para cada jogo
            echo "<td>" . " " . $n_jogo . " " . "</td>";
            echo "<td>" . " " . $array[$i][1] . " " . "</td>"; //data
            echo "<td style='text-align: right; padding-right: 10px;'>" . " " . $array[$i][3] . "</td>"; //casa
            echo "<td>" . "<img src= $imgSrc1 style='vertical-align:middle; height: 45px;'>" . " " . "</td>"; //emblema_casa
            if($bool){
                echo "<td>" . " " . $golos[$i][0] . " " . "</td>"; //resultado casa
                echo "<td>" . " " . $golos[$i][1] . " " . "</td>"; //resultado fora


            } else{
                echo "<td>" . " " . " _ " . " " . "</td>"; //resultado casa
                echo "<td>" . " " . " _ " . " " . "</td>"; //resultado fora
            }

            echo "<td>" . "<img src= $imgSrc2 style='vertical-align:middle; height: 45px;' >" . " " . "</td>";//emblema_fora
            echo "<td style='text-align: left; padding-left: 10px;'>" . " " . $array[$i][5] . " " . "</td>";
            echo "<input type='hidden' name='postIDjogo' value='$id'>" . "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }

    ?>
</div>
<div class="footer">
    Trabalho desenvolvido por Artem Basok e Kelvin Clark,<br>
    no âmbito da disciplina de Sistemas Informáticos, 2020
</div>
</body>
</html>
