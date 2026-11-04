<?php

namespace Kriko\Services\Control;

use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Services\Validar\ValidarCategoria;
use Kriko\Services\Validar\ValidarProduto;
use Kriko\Services\Buscar\BuscarCategoria;
use Kriko\Services\ListService\Atualizar\AtualizarPedido;
use Kriko\Services\ListService\Atualizar\PagamentoAprovado;
use Kriko\Services\ListService\Cadastrar\CadastrarAdministrador;
use Kriko\Services\ListService\Cadastrar\CadastrarCategoria;
use Kriko\Services\ListService\Cadastrar\CadastrarItemPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarMesa;
use Kriko\Services\ListService\Cadastrar\CadastrarPagamento;
use Kriko\Services\ListService\Cadastrar\CadastrarPedido;
use Kriko\Services\ListService\Cadastrar\CadastrarProduto;

class ChainControl
{
    public function __construct(private EntityManager $entityManager)
    { }

    private function executar(OperacaoDTO $dto, Handler ...$handlers): OperacaoDTO
    {
        if (empty($handlers)) {
            throw new Exception('Nenhum handler informado.');
        }

        for ($i = 0; $i < count($handlers) - 1; $i++) {
            $handlers[$i]->setProximo($handlers[$i + 1]);
        }

        $conexao = $this->entityManager->getConnection();
        $conexao->beginTransaction();

        try {
            $resultado = $handlers[0]->processar($dto);
            $this->entityManager->flush();

            $conexao->commit();

            return $resultado;

        } catch (Exception $erro) {
            $conexao->rollBack();
            throw $erro;

        }
    }

    public function categoria(array $dados): OperacaoDTO
    {
        $dto = new OperacaoDTO($dados);

        return $this->executar($dto,
                    new ValidarCategoria(),
                    new CadastrarCategoria($this->entityManager)
                );
    }

    public function produto(array $dados): OperacaoDTO
    {
        $dto = new OperacaoDTO($dados);

        return $this->executar($dto,
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
                new VerificarEmail($this->entityManager),
                new CadastrarAdministrador($this->entityManager)
            );
}

public function mesa(array $dados): OperacaoDTO
{
    return $this->executar(
                new OperacaoDTO($dados),
                new ValidarMesa(),
                new VerificarNumeroMesa($this->entityManager),
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
    return $this->executar(
                new OperacaoDTO($dados),
                new ValidarAtualizacaoPedido(),
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
                new VerificarValor($this->entityManager),
                new CadastrarPagamento($this->entityManager)
            );
    }

    public function pagamentoAprovado(array $dados): OperacaoDTO
    {
        return $this->executar(
                new OperacaoDTO($dados),
                new ValidarAprovacao(),
                new BuscarPagamento($this->entityManager),
                new PagamentoAprovado($this->entityManager)
            );
    }
}
