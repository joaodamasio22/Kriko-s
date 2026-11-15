<?php

namespace Kriko\Services\Buscar;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\{Administrador, Categoria, Mesa, Pedido, Produto};

final class BuscarEntidade extends Handler
{
    private const CAMPOS = [
        'admin_id' => [Administrador::class, 'administrador'],
        'categoria_id' => [Categoria::class, 'categoria'],
        'mesa_id' => [Mesa::class, 'mesa'],
        'pedido_id' => [Pedido::class, 'pedido'],
        'produto_id' => [Produto::class, 'produto'],
    ];

    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly string $campo,
    ) {}

    protected function executar(OperacaoDTO $dto): void
    {
        [$classe, $destino] = self::CAMPOS[$this->campo];
        $id = filter_var($dto->obter($this->campo), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        if ($id === false) {
            throw new DomainException("Identificador {$this->campo} inválido.");
        }

        $entidade = $this->entityManager->find($classe, $id);
        if (!$entidade) {
            throw new DomainException(ucfirst($destino) . ' não encontrado(a).');
        }
        if ($entidade instanceof Pedido) {
            $this->entityManager->refresh($entidade, LockMode::PESSIMISTIC_WRITE);
        }
        $dto->{$destino} = $entidade;
    }
}
