<?php

namespace Kriko\Services\Validar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarMesa extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $numero = filter_var($dto->obter('numeroMesa'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $descricao = $dto->obter('descricao');
        $ativo = $dto->obter('ativo', 'S');

        if ($numero === false) {
            throw new DomainException('Número da mesa deve ser inteiro positivo.');
        }

        if ($descricao !== null && (!is_string($descricao) || mb_strlen($descricao) > 100)) {
            throw new DomainException('Descrição da mesa inválida.');
        }

        if (!in_array($ativo, ['N', 'S'], true)) {
            throw new DomainException('Campo ativo inválido.');
        }
    }
}
