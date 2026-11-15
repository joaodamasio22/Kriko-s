<?php

namespace Kriko\Services\Validar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarPedido extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        foreach (['mesa_id', 'admin_id'] as $campo) {
            if (
                filter_var($dto->obter($campo), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1],
                ]) === false
            ) {
                throw new DomainException("Identificador {$campo} inválido.");
            }
        }

        $status = $dto->obter('status', 'ABERTO');

        if ($status !== 'ABERTO') {
            throw new DomainException('Novos pedidos devem começar abertos.');
        }

        $observacao = $dto->obter('observacao');

        if ($observacao !== null && (!is_string($observacao) || mb_strlen($observacao) > 500)) {
            throw new DomainException('Observação inválida.');
        }
    }
}
