<?php

namespace Kriko\Services\CriandoProdutos;

use Doctrine\ORM\EntityManager;
use Kriko\Controller\UserControl;

class CriandoProdutos {
    public function __construct(private readonly EntityManager $entityManager)
    { }

    public function criarCategoria(array $dados)
    {
        (new UserControl($this->entityManager))->categoria($dados);
    }

    public function criarMesa(array $dados)
    {
        (new UserControl($this->entityManager))->mesa($dados);
    }

    public function criarAdministrador(array $dados)
    {
        (new UserControl($this->entityManager))->administrador($dados);
    }

    public function criarProduto(array $dados)
    {
        (new UserControl($this->entityManager))->produto($dados);
    }
}
