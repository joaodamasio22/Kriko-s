<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarItemPedido extends Handler {
    protected function executar(OperacaoDTO $dto): void
    {
        foreach (['pedido_id', 'produto_id', 'quantidade'] as $campo) {
            if (filter_var($dto->obter($campo), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new Exception("Campo {$campo} deve ser inteiro positivo.");
            }
        }
    }
}
