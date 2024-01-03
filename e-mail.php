<?php

$host = "localhost";
$user = "wesleySc";
$senha = "Jw240104*";
$banco = "webserver";

$db = new mysqli($host, $user, $senha, $banco);

// Verificar a conexão
if ($db->connect_error) {
    die("Conexão falhou: " . $db->connect_error);
}

function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF possui 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/^(\d)\1*$/', $cpf)) {
        return false;
    }

    // Calcula os dígitos verificadores
    for ($i = 9, $j = 10, $soma = 0; $i > 0; $i--, $j--) {
        $soma += $cpf[$i - 1] * $j;
    }
    $resto = $soma % 11;
    $dv1 = $resto < 2 ? 0 : 11 - $resto;

    for ($i = 10, $j = 11, $soma = 0; $i > 0; $i--, $j--) {
        $soma += $cpf[$i - 1] * $j;
    }
    $resto = $soma % 11;
    $dv2 = $resto < 2 ? 0 : 11 - $resto;

    // Verifica se os dígitos verificadores estão corretos
    if ($cpf[9] != $dv1 || $cpf[10] != $dv2) {
        return false;
    }

    return true;
}

function validarSenha($senha) {
    // Adicione outras regras de validação de senha conforme necessário
    return strlen($senha) >= 8;
}

$mensagens = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['cpf']) && !empty($_POST['cpf']) &&
        isset($_POST['password']) && !empty($_POST['password'])
    ) {
        $cpf = addslashes($_POST['cpf']);
        $senha = password_hash(addslashes($_POST['password']), PASSWORD_DEFAULT);

        // Validar CPF
        if (validarCPF($cpf)) {
            // Validar senha
            if (validarSenha($senha)) {
                // Mensagem de CPF e senha válidos
                $mensagens[] = "CPF e senha válidos!";

                // Preparar uma instrução SQL para inserção
                $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, cpf_cnpj) VALUES (?, ?, ?, ?)");

                // Verificar se a preparação foi bem-sucedida
                if ($stmt) {
                    // Atribuir valores aos parâmetros da declaração
                    $nome = 'teste';  // Substitua com o nome real
                    $email = 'email@teste.com';  // Substitua com o e-mail real

                    // Vincular parâmetros à declaração
                    $stmt->bind_param('ssss', $nome, $email, $senha, $cpf);

                    // Executar a declaração
                    if ($stmt->execute()) {
                        // Mensagem de sucesso na inserção
                        $mensagens[] = "Inserção bem-sucedida!";
                    } else {
                        // Mensagem de erro na inserção
                        $mensagens[] = "Erro na inserção: " . $stmt->error;
                    }

                    // Fechar a declaração
                    $stmt->close();
                } else {
                    // Mensagem de erro na preparação da declaração
                    $mensagens[] = "Falha na preparação da declaração: " . $db->error;
                }
            } else {
                // Mensagem de senha inválida
                $mensagens[] = "Senha inválida. A senha deve atender aos critérios desejados.";
            }
        } else {
            // Mensagem de CPF inválido
            $mensagens[] = "CPF inválido. Por favor, insira um CPF válido.";
        }
    } else {
        // Mensagem de campos do formulário não preenchidos
        $mensagens[] = "Por favor, preencha todos os campos do formulário.";
    }
}

// Fechar a conexão
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Cadastro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Formulário de Cadastro</h1>

<form method="post" action="">
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF" required>

    <label for="password">Senha:</label>
    <input type="password" id="password" name="password" placeholder="Digite sua senha" required>

    <button type="submit">Cadastrar</button>
</form>

<?php
// Exibir todas as mensagens
foreach ($mensagens as $mensagem) {
    echo $mensagem . "<br>";
}
?>

</body>
</html>
