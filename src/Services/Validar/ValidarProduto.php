<?php

namespace Kriko\Services\Validar;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use InvalidArgumentException;

class ValidarProduto extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $preco = $dto->obter('preco');
        $categoriaId = $dto->obter('categoria_id');

        if (!is_string($nome) || trim($nome) === '') {
            throw new InvalidArgumentException(
                'O nome do produto é obrigatório.'
            );
        }

        if (mb_strlen($nome) > 100) {
            throw new InvalidArgumentException(
                'O nome do produto é muito grande.'
            );
        }

        if (
            filter_var(
                $categoriaId,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            ) === false
        ) {
            throw new InvalidArgumentException(
                'Categoria inválida.'
            );
        }

        if (
            !is_string($preco) ||
            !preg_match('/^\d{1,8}\.\d{2}$/', $preco)
        ) {
            throw new InvalidArgumentException(
                'O preço deve estar no formato 12.50.'
            );
        }

        if ((float) $preco <= 0) {
            throw new InvalidArgumentException(
                'O preço deve ser maior que zero.'
            );
        }
    }
}
