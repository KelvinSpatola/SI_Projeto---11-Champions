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
                <input type="radio" name="tab" id="navBtn3" checked>
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
        $db_campeonato_conn = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
        $conn = pg_connect($db_campeonato_conn) or die("Erro na ligacao");

        $queryListaTemporadasDropdown = pg_query($conn, "SELECT DISTINCT t.ano FROM temporada as t ORDER BY t.ano DESC") or die;

        $arrayOrderBySELECT = array("Ranking ASC", "Ranking DESC", "Nome ASC", "Nome DESC", "Vitorias ASC", "Vitorias DESC", "Empates ASC", "Empates DESC", "Derrotas ASC", "Derrotas DESC", "Golos Marcados ASC", "Golos Marcados DESC", "Golos Sofridos ASC", "Golos Sofridos DESC");

        $arrayQuerySELECT = array(
            " ORDER BY s.ranking ASC",
            " ORDER BY s.ranking DESC",
            " ORDER BY e.nome ASC",
            " ORDER BY e.nome DESC",
            " ORDER BY s.vitorias ASC",
            " ORDER BY s.vitorias DESC",
            " ORDER BY s.empates ASC",
            " ORDER BY s.empates DESC",
            " ORDER BY s.derrotas ASC",
            " ORDER BY s.derrotas DESC",
            " ORDER BY s.golos_marcados ASC",
            " ORDER BY s.golos_marcados DESC",
            " ORDER BY s.golos_sofridos ASC",
            " ORDER BY s.golos_sofridos DESC");

        $queryPreffix = "SELECT DISTINCT s.ranking AS pos, e.emblema_link, e.nome AS equipa,
        s.pontos AS pts, s.vitorias AS v, s.empates AS emp, s.derrotas AS d,
        s.golos_marcados AS gm, s.golos_sofridos AS gs
        FROM status_equipa AS s
        JOIN equipa AS e ON e.id = s.equipa_id
        JOIN temporada AS t ON t.id = s.temporada_id
        WHERE t.ano = ";


        if (isset($_POST['temporada']) && isset($_POST['orderByClassClubes'])) {

          $ano = $_POST['temporada'];
            $querySuffix = $_POST['orderByClassClubes'];
            $strConcatenada = $queryPreffix . $ano . $querySuffix;

            $queryСlassificacoes = pg_query($conn, $strConcatenada) or die;

            $numResultado = pg_affected_rows($queryСlassificacoes); // 9

            $clube = array();
            $list_clubes = null;
            for ($i = 0; $i < $numResultado; $i++) { // $i => n_clubes que vai até
                $clube = pg_fetch_array($queryСlassificacoes, $i);
                // para cada [clube] cria 11 colunas
                $list_clubes[$i][0] = $clube['pos'];
                $list_clubes[$i][1] = $clube['emblema_link'];
                $list_clubes[$i][2] = $clube['equipa'];
                $list_clubes[$i][3] = $clube['pts'];
                $list_clubes[$i][4] = $clube['v'];
                $list_clubes[$i][5] = $clube['emp'];
                $list_clubes[$i][6] = $clube['d'];
                $list_clubes[$i][7] = $clube['gm'];
                $list_clubes[$i][8] = $clube['gs'];
            }

            echo build_table($list_clubes);

            echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: center;' name='myform' action='Classificacoes.php' method='POST'>";
            echo "<select class='selection' name='temporada'>";
            while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                $temporada_ano = $rows['ano'];
                echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
            }
            echo "</select>";
            echo "<select class='selection' name='orderByClassClubes'>";
            for ($i = 0; $i < count($arrayQuerySELECT); $i++) {
                echo "<option class='selection' value='$arrayQuerySELECT[$i]'> $arrayOrderBySELECT[$i] </option>";
            }
            echo "</select>";
            echo "<input style='width: 200px;' type='submit' name='submit' value='Ver os Jogos da Jornada'>";
            echo "</form>";

        } else {
            $str = $queryPreffix . " '2020' " . $arrayQuerySELECT[0];

            $queryPagInicial = pg_query($conn, $str) or die;

            $totalRows = pg_affected_rows($queryPagInicial); // 9

            $clube = array();
            $list_clubes = null;
            for ($i = 0; $i < $totalRows; $i++) { // $i => n_clubes que vai até
                $clube = pg_fetch_array($queryPagInicial, $i);
                // para cada [clube] cria 11 colunas
                $list_clubes[$i][0] = $clube['pos'];
                $list_clubes[$i][1] = $clube['emblema_link'];
                $list_clubes[$i][2] = $clube['equipa'];
                $list_clubes[$i][3] = $clube['pts'];
                $list_clubes[$i][4] = $clube['v'];
                $list_clubes[$i][5] = $clube['emp'];
                $list_clubes[$i][6] = $clube['d'];
                $list_clubes[$i][7] = $clube['gm'];
                $list_clubes[$i][8] = $clube['gs'];
            }

            echo build_table($list_clubes);

            echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: center;' name='myform2' action='Classificacoes.php' method='POST'>";
            echo "<select class='selection' name='temporada'>";
            while ($rows = pg_fetch_array($queryListaTemporadasDropdown)) {
                $temporada_ano = $rows['ano'];
                echo "<option class='selection' value='$temporada_ano'> $temporada_ano </option>";
            }
            echo "</select>";
            echo "<select class='selection' name='orderByClassClubes'>";
            for ($i = 0; $i < count($arrayQuerySELECT); $i++) {
                echo "<option class='selection' value='$arrayQuerySELECT[$i]'> $arrayOrderBySELECT[$i] </option>";
            }
            echo "</select>";
            echo "<input style='width: 200px;' type='submit' name='submit' value='Ver as classificacoes'>";
            echo "</form>";

        }
        pg_close($conn);
        ?>
        <?php

        function build_table($array)
        {
            echo "<div class='selection'>";
            echo '<form action ="classificacoes.php" method="POST">';

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

    </div>
</div>
<div class="footer">
    Trabalho desenvolvido por Artem Basok e Kelvin Clark,<br>
    no âmbito da disciplina de Sistemas Informáticos, 2020
</div>
</body>
</html>
