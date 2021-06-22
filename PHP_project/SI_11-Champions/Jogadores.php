<?php
session_start();
?>

<?php
$str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
$conn = pg_connect($str) or die("Erro na ligacao");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Jogadores
        <?php
        $equipaSelecionada = $_POST['equipa'];
        echo "$equipaSelecionada";
        ?>
    </title>
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
                <input type="radio" name="tab" id="navBtn4">
                <input type="radio" name="tab" id="navBtn5" checked>

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
    <div class="btcontent">
        <button onclick="goBack()"style="float: left; margin-left: 10px;">Go Back</button>
        <div><button class="logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
<div class="pageinfo">
        <?php
        // POST
        $equipaSelecionada = $_POST['equipa'];

        // QUERYS
        $emblemaEquipaQuery = pg_query($conn, "select equipa.emblema_link from equipa where equipa.nome = '$equipaSelecionada'") or die;
        $equipamentoEquipaQuery = pg_query($conn, "select equipa.equipamento_link from equipa where equipa.nome = '$equipaSelecionada'") or die;
        $formacaoEquipaQuery = pg_query($conn, "select equipa.formacao_link from equipa where equipa.nome = '$equipaSelecionada'") or die;
        $jogadoresSelecionados = pg_query($conn, "select jogador.nome, jogador.posicao, jogador.foto_link from jogador join equipa on jogador.equipa_id = equipa.id where equipa.nome = '$equipaSelecionada'") or die;

        echo "<h1 class='selection'>$equipaSelecionada<h1>";
        $emblemaArr = pg_fetch_array($emblemaEquipaQuery);
        $equipamentoArr = pg_fetch_array($equipamentoEquipaQuery);
        $formacaoArr = pg_fetch_array($formacaoEquipaQuery);
        $emblemaEquipa = $emblemaArr['emblema_link'];
        $equipamentoEquipa = $equipamentoArr['equipamento_link'];
        $formacaoEquipa = $formacaoArr['formacao_link'];
        echo "<img class='selection' style=' height: 140px;' src= '$emblemaEquipa'>";
        echo "<img class='selection' style=' height: 140px;' src= '$equipamentoEquipa'>";


        echo "<h2 class='titulo''>Lista de jogadores:</h2>";
        $numJogadores = pg_affected_rows($jogadoresSelecionados);
        for ($i = 0; $i < $numJogadores; $i++) {
            $equipas = pg_fetch_array($jogadoresSelecionados);
            $list_jogadores[$i][0] = $equipas['posicao'];
            $list_jogadores[$i][1] = $equipas['foto_link'];
            $list_jogadores[$i][2] = $equipas['nome'];
        }

        echo build_table($list_jogadores);

        ?>

        <?php
        function build_table($array)
        {
            echo "<table  class='selection' style='justify-content: center; width: 100%;'>";
            echo "<th class='selection'>" . "POS" . "</th>";
            echo "<th class='selection'>" . "FOTO" . "</th>";
            echo "<th class='selection'>" . "NOME" . "</th>";

            // Table body
            for ($i = 0; $i < count($array); $i++) {
                echo "<tr class='selection'>";
                for ($j = 0; $j < count($array[$i]); $j++) {
                    if ($j == 0) echo "<td class='selection' style='width: 10vw;'>" . " " . $array[$i][0] . " " . "</td>";
                    if ($j == 1) {
                        $imgSrc = $array[$i][1];
                        echo "<td class='selection'>" . "<img src= $imgSrc style='vertical-align:middle'>" . " " . "</td>";
                    }
                    if ($j == 2) echo "<td class='selection' style='width: 40vw;'>" . $array[$i][2] . " " . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";

        }
        echo "<div>";
        echo "<img class='selection' style=';' src= '$formacaoEquipa'>";
        echo "</div>";
        ?>
</div>
</div>
<div class="footer">
    <br>Trabalho desenvolvido por Artem Basok e Kelvin Clark,</br>
    no âmbito da disciplina de Sistemas Informáticos, 2020</p>
</div>
</body>
</html>