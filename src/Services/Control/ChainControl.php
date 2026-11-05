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

        if ($handlers === []) {
            throw new Exception('Nenhum handler informado.');
        }

        $total = count($handlers);

        for ($i = 0; $i < $total - 1; $i++) {
            $handlers[$i]->setProximo(
                $handlers[$i + 1]
            );
        }

        $conexao = $this->entityManager->getConnection();
        $conexao->beginTransaction();

        try {
            $resultado = $handlers[0]->processar($dto);

            $this->entityManager->flush();

            $conexao->commit();

            return $resultado;

        } catch (Throwable $erro) {
            $conexao->rollBack();

            throw $erro;
        }
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

    public function atualizarPedido(array $dados): OperacaoDTO
    {
        $dto = new OperacaoDTO($dados);

        $pedidoId = filter_var($dto->obter('pedido_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($pedidoId === false) {
            throw new Exception('Identificador do pedido inválido.');
        }

        return $this->executar(
                $dto,
                new BuscarPedido($this->entityManager),
                new AtualizarPedido($this->entityManager)
            );
    }

    public function pagamento(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarPagamento(),
                new BuscarPedido($this->entityManager),
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
