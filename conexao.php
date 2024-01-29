<?php
$servername = "localhost";
$username = "wesleySc";
$password = "Jw240104*";
$dbname = "guardadados";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram recebidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nomePaciente = $_POST["nome"];
    $dataConsulta = $_POST["data"];
    $horarioConsulta = $_POST["horario"];

    // Prepara e executa a consulta SQL para inserção
    $sql = "INSERT INTO consultas (nome_paciente, data_consulta, horario_consulta) VALUES ('$nomePaciente', '$dataConsulta', '$horarioConsulta')";

    if ($conn->query($sql) === TRUE) {
        echo "Consulta marcada com sucesso!";
    } else {
        echo "Erro ao marcar a consulta: " . $conn->error;
    }
}

// Fecha a conexão
$conn->close();
?>
