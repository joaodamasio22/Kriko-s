<?php

namespace Kriko\Controller;

use Doctrine\ORM\EntityManager;
use Kriko\Entity\Categoria;
use Kriko\Entity\Produto;
use Kriko\Services\ListService\CadastrarAdministrador;
use Kriko\Services\ListService\CadastrarCategoria;
use Kriko\Services\ListService\CadastrarMesa;
use Kriko\Services\ListService\CadastrarProduto;

class UserControl
{
    public function __construct(private EntityManager $entityManager)
    { }

    public function index()
    {
        // VERIFICAÇÃO PARA QUAL METODO IRÁ EXECUTAR
    }

    public function Categoria()
    {
        (new CadastrarCategoria($this->entityManager))->cadastrarCategoria("Pastel", "Melhor Pastel da cidade");
        
    }

    public function Produto()
    {
        (new CadastrarProduto($this->entityManager))->cadastrarProduto($categoria, "Pastel de Carne", "Pastel de carne com bastante recheio", 1490, "S");
    }

    public function Mesa()
    {
        (new CadastrarMesa($this->entityManager))->cadastrarMesa(1, "Mesa 1", "S");
    }

    public function Administrador()
    {
        (new CadastrarAdministrador($this->entityManager))->cadastrarAdministrador('Joao', 'joao@gmail.com', '123456', 'MASTER', 'S');
    }

}
