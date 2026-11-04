<?php

namespace Kriko\Services\Control;

use Kriko\Chain\Handler;

// Código de encademaento de responsabilidades(sequência de passos que só 
// continua se o passo anterior for True)

class ChainControl extends Handler {
    public function handler()
    {
        $dados = [
            // Categoria
            'nomeCategoria' => 'Pastel',
            'descricaoCategoria' => 'Melhor Pastel da Cidade',

            // Administrador
            'nomeAdm' => 'Joao',
            'emailAdm' => 'Joao@teste.com',
            'perfilAdm' => 'MASTER',
        ];
    }
}
