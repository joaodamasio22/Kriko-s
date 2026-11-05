<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarAdministrador extends Handler {
    protected function executar(OperacaoDTO $dto): void
    {
        $nome = $dto->obter('nome');
        $email = $dto->obter('email');
        $senha = $dto->obter('senha');
        $perfil = $dto->obter('perfil');

        if (!is_string($nome) || trim($nome) === '' || mb_strlen($nome) > 100) {
            throw new Exception('Nome do administrador inválido.');
        }

        if (!is_string($email) || mb_strlen($email) > 150) {
            throw new Exception('E-mail do administrador inválido.');
        }

        if (!is_string($senha) || strlen($senha) < 8) {
            throw new Exception('Senha deve ter ao menos 8 caracteres.');
        }

        if (!in_array($perfil, ['MASTER', 'GARCOM', 'COZINHA'], true)) {
            throw new Exception('Perfil inválido.');
        }
    }
}
