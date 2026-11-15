<?php

namespace Kriko\Services\Validar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarAdministrador extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $email = $dto->obter('email');
        $senha = $dto->obter('senha');
        $perfil = $dto->obter('perfil');

        if (!is_string($nome) || trim($nome) === '' || mb_strlen($nome) > 100) {
            throw new DomainException('Nome do administrador inválido.');
        }

        if (
            !is_string($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            mb_strlen($email) > 150
        ) {
            throw new DomainException('E-mail do administrador inválido.');
        }

        if (!is_string($senha) || strlen($senha) < 8) {
            throw new DomainException('Senha deve ter ao menos 8 caracteres.');
        }

        if (!in_array($perfil, ['MASTER', 'GARCOM', 'COZINHA'], true)) {
            throw new DomainException('Perfil inválido.');
        }
        if (!in_array($dto->obter('ativo', 'S'), ['S', 'N'], true)) {
            throw new DomainException('Campo ativo inválido.');
        }
    }
}
