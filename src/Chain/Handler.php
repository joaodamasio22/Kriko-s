<?php

namespace Kriko\Chain;

abstract class Handler
{
    protected ?Handler $proximo = null;

    public function setProximo(Handler $proximo): Handler
    {
        $this->proximo = $proximo;

        return $proximo;
    }

    public function processar(?array $dados = null): bool
    {
        if(!$this->proximo){
            return true;

        }

        return $this->proximo->processar($dados);
    }
}
