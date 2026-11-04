<?php

namespace Kriko\Services\Validar;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Exception;

class ValidarProduto extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $preco = $dto->obter('preco');
        $categoriaId = $dto->obter('categoria_id');

        if (!is_string($nome) || $nome === '') {
            throw new Exception('O nome do produto é obrigatório.');
        }

        if (mb_strlen($nome) > 100) {
            throw new Exception('O nome do produto é muito grande.');
        }

        if (filter_var($categoriaId, FILTER_VALIDATE_INT,['options' => ['min_range' => 1]]) === false) {
            throw new Exception('Categoria inválida.');
        }

        if ((float) $preco <= 0) {
            throw new Exception('O preço deve ser maior que zero.');
        }
    }
}
