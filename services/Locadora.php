<?php
namespace Services;

use Models\{Veiculo, Carro, Moto};

// Classe para gerenciar a Locação
class Locadora {
    private array $veiculos = [];

    public function __construct()
    {
        $this->carregarVeiculo();
    }

    private function carregarVeiculo(): void {
        if (file_exists(ARQUIVO_JSON)){

            // Decodifica o arquivo JSON
            $dados = json_decode(file_get_contents(ARQUIVO_JSON),true);

            foreach ($dados as $dado){

                if($dado['tipo']=== 'Carro'){
                    $veiculo = new Carro($dado['modelo'], $dado['placa']);
                } else {
                    $veiculo = new Moto($dado['modelo'], $dado['placa']);
                }
                $veiculo->setDisponivel($dado['disponivel']);

                $this->veiculos[] = $veiculo;
            }
        }
    }

    // Salvar veículos
    private function salvarVeiculos(): void{
        $dados = [];

        foreach($this->veiculos as $veiculo){
            $dados[] = [
                'tipo' => ($veiculo instanceof Carro) ? 'Carro' :'Moto', 'modelo' => $veiculo -> getModelo(),
                'placa' => $veiculo -> getPlaca(),
                'disponivel' => $veiculo -> isDisponivel()
            ];
        }

            $dir = dirname(ARQUIVO_JSON);

            if (!is_dir($dir)){
                mkdir($dir,0777, true);
            }

            file_put_contents(ARQUIVO_JSON, json_encode($dados, JSON_PRETTY_PRINT));
    }

        // Adicionar novo veículo 
        public function adicionarVeiculo (Veiculo $veiculo): void {
            $this->veiculos[] = $veiculo;
            $this->salvarVeiculos();
        }

        //Remover veículo
        public function deletarVeiculo(string $modelo, string $placa): string{

            foreach ($this->veiculos as $key => $veiculo){

                // Verifica se o modelo e a placa correspondem
                if($veiculo->getModelo() === $modelo && $veiculo->getPlaca() ===$placa){
                    // Remove o veículo do array
                    unset($this->veiculos[$key]);

                    // Reogarnizar os indíces
                    $this->veiculos = array_values($this->veiculos);

                    // Salvar o novo estado
                    $this->SalvarVeiculos();
                    return "Veículo '{$modelo}' removido com sucesso!";
                }
            }
            return "Veículo não encontrado!";
        }

        // Alugar veículo por n dias
        public function alugarVeiculo(string $modelo, int $dias = 1): string{

            // Percorre a lista de veículos
            foreach($this->veiculos as $veiculo){

                if($veiculo->getModelo() === $modelo && $veiculo->isDisponivel()){

                    // Calcular valor do aluguel
                    $valorAluguel = $veiculo->calcularAluguel($dias);

                    // Marcar com alugado/indisponível
                    $mensagem = $veiculo->alugar();

                    $this->salvarVeiculos();

                    return $mensagem . "Valor do aluguel: R$" . number_format($valorAluguel, 2,',','.');
                }
            }
            return "Veículo não disponível";
        }

        // Devolver veículo

        public function devolverVeiculo(string $modelo) :string{

            // Percorrer a lista
            foreach($this->veiculos as $veiculo){

                if($veiculo->getModelo() === $modelo && !$veiculo->isDisponivel()){

                    // Disponibilizar o Veículo
                    $mensagem = $veiculo->devolver();

                    $this->salvarVeiculos();
                    return $mensagem;
                }
            }
            return "Veiculo já disponível ou não encontrado.";
        }

        // Retorna a lista de veículos

        public function listarVeiculos():array{
            return $this->veiculos;
        }

        // Calcular previsão do valor
        public function calcularPrevisaoAluguel(string $tipo, int $dias): float {
            if($tipo = 'Carro'){
                return (new Carro('','')) -> calcularAluguel($dias);
            }
            return (new Moto('','')) -> calcularAluguel($dias);

        }   
}