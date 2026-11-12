<?php

namespace Kriko\Services\Control;

use Exception;
use Throwable;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pagamento;

use Doctrine\ORM\EntityManager;

use Kriko\Services\Buscar\BuscarMesa;
use Kriko\Services\Buscar\BuscarPedido;
use Kriko\Services\Buscar\BuscarProduto;
use Kriko\Services\Buscar\BuscarCategoria;
use Kriko\Services\Buscar\BuscarAdministrador;

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
    public function __construct(
        private readonly EntityManager $entityManager
    ) { }

    private function executar(OperacaoDTO $dto, Handler ...$handlers ): OperacaoDTO
    {
        $conexao = $this->entityManager->getConnection();
        $conexao->beginTransaction();

        try {
            $resultado = $this->processar($dto, ...$handlers);

            $this->entityManager->flush();

            $conexao->commit();

            return $resultado;

        } catch (Throwable $erro) {
            if ($conexao->getTransactionNestingLevel() > 0) {
                $conexao->rollBack();
            }

            throw $erro;
        }
    }

    private function processar(OperacaoDTO $dto, Handler ...$handlers): OperacaoDTO
    {
        if ($handlers === []) {
            throw new Exception('Nenhum handler informado.');
        }

        $total = count($handlers);
        for ($i = 0; $i < $total - 1; $i++) {
            $handlers[$i]->setProximo($handlers[$i + 1]);
        }

        return $handlers[0]->processar($dto);
    }

    public function categoria(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarCategoria(),
                new CadastrarCategoria($this->entityManager)
            );
    }

    public function produto(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarProduto(),
                new BuscarCategoria($this->entityManager),
                new CadastrarProduto($this->entityManager)
            );
    }

    public function administrador(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarAdministrador(),
                new CadastrarAdministrador($this->entityManager)
            );
    }

    public function mesa(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarMesa(),
                new CadastrarMesa($this->entityManager)
            );
    }

    public function pedido(array $dados): OperacaoDTO
    {
        return $this->executar(
            new OperacaoDTO($dados),
            new ValidarPedido(),
            new BuscarMesa($this->entityManager),
            new BuscarAdministrador($this->entityManager),
            new CadastrarPedido($this->entityManager)
        );
    }

    public function itemPedido(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarItemPedido(),
                new BuscarPedido($this->entityManager),
                new BuscarProduto($this->entityManager),
                new CadastrarItemPedido($this->entityManager)
            );
    }

    public function realizarPedido(array $dados): OperacaoDTO
    {
        $itens = $dados['itens'] ?? null;
        if (!is_array($itens) || $itens === []) {
            throw new Exception('O pedido deve possuir ao menos um item.');
        }
        if (count($itens) > 30) {
            throw new Exception('O pedido pode possuir no máximo 30 itens distintos.');
        }

        $quantidadesPorProduto = [];
        foreach ($itens as $item) {
            if (!is_array($item)) {
                throw new Exception('Item do pedido inválido.');
            }

            $produtoId = filter_var($item['produto_id'] ?? null, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);
            $quantidade = filter_var($item['quantidade'] ?? null, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 50],
            ]);
            if ($produtoId === false || $quantidade === false) {
                throw new Exception('Produto ou quantidade inválida no pedido.');
            }

            $quantidadesPorProduto[$produtoId] = ($quantidadesPorProduto[$produtoId] ?? 0) + $quantidade;
            if ($quantidadesPorProduto[$produtoId] > 50) {
                throw new Exception('O limite por produto é 50 unidades.');
            }
        }

        if (array_sum($quantidadesPorProduto) > 100) {
            throw new Exception('O limite por pedido é 100 unidades.');
        }

        $conexao = $this->entityManager->getConnection();
        $conexao->beginTransaction();

        try {
            $pedidoDto = $this->processar(
                new OperacaoDTO($dados),
                new ValidarPedido(),
                new BuscarMesa($this->entityManager),
                new BuscarAdministrador($this->entityManager),
                new CadastrarPedido($this->entityManager)
            );

            foreach ($itens as $item) {
                $itemDto = new OperacaoDTO($item);
                $itemDto->pedido = $pedidoDto->pedido;
                $this->processar(
                    $itemDto,
                    new ValidarItemPedido(),
                    new BuscarProduto($this->entityManager),
                    new CadastrarItemPedido($this->entityManager)
                );
            }

            $atualizacaoDto = new OperacaoDTO([
                'forma_pagamento' => $dados['forma_pagamento'] ?? null,
                'statusPagamento' => 'PENDENTE',
            ]);
            $atualizacaoDto->pedido = $pedidoDto->pedido;
            $this->processar(
                $atualizacaoDto,
                new AtualizarPedido($this->entityManager)
            );

            $pagamentoDto = new OperacaoDTO(['status' => 'PENDENTE']);
            $pagamentoDto->pedido = $pedidoDto->pedido;
            $this->processar(
                $pagamentoDto,
                new ValidarPagamento(),
                new CadastrarPagamento($this->entityManager)
            );
            $pedidoDto->pagamento = $pagamentoDto->pagamento;

            $this->entityManager->flush();
            $conexao->commit();

            return $pedidoDto;
        } catch (Throwable $erro) {
            if ($conexao->getTransactionNestingLevel() > 0) {
                $conexao->rollBack();
            }

            throw $erro;
        }
    }

    public function atualizarPedido(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new BuscarPedido($this->entityManager),
                new AtualizarPedido($this->entityManager)
            );
    }





    public function pagamento(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new BuscarPedido($this->entityManager),
                new ValidarPagamento(),
                new CadastrarPagamento($this->entityManager)
            );
    }

    public function registrarPagamento(array $dados): OperacaoDTO
    {
        $dados['statusPagamento'] = 'PENDENTE';

        return $this->executar(
            new OperacaoDTO($dados),
            new BuscarPedido($this->entityManager),
            new AtualizarPedido($this->entityManager),
            new ValidarPagamento(),
            new CadastrarPagamento($this->entityManager)
        );
    }





    public function pagamentoAprovado(array $dados): OperacaoDTO
    {
        $dto = new OperacaoDTO($dados);

        $pagamentoId = filter_var($dto->obter('pagamento_id'),FILTER_VALIDATE_INT,['options' => ['min_range' => 1]]);

        if ($pagamentoId === false) {
            throw new Exception('Identificador do pagamento inválido.');
        }

        $pagamento = $this->entityManager->find(Pagamento::class, $pagamentoId);

        if ($pagamento === null) {
            throw new Exception('Pagamento não encontrado.');
        }

        $dto->pagamento = $pagamento;

        return $this->executar(
                $dto,
                new PagamentoAprovado($this->entityManager)
            );
    }
}
