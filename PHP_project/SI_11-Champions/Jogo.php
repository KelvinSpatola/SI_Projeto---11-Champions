<?php
session_start();
?>

<?php


$admin = true;


$str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
$conn = pg_connect($str) or die("Erro na ligacao");

if (isset($_GET['jogo'])) {
    $jogoSelecionado = $_GET['jogo'];
}


$queryJogoSelecionado = pg_query($conn, "SELECT j.n_jogo AS n_jogo, casa.emblema_link AS emblema_casa, casa.nome AS casa,
	fora.emblema_link AS emblema_fora, fora.nome AS fora
    FROM jogo AS j
	LEFT JOIN equipa AS casa ON casa.id = j.equipa_casa
	LEFT JOIN equipa AS fora ON fora.id = j.equipa_fora
    WHERE j.id = '$jogoSelecionado'") or die;

$queryEventos = pg_query($conn, "select j.nome as nome, e.nome as clube, ev.ocorrencia as ocor, ev.minuto as minut
from jogo_evento as ev
join jogo as jog on jog.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join jogador as j on j.id = ev.jogador_id
where jog.id= $jogoSelecionado order by minut asc") or die;


$jogo = pg_fetch_array($queryJogoSelecionado);
$nomeCasa = $jogo['casa'];
$nomeFora = $jogo['fora'];

?>


<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Lista de Jogos</title>
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
<script>
    function goBack() {
        window.history.back();
    }
</script>
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

                <label for="navBtn1" class="navBtn1" onclick="window.location.href='home.php'"><a
                            href="#">Home</a></label>
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

        $jogoSelecionado = $_GET['jogo'];
        $arrayTipoOcorrencias = array("golo", "ca", "cv");


        $queryJogoSelecionado = pg_query($conn, "SELECT j.n_jogo AS n_jogo, casa.emblema_link AS emblema_casa, casa.nome AS casa, 
	fora.emblema_link AS emblema_fora, fora.nome AS fora
    FROM jogo AS j 
	LEFT JOIN equipa AS casa ON casa.id = j.equipa_casa
	LEFT JOIN equipa AS fora ON fora.id = j.equipa_fora
    WHERE j.id =" . $jogoSelecionado) or die;

        $jogo = pg_fetch_array($queryJogoSelecionado);
        $emblemaCasa = $jogo['emblema_casa'];
        $emblemaFora = $jogo['emblema_fora'];
        $nomeCasa = $jogo['casa'];
        $nomeFora = $jogo['fora'];

        $queryBuscarJogadorFora = pg_query($conn, "select jo.nome from jogador as jo
join equipa as e on e.id = jo.equipa_id
join jogo as j on j.equipa_fora = e.id
where j.id = $jogoSelecionado order by jo.nome asc ");

        $queryBuscarResultadoJogo = pg_query($conn, "select c.nome as casa, 

(select count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.id = $jogoSelecionado and ev.ocorrencia = 'golo' and t.ano = ano 
group by e.nome limit 1
) as resultadocasa, 

f.nome as fora, 

(select count(ev.ocorrencia) as golos
from jogo_evento as ev
join jogo as j on j.id = ev.jogo_id
join equipa as e on e.id = ev.equipa_id
join temporada as t on t.id = j.temporada_id
where j.id = $jogoSelecionado and ev.ocorrencia = 'golo' and t.ano = ano 
group by e.nome offset 1 limit 1
) as resultadofora

from jogo as j
join equipa as c on c.id = j.equipa_casa
join equipa as f on f.id = j.equipa_fora
join jogo_evento as ev on ev.jogo_id = j.id
join temporada as t on t.id = j.temporada_id
where j.id = $jogoSelecionado and ev.ocorrencia = 'golo'  and t.ano = ano 
limit 1");

        $fetchResultado = pg_fetch_array($queryBuscarResultadoJogo);
        $list_resultados[0] = $fetchResultado['casa'];
        $list_resultados[1] = $fetchResultado['resultadocasa'];
        $list_resultados[2] = $fetchResultado['fora'];
        $list_resultados[3] = $fetchResultado['resultadofora'];

        echo build_table_resultadosjogo($list_resultados, $emblemaCasa, $emblemaFora);


        $totalEventos = pg_affected_rows($queryEventos);
        $fetchEventos = array();
        $resultadoEventos = array();
        for ($i = 0; $i < $totalEventos; $i++) {
            $fetchEventos = pg_fetch_array($queryEventos, $i);
            $resultadoEventos[$i][0] = $fetchEventos['nome'];
            $resultadoEventos[$i][1] = $fetchEventos['clube'];
            $resultadoEventos[$i][2] = $fetchEventos['ocor'];
            $resultadoEventos[$i][3] = $fetchEventos['minut'];
        }
        echo build_table_eventosJogo($resultadoEventos);

        ?>

        <?php
        if ($_SESSION) {

            if (!isset($_POST['btn1']) && !isset($_POST['btn2'])) {
                $novolink = "Jogo.php?jogo=" . $jogoSelecionado;
                echo "</br>";
                echo "<div style='display: inline-block;'>";
                echo "<form style= 'display: inline-block; margin: 10px; widht=40vw; height: 60px;'name='myform5'  action=$novolink method='POST'>";
                echo "<input style='display: inline-block; margin: 10px;  width: 100px;' type='submit' name='btn1' id='This' value='Editar'/>";
                echo "<input style='display: inline-block; margin: 10px;  width: 100px;' type='submit' name='btn2' id='This' value='Editar'/>";
                echo "</form>";
                echo "</div>";


            } else {
                if (isset($_POST['btn1'])) {
                    $novolink = "EdicaoJogo.php";
                    echo "</br>";
                    echo "<div style='display: inline-block;'>";
                    echo "<form style= 'display: inline-block; margin: 10px; widht=40vw; height: 60px;'name='myform6'  action=$novolink method='POST'>";
                    echo "<input style='display: inline-block; background: #ffa500;margin: 10px;  width: 100px;' type='button' onclick='history.back();' name='cancel' id='This' value='Cancel'/>";
                    echo "<input style='display: inline-block; background: lightgreen; margin: 10px;  width: 100px;' type='submit' name='save' id='This' value='Save'/>";
                    echo "</form>";
                    echo "</div>";


                    $queryBuscarJogadorCasa = pg_query($conn, "select jo.nome as jognomecasa from jogador as jo
                            join equipa as e on e.id = jo.equipa_id
                            join jogo as j on j.equipa_casa = e.id
                            where j.id = $jogoSelecionado order by jognomecasa asc ");
                    $totalJogadoresCasa = pg_affected_rows($queryBuscarJogadorCasa);

                    $jogadorCasa = array();
                    $list_jogadores_casa = null;


                    echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: space-between;' name='myform7' action=$novolink method='POST'>";
                    echo "<div class='selection' style='padding: 10px; padding-bottom: 20px; margin-right: 60px;  display: inline-block; '>";
                    echo "<label style='padding: 20px';>Adicione uma ocorrencia</label></br>";

                    echo "<select style='display: inline-block;' name='jogo'>";
                    echo "<option class='selection' value='$jogoSelecionado'></option>";
                    echo "</select>";
                    echo "<select style='display: inline-block;' class='selection' name='selectjogadorcasa'>";
                    while ($jogadorCasa = pg_fetch_array($queryBuscarJogadorCasa)) {
                        $list_jogadores_casa = $jogadorCasa['jognomecasa'];
                        echo "<option class='selection' value='$list_jogadores_casa'> $list_jogadores_casa </option>";
                    }
                    echo "</select>";

                    echo "<select style='display: inline-block;' class='selection' name='selectjogadorocorrencia'>";
                    for ($i = 0; $i < count($arrayTipoOcorrencias); $i++) {
                        echo "<option class='selection' value='$arrayTipoOcorrencias[$i]'> $arrayTipoOcorrencias[$i] </option>";
                    }
                    echo "</select>";

                    echo "<input style='display: inline-block; width: 300px; height: 35px' type='text'  name='adicionarminuto' value='minuto, separado por virgulas e p(penalti)'>";
                    echo "<input style='width: 200px;' type='submit' name='submit' value='Add'>";
                    echo "</div>";

                    echo "</form>";

                } else if (isset($_POST['btn2'])) {
                    echo " INFORMACAO JOGO2 ";
                }
            }

            ?>
        <?php } ?>

        <?php
        function build_table_resultadosjogo($array, $emblemaCasa, $emblemaFora)
        {
            echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

            echo "<thead>";

            echo "<tr>";
            echo "<th>" . "Casa" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "ResultadoC" . "</th>";
            echo "<th>" . "ResultadoF" . "</th>";
            echo "<th>" . " " . "</th>";
            echo "<th>" . "Fora" . "</th>";
            echo "</tr>";

            echo "</thead>";

            echo "<tbody>";
                echo "<tr class='linha' style='height: 80px;'>";
                echo "<td>" . " " . $array[0] . " " . "</td>"; // nome casa
                echo "<td>" . "<img src= $emblemaCasa style='vertical-align:middle; height: 45px;'>" . " " . "</td>";
                echo "<td>" . " " . $array[1] . " " . "</td>"; // resultado casa
                echo "<td>" . " " . $array[3] . " " . "</td>"; // resultado fora
                echo "<td>" . "<img src= $emblemaFora style='vertical-align:middle; height: 45px;'>" . " " . "</td>";
                echo "<td>" . " " . $array[2] . " " . "</td>"; // nome fora
                echo "</tr>";
            echo "</tbody>";

            echo "</table>";
            echo "</div>";
        }

        function build_table_eventosJogo($array)
        {
            echo "<table style='border-collapse: collapse;justify-content: flex-end; width: 100%;'>";

            echo "<thead>";

            echo "<tr>";
            echo "<th>" . "Nome" . "</th>";
            echo "<th>" . "Clube " . "</th>";
            echo "<th>" . "Ocorrencia" . "</th>";
            echo "<th>" . "Minuto" . "</th>";
            echo "</tr>";

            echo "</thead>";

            echo "<tbody>";
            for ($i = 0; $i < count($array); $i++) {
                echo "<tr class='linha' style='height: 80px;'>";
                echo "<td>" . " " . $array[$i][0] . " " . "</td>";
                echo "<td>" . " " . $array[$i][1] . " " . "</td>";
                echo "<td>" . " " . $array[$i][2] . " " . "</td>";
                echo "<td>" . " " . $array[$i][3] . " " . "</td>";

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
    <br>Trabalho desenvolvido por Artem Basok e Kelvin Clark,</br>
    no âmbito da disciplina de Sistemas Informáticos, 2020</p>
</div>
</body>
</html>
