<?php

// incluir o autoload do composer para carregar automaticamente as classe utilizadas
require_once __DIR__ . '/../vendor/autoload.php';

// incluir o arquivo com as variáveis
require_once __DIR__ . '/../config/config.php';

session_start();

//importar as classes Lovcadore e Auth
use Services\{Locadora, Auth};

//importar as classes Carro e Moto
use Models\{Carro, Moto};

//verificar se o usuário está logado
if(!Auth::verificarLogin()){
    header('Location: login.php');
    exit;
}

//sair do login, logout
if (isset($_GET['logout'])){
    (new Auth())->logout();
    header('Location: login.php');
    exit;
}
// criar uma instancia de classa locadora
$locadora = new Locadora();

$mensagem = '';

$usuario = Auth::getUsuario();

// verificar os dados do formulário via POST
if($SERVER['REQUEST_METHOD'])