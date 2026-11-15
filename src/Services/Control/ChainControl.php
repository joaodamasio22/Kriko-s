<?php

namespace Kriko\Services\Control;

use DomainException;
use Throwable;

use Kriko\Services\Buscar\BuscarEntidade;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pagamento;

use Doctrine\ORM\EntityManager;

use Kriko\Services\Validar\ValidarMesa;
use Kriko\Services\Validar\ValidarPedido;
use Kriko\Services\Validar\ValidarProduto;
use Kriko\Services\Validar\ValidarCategoria;
use Kriko\Services\Validar\ValidarPagamento;
use Kriko\Services\Validar\ValidarItemPedido;
use Kriko\Services\Validar\ValidarAdministrador;

use Kriko\Services\ListService\Cadastrar\CadastrarMesa;
use Kriko\Services\ListService\Atualizar\AtualizarPedido;
use Kriko\Services\ListService\Atualizar\PagamentoAprovado;

use Kriko\Services\ListService\Cadastrar\CadastrarPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarProduto;
use Kriko\Services\ListService\Cadastrar\CadastrarCategoria;
use Kriko\Services\ListService\Cadastrar\CadastrarPagamento;
use Kriko\Services\ListService\Cadastrar\CadastrarItemPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarAdministrador;

class ChainControl
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    private function executar(OperacaoDTO $dto, Handler ...$handlers): OperacaoDTO
    {
        if ($handlers === []) {
            throw new DomainException('Nenhum handler informado.');
        }

        $conexao = $this->entityManager->getConnection();
        $conexao->beginTransaction();

        try {
            $resultado = $this->processar($dto, ...$handlers);

            $this->entityManager->flush();

            $conexao->commit();

            return $resultado;
        } catch (Throwable $erro) {
            $conexao->rollBack();
            $this->entityManager->clear();

            throw $erro;
        }
    }

    private function processar(OperacaoDTO $dto, Handler ...$handlers): OperacaoDTO
    {
        for ($i = 0, $total = count($handlers) - 1; $i < $total; $i++) {
            $handlers[$i]->setProximo($handlers[$i + 1]);
        }
        return $handlers[0]->processar($dto);
    }

    public function pedidoComItens(array $dados, array $itens): OperacaoDTO
    {
        if (!$itens || count($itens) > 30 || array_sum($itens) > 100) {
            throw new DomainException('Confira os itens do pedido.');
        }
        return $this->entityManager
            ->getConnection()
            ->transactional(function () use ($dados, $itens): OperacaoDTO {
                try {
                    $dto = $this->processar(
                        new OperacaoDTO($dados),
                        new ValidarPedido(),
                        new BuscarEntidade($this->entityManager, 'mesa_id'),
                        new BuscarEntidade($this->entityManager, 'admin_id'),
                        new CadastrarPedido($this->entityManager),
                    );
                    $this->entityManager
                        ->getRepository(\Kriko\Entity\Produto::class)
                        ->findBy(['id' => array_keys($itens)]);
                    foreach ($itens as $produtoId => $quantidade) {
                        $item = new OperacaoDTO(
                            ['produto_id' => $produtoId, 'quantidade' => $quantidade],
                            pedido: $dto->pedido,
                        );
                        $this->processar(
                            $item,
                            new ValidarItemPedido(),
                            new BuscarEntidade($this->entityManager, 'produto_id'),
                            new CadastrarItemPedido($this->entityManager),
                        );
                    }
                    $this->entityManager->flush();
                    return $dto;
                } catch (Throwable $erro) {
                    $this->entityManager->clear();
                    throw $erro;
                }
            });
    }

    public function categoria(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarCategoria(),
            new CadastrarCategoria($this->entityManager),
        );
    }

    public function produto(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarProduto(),
            new BuscarEntidade($this->entityManager, 'categoria_id'),
            new CadastrarProduto($this->entityManager),
        );
    }

    public function administrador(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarAdministrador(),
            new CadastrarAdministrador($this->entityManager),
        );
    }

    public function mesa(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarMesa(),
            new CadastrarMesa($this->entityManager),
        );
    }

    public function pedido(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarPedido(),
            new BuscarEntidade($this->entityManager, 'mesa_id'),
            new BuscarEntidade($this->entityManager, 'admin_id'),
            new CadastrarPedido($this->entityManager),
        );
    }

    public function itemPedido(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarItemPedido(),
            new BuscarEntidade($this->entityManager, 'pedido_id'),
            new BuscarEntidade($this->entityManager, 'produto_id'),
            new CadastrarItemPedido($this->entityManager),
        );
    }

    public function atualizarPedido(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new BuscarEntidade($this->entityManager, 'pedido_id'),
            new AtualizarPedido(),
        );
    }

    public function pagamento(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new BuscarEntidade($this->entityManager, 'pedido_id'),
            new ValidarPagamento(),
            new CadastrarPagamento($this->entityManager),
        );
    }

    public function pagamentoAprovado(array $dados): OperacaoDTO
    {
        $dto = new OperacaoDTO($dados);

        $pagamentoId = filter_var($dto->obter('pagamento_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($pagamentoId === false) {
            throw new DomainException('Identificador do pagamento inválido.');
        }

        $pagamento = $this->entityManager->find(Pagamento::class, $pagamentoId);

        if ($pagamento === null) {
            throw new DomainException('Pagamento não encontrado.');
        }

        $dto->pagamento = $pagamento;

        return $this->executar($dto, new PagamentoAprovado($this->entityManager));
    }
}
