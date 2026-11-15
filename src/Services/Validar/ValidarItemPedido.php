<?php

namespace Kriko\Services\Validar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarItemPedido extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        foreach (
            $dto->pedido ? ['produto_id', 'quantidade'] : ['pedido_id', 'produto_id', 'quantidade']
            as $campo
        ) {
            if (
                filter_var($dto->obter($campo), FILTER_VALIDATE_INT, [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => $campo === 'quantidade' ? 50 : PHP_INT_MAX,
                    ],
                ]) === false
            ) {
                throw new DomainException("Campo {$campo} deve ser inteiro positivo.");
            }
        }
    }
}
