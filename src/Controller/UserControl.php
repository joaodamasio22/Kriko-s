<?php

namespace Kriko\Controller;

use Doctrine\ORM\EntityManager;
use Kriko\Services\Control\ChainControl;
use Kriko\DTO\OperacaoDTO;

class UserControl
{
    private ChainControl $chainControl;

    public function __construct(private EntityManager $entityManager)
    {
        $this->chainControl = new ChainControl($this->entityManager);
    }

    public function categoria(array $dados): OperacaoDTO
    {
        return $this->chainControl->categoria($dados);
    }

    public function produto(array $dados): OperacaoDTO
    {
        return $this->chainControl->produto($dados);
    }

    public function mesa(array $dados): OperacaoDTO
    {
        return $this->chainControl->mesa($dados);
    }

    public function administrador(array $dados): OperacaoDTO
    {
        return $this->chainControl->administrador($dados);
    }

    public function pedido(array $dados): OperacaoDTO
    {
        return $this->chainControl->pedido($dados);
    }

    public function itemPedido(array $dados): OperacaoDTO
    {
        return $this->chainControl->itemPedido($dados);
    }

    public function atualizarPedido(array $dados): OperacaoDTO
    {
        return $this->chainControl->atualizarPedido($dados);
    }

    public function pagamento(array $dados): OperacaoDTO
    {
        return $this->chainControl->pagamento($dados);
    }

    public function pagamentoAprovado(array $dados): OperacaoDTO
    {
        return $this->chainControl->pagamentoAprovado($dados);
    }
}
