<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarItemPedido extends Handler {
    protected function executar(OperacaoDTO $dto): void
    {
        $produtoId = filter_var($dto->obter('produto_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($produtoId === false) {
            throw new Exception('Campo produto_id deve ser inteiro positivo.');
        }

        $quantidade = filter_var($dto->obter('quantidade'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 50],
        ]);
        if ($quantidade === false) {
            throw new Exception('A quantidade deve estar entre 1 e 50.');
        }

        if ($dto->pedido === null && filter_var(
            $dto->obter('pedido_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) === false) {
            throw new Exception('Campo pedido_id deve ser inteiro positivo.');
        }
    }
}
