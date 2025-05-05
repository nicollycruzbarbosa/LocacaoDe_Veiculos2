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

            $dir = dirname(ARQUIVO_JSON);

            if (!is_dir($dir)){
                mkdir($dir,0777, true);
            }

            file_put_contents(ARQUIVO_JSON, json_encode($dados, JSON_PRETTY_PRINT));
        }

        // adicionar novo veículo 
        public function adicionarVeiculo(Veiculos $veiculo): void{
            $this->veiculos[] = $veiculo;
            $this->salvarVericulos();
        }

        // remover veículo
        public function deletarVeiculo(string $modelo, string $placa): string{

            foreach ($this->veiculos as $key => $veiculo){

                // verifica se a placa do carro e moto, e o modelo correspondem
                if($veiculo->getModelo() === $modelo && $veiculo->getPlaca() === $placa){
                    unset($this->veiculos[$key]);
                }

                //reorganizar os indices
                $this->veiculos = array_values($this->veiculos);

                //salvar o novo estado
                $this->salvarVeiculos();
                return "Veículo '{$modelo}' removido com sucesso!";

            
            }
        }
        return "Veículo não encontrado";
}

                //alugar veiculos por aguns dias
                public function alugarVeiculo(string $modelo, int $dias = 1): string{
                    //percorre a lista de veiculoss
                    foreach($this->veiculos as $veiculo){

                        if($veiculo->getodelo() === $modelo && $veiculo->isDisponivel()){

                            //calcular o valor do aluguel
                            $valorAluguel = $veiculo->calcularAluguel($dias);

                            // marcar como alugadooo
                            $mensagem = $veiculo->alugar();

                            $this->salvarVeiculos();

                            return $mensagem . "Valor do aluguel: R$" . number_format($valorAluguel, 2,',','.');

                        }
                    }

                    return "Veículo não disponível";
                }

                //devolver veículo
}

