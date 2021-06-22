<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Jornadas</title>
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
    <div class="pageinfo">
        <?php
        $str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
        $conn = pg_connect($str) or die("Erro na ligacao");

        $queryListaJornadas = pg_query($conn, "SELECT DISTINCT j.n_jornada, t.ano FROM jogo AS j JOIN temporada as t ON t.id = j.temporada_id where t.ano = 2020 ORDER BY n_jornada ASC") or die;
        $numJornadas = pg_affected_rows($queryListaJornadas);

        $queryListaTemporadasDropdown = pg_query($conn, "SELECT DISTINCT t.ano FROM temporada as t ORDER BY t.ano ASC") or die;
        $ano_temporada = 2020;

        $queryListaJornadasDropdown = pg_query($conn, "SELECT DISTINCT j.n_jornada FROM jogo AS j JOIN temporada as t ON t.id = j.temporada_id WHERE t.ano ='$ano_temporada'ORDER BY n_jornada ASC") or die;

        //Estrai resultados do query linha a linha e guarda num array
        for ($i = 0; $i < $numJornadas; $i++) {
            $jornadas = pg_fetch_array($queryListaJornadas);
            $list_jornadas[$i][0] = $jornadas['n_jornada'];
        }

        echo "<h3 class='titulo'>Número de Jornadas desta temporada: $numJornadas </h3>";
        // apresenta valores do array ordenado
        echo "<ul class='selection' style='margin-bottom: 2px; justify-content: center; align-items: center;'>";
        for ($i = 0; $i < $numJornadas; $i++) {
            echo "<li class='selection' style='list-style-type: none; width: 20vw; height: 30px; padding-top: 0px; margin-left: 30%;margin-bottom: 2px;'>" . "Jornada Nº" . $list_jornadas[$i][0] . "</li>";
            // echo "<button class="logout" onclick="window.location.href='logout.php'">Logout</button>";
        }
        echo "</ul>";

        //echo "<h3 class='tituloextra'>Temporada selecionada: $ano_temporada</h3>";
        echo "<h3 class='titulo'>Selecione uma jornada para visualizar os seus jogos:</h3>";

        echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: center;' name='myform' action='ListaJogos.php' method='POST'>";

            echo "<select class='selection' name='temporada'>";
            while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                $temporada_ano = $rows['ano'];
                echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
            }
            echo "</select>";


            echo "<select class='selection' name='jornada_selecionada'>";
            while ($rows = pg_fetch_array($queryListaJornadasDropdown)) {
                $n_jornada = $rows['n_jornada'];
                echo "<option class='selection' value='$n_jornada'> $n_jornada </option>";
            }
            echo "</select>";

            echo "<input style='width: 200px;' type='submit' name='submit' value='Ver os Jogos da Jornada'>";
        echo "</form>";

        echo "<br/>";

        pg_close($conn);

        ?>
    </div>
</div>
<div class="footer">
    Trabalho desenvolvido por Artem Basok e Kelvin Clark,<br>
    no âmbito da disciplina de Sistemas Informáticos, 2020
</div>
</body>
</html>



