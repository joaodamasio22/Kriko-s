<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarProduto extends Handler {
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $descricao = $dto->obter('descricao');
        $categoria = filter_var($dto->obter('categoria_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $preco = filter_var($dto->obter('preco'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $ativo = $dto->obter('ativo', 'S');

        if ($categoria === false) {
            throw new Exception('Categoria inválida.');
        }

        if ($preco === false) {
            throw new Exception('Preço do produto inválido.');
        }

        if (!is_string($nome) || $nome === '' || mb_strlen($nome) > 100) {
            throw new Exception('Nome do produto inválido.');
        }

        if ($descricao !== null && !is_string($descricao)) {
            throw new Exception('Descrição do produto inválida.');
        }

        if (!in_array($ativo, ['N', 'S'], true)) {
            throw new Exception('Campo ativo inválido.');
        }
    }
}
