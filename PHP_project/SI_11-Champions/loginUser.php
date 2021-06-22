<?php
session_start();

$str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
$conn = pg_connect($str) or die("Erro na ligacao");

if (isset($_POST['username']) && isset($_POST['password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['username']);
    $pass = validate($_POST['password']);

    if (!empty($uname) && !empty($pass)) { // verifica se foi preenchido os campos
        $queryUser = "SELECT * FROM utilizador WHERE username='$uname' AND password='$pass'";
        $result = pg_query($conn, $queryUser);
        $row = pg_fetch_array($result);

        if ($row['username'] === $uname && $row['password'] === $pass) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['username'] = $row['username'];

            header("Location: home.php");
            exit();
        } else {
            header("Location: login.php?error=Nome de utilizador ou palavra passe invalidas");
            // exit();
        }

    } else {
        header("Location: login.php?error=Nome de utilizador ou palavra passe invalidas");
        // exit();
    }
} else {
    header("<script>window.history.back(-2)</script>");
    exit();
}