<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarCategoria extends Handler {
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $descricao = $dto->obter('descricao');
        
        if (!is_string($nome) || trim($nome) === '' || mb_strlen($nome) > 50) {
            throw new Exception('Nome da categoria obrigatório (até 50 caracteres).');
        }

        if ($descricao !== null && (!is_string($descricao) || mb_strlen($descricao) > 255)) {
            throw new Exception('Descrição da categoria inválida (até 255 caracteres).');
        }
    }
}
