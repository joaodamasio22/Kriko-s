<?php

namespace Kriko\Services;

use Doctrine\ORM\EntityManager;
use DomainException;
use Kriko\Entity\Administrador;

final class Autenticacao
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function entrar(string $email, string $senha): array
    {
        $admin = filter_var($email, FILTER_VALIDATE_EMAIL)
            ? $this->entityManager
                ->getRepository(Administrador::class)
                ->findOneBy(['email' => $email, 'ativo' => 'S'])
            : null;
        if (!$admin || $senha === '') {
            throw new DomainException('E-mail ou senha inválidos.');
        }

        $armazenada = $admin->getSenha();
        $usaHash = password_get_info($armazenada)['algo'] !== null;
        $valida = $usaHash
            ? password_verify($senha, $armazenada)
            : hash_equals($armazenada, $senha);
        if (!$valida) {
            throw new DomainException('E-mail ou senha inválidos.');
        }
        if (!$usaHash || password_needs_rehash($armazenada, PASSWORD_DEFAULT)) {
            $admin->setSenha(password_hash($senha, PASSWORD_DEFAULT));
            $this->entityManager->flush();
        }
        return [
            'id' => $admin->getId(),
            'nome' => $admin->getNome(),
            'perfil' => $admin->getPerfil(),
        ];
    }
}
