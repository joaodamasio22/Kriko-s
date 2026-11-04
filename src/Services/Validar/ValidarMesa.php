<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarMesa extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $numero = filter_var($dto->obter('numero_mesa'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $descricao = $dto->obter('descricao');
        $ativo = $dto->obter('ativo', '1');

        if ($numero === false) {
            throw new Exception('Número da mesa deve ser inteiro positivo.');
        }

        if ($descricao !== null && (!is_string($descricao) || mb_strlen($descricao) > 100)) {
            throw new Exception('Descrição da mesa inválida.');
        }

        if (!in_array($ativo, ['0', '1', 0, 1, false, true], true)) {
            throw new Exception('Campo ativo inválido.');
        }
    }
}
