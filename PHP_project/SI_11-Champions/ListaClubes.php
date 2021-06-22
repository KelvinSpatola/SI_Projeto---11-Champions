<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Lista de Clubes</title>
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
        <button class="buttonBlue" onclick="goBack()" style="float: left; margin-left: 10px;">Go Back</button>
        <div>
            <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    <div class="pageinfo">
        <?php
        $str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
        $conn = pg_connect($str) or die("Erro na ligacao");

        $queryEquipas = pg_query($conn, "select nome from equipa") or die;
        $queryEquipasDropdown = pg_query($conn, "select nome from equipa") or die;
        $queryJogadores = pg_query($conn, "select jogador.nome, jogador.posicao, equipa.nome from jogador join equipa on jogador.equipa_id = equipa.id") or die;

        $jogadoresSelecionados = pg_query($conn, "select jogador.nome from jogador join equipa on jogador.equipa_id = equipa.id") or die;

        $numEquipas = pg_affected_rows($queryEquipas);
        $numJogadores = pg_affected_rows($queryJogadores);

        echo "<h3 style='alignment: center; justify-content: center; border: 2px solid rgb(231, 231, 231); padding: 10px; background: rgba(255, 255, 255, 0.80); margin-bottom: 10px;'>Número de equipas desta temporada: $numEquipas </h3>";
        echo "<h3 style='alignment: center; justify-content: center; border: 2px solid rgb(231, 231, 231); border-bottom: 0px solid rgb(231, 231, 231); padding-top: 10px; padding-bottom: 10px;margin-bottom: 0; background: rgba(255, 255, 255, 0.80);'>Lista de equipas:</h3>";

        //Estrai resultados do query linha a linha e guarda num array
        for ($i = 0; $i < $numEquipas; $i++) {
            $equipas = pg_fetch_array($queryEquipas);
            $list_equipas[$i][0] = $equipas['nome'];
        }

        // apresenta valores do array ordenado
        echo "<ul class='selection' style='margin-bottom: 2px; justify-content: center; align-items: center;'>";
        for ($i = 0; $i < $numEquipas; $i++) {
            echo "<li class='selection' style='list-style-type: none; width: 20vw; height: 30px; padding-top: 0px; margin-left: 30%;margin-bottom: 2px;'>" . $list_equipas[$i][0] . "</li>";

            // echo "<button class="logout" onclick="window.location.href='logout.php'">Logout</button>";


        }
        echo "</ul>";

        echo "<h3 style='alignment: center; justify-content: center; border: 2px solid rgb(231, 231, 231); border-bottom: 0px solid rgb(231, 231, 231); margin-top: 10px; padding-top: 10px; padding-bottom: 10px;margin-bottom: 0; background: rgba(255, 255, 255, 0.80);'>Selecione uma equipa para ver os seus jogadores:</h3>";

        echo "<form class='selection' style='padding-top: 10px; margin-top: 0px; justify-content: center;' name='myform' action='Jogadores.php' method='POST'>";
        echo "<select class='selection' name='equipa'>";
        while ($rows = pg_fetch_array($queryEquipasDropdown)) {
            $nome2 = $rows['nome'];
            echo "<option class='selection' value='$nome2'> $nome2 </option>";
        }
        echo "</select>";
        //echo "<imput type='hidden' name='equipa' value='teste'>";
        echo "<input style='width: 200px;' type='submit' name='submit' value='ver equipa'>";
        echo "</form>";

        echo "<br/>";

        pg_close($conn);
        ?>
    </div>
</div>

<div class="footer">
    <br>Trabalho desenvolvido por Artem Basok e Kelvin Clark,</br>
    no âmbito da disciplina de Sistemas Informáticos, 2020</p>
</div>

</div>
</div>
<div class="footer">
    <br>Trabalho desenvolvido por Artem Basok e Kelvin Clark,</br>
    no âmbito da disciplina de Sistemas Informáticos, 2020</p>
</div>
</body>
</html>

