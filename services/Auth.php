<?php
// Define espaço para organização do código
namespace Services;

class Auth{
    private array $usuarios = [];

    // Método construtor
    public function __construct(){
        $this->carregarUsuarios();
    }

    // Método para carregar usuários do arquivo JSON
    
}