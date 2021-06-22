<?php
$str = "dbname=DB_Campeonato user=postgres password=postgres host=localhost port=5432";
$conn = pg_connect($str) or die("Erro na ligacao");

$queryEquipas = pg_query($conn, "select nome from equipa") or die;
$queryEquipasDropdown = pg_query($conn, "select nome from equipa") or die;
$queryJogadores = pg_query($conn, "select jogador.nome, jogador.posicao, equipa.nome from jogador join equipa on jogador.equipa_id = equipa.id") or die;

$numEquipas = pg_affected_rows($queryEquipas);
$numJogadores = pg_affected_rows($queryJogadores);

echo "<h2>Número de equipas desta temporada: $numEquipas </h2>";
echo "<h2>Lista de equipas:</h2>";

//Estrai resultados do query linha a linha e guarda num array
for ($i = 0; $i < $numEquipas; $i++) {
    $equipas = pg_fetch_array($queryEquipas);
    $list_equipas[$i][0] = $equipas['nome'];
}

// apresenta valores do array ordenado
echo "<ul>";
for ($i = 0; $i < $numEquipas; $i++) {
    echo "<li>" . $list_equipas[$i][0] . "</li>";
}
echo "</ul>";

echo "<h2>Selecione uma equipa para ver seus jogadores</h2>";
echo "<br/>";

echo "<select name='equipa'>";
while ($rows = pg_fetch_array($queryEquipasDropdown)) {
    $nome = $rows['nome'];
    echo "<option value='nome'> $nome </option>";
}
echo "</select>";
echo "<input type='submit' name='submit' value='ver equipa'>";
echo "<br/>";

if (isset($_POST['submit'])) {
    //$getEquipa = $_GET['nome'];
    //echo "equipa selecionada: " . $getEquipa;
    echo $_POST['nome'];
}

pg_close($conn);
?>


