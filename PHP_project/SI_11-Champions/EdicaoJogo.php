<?php
session_start();
?>
<!DOCTYPE html>
<html lang=pt>
<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>Lista de equipas da temporada</title>
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
                <input type="radio" name="tab" id="navBtn1" checked>
                <input type="radio" name="tab" id="navBtn2">
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
    <?php

    if($_SESSION){



        if (isset($_POST['selectjogadorcasa']) && isset($_POST['selectjogadorocorrencia']) && isset($_POST['adicionarminuto'])) {

            $escrevejogador = $_POST['selectjogadorcasa'];
            $escreveocorrencia = $_POST['selectjogadorocorrencia'];
            $escreveminuto = $_POST['adicionarminuto'];
            $jogoSelecionado = $_POST['jogo'];


            echo "<p> Deseja inserir estes dados na no jogo ? </p><br>";
            echo $escrevejogador . " " . $escreveocorrencia . " " . $escreveminuto . " " . $jogoSelecionado;
        }
    }
    ?>

</div>
<div class="footer">
    <br>Trabalho desenvolvido por Artem Basok e Kelvin Clark,</br>
    no âmbito da disciplina de Sistemas Informáticos, 2020</p>
</div>
</body>
</html>