<?php

namespace Kriko\Services\Validar;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Exception;

class ValidarCategoria extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $descricao = $dto->obter('descricao');

        if (!is_string($nome) || $nome === '') {
            throw new Exception('O nome da categoria é obrigatório.');
        }

        if (mb_strlen($nome) > 50) {
            throw new Exception('O nome da categoria deve possuir no máximo 50 caracteres.');
        }

        if ($descricao !== null && (!is_string($descricao) || mb_strlen($descricao) > 255)) {
            throw new Exception('Descrição inválida.');
        }
    }
}
