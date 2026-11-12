<?php

namespace Kriko\Chain;

use Kriko\DTO\OperacaoDTO;

abstract class Handler
{
    protected ?Handler $proximo = null;

    public function setProximo(Handler $handler): Handler
    {
        $this->proximo = $handler;

        return $handler;
    }

    public function processar(OperacaoDTO $dto): OperacaoDTO
    {
        $this->executar($dto);

        if ($this->proximo !== null) {
            return $this->proximo->processar($dto);
        }

        return $dto;
    }

    abstract protected function executar(OperacaoDTO $dto): void;
}
