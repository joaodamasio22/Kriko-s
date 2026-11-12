<?php

namespace Kriko\Services\Auth;

use Doctrine\ORM\EntityManager;
use Kriko\Entity\Administrador;
use RuntimeException;

final class AutenticarAdministrador
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function autenticar(string $email, string $senha): Administrador
    {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        if ($email === false || $senha === '') {
            throw new RuntimeException('E-mail ou senha inválidos.');
        }

        $administrador = $this->entityManager
            ->getRepository(Administrador::class)
            ->findOneBy(['email' => $email, 'ativo' => 'S']);

        if ($administrador === null || !$this->senhaValida($administrador, $senha)) {
            throw new RuntimeException('E-mail ou senha inválidos.');
        }

        $senhaArmazenada = $administrador->getSenha();
        if (($this->informacoesHash($senhaArmazenada)['algo'] ?? null) === null) {
            $administrador->setSenha(password_hash($senha, PASSWORD_DEFAULT));
            $this->entityManager->flush();
        }

        return $administrador;
    }

    private function senhaValida(Administrador $administrador, string $senha): bool
    {
        $senhaArmazenada = $administrador->getSenha();
        $usaHash = ($this->informacoesHash($senhaArmazenada)['algo'] ?? null) !== null;

        return $usaHash
            ? password_verify($senha, $senhaArmazenada)
            : hash_equals($senhaArmazenada, $senha);
    }

    private function informacoesHash(string $senha): array
    {
        return password_get_info($senha);
    }
}
