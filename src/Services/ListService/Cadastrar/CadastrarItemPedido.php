<?php

namespace Kriko\Services\ListService\Cadastrar;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\ItemPedido;

class CadastrarItemPedido extends Handler {
    public function __construct(private EntityManager $entityManager)
    { }

    public function executar(OperacaoDTO $dto): void
    {
        if ($dto->pedido === null) {
            throw new Exception('Pedido não cadastrado, tente novamente.');
        }

        if (!in_array($dto->pedido->getStatus(), ['ABERTO', 'EM_PREPARACAO'], true)) {
            throw new Exception('Não é permitido adicionar itens a este pedido.');
        }

        $quantidade = (int) $dto->obter('quantidade');
        $totalUnidades = $quantidade;
        $unidadesDoProduto = $quantidade;
        foreach ($dto->pedido->getItens() as $itemExistente) {
            $totalUnidades += $itemExistente->getQuantidade();
            if ($itemExistente->getProduto()?->getId() === $dto->produto->getId()) {
                $unidadesDoProduto += $itemExistente->getQuantidade();
            }
        }
        if ($totalUnidades > 100 || $unidadesDoProduto > 50) {
            throw new Exception('O item ultrapassa os limites de quantidade do pedido.');
        }

        $precoUnitario = $dto->produto->getPreco();
        $itemPedido = new ItemPedido();
        $itemPedido
            ->setPedido($dto->pedido)
            ->setProduto($dto->produto)
            ->setQuantidade($quantidade)
            ->setPrecoUnitario($precoUnitario)
        ;

        $this->entityManager->persist($itemPedido);
        $dto->pedido->adicionarItem($itemPedido);
        $dto->itemPedido = $itemPedido;

        $valorTotal = $dto->pedido->getValorTotal();
        $valorItem = $quantidade * $precoUnitario;
        $dto->pedido->setValorTotal($valorTotal + $valorItem);
    }
}
