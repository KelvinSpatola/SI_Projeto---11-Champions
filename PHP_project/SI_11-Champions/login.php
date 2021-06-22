<!DOCTYPE html>
<html>
<header>
    <meta charset="UTF-8">
</header>

<head>
    <link rel="icon" type="image/png" href="img/favicon.png"/>
    <title>11 Champions Area Reservada</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="scripts.js"></script>
</head>

<body class="bodyindex; background">
<form style="width: 500px;" action="loginUser.php" method="post">
    <h2>AREA RESERVADA</h2>
    <?php if (isset($_GET['error'])) { ?>
        <p class="error"><?php echo $_GET['error']; ?></p>
    <?php } ?>
    <label>Nome de Utilizador</label>
    <input type="text" name="username" placeholder="Nome de Utilizador"><br>

    <label>Palavra-passe</label>
    <input type="password" name="password" placeholder="Palavra-passe"><br>

    <button class="buttonBlue" type="submit">Entrar</button>
    <button class="buttonBlue" onclick="window.location.href = 'home.php" style="float: left; margin-left: 10px;"><a href="javascript:history.go(-1)">Go Back</a></button>
</form>
</body>
</html>