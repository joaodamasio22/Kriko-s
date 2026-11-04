<?php

namespace Kriko\Services\ListService\Cadastrar;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Chain\Handler;
use Kriko\Entity\Administrador;
use Kriko\Entity\Mesa;

class CadastrarAdministrador extends Handler
{
    public function __construct(private EntityManager $entity_manager)
    { }

    public function processar(?array $dados = null): bool
                                
    {
        $conn = $this->entity_manager->getConnection();
        $conn->beginTransaction();

        try{
            $admin = new Administrador();
            $admin
                ->setNome($dados['nomeAdm'])
                ->setEmail($dados['emailAdm'])
                ->setSenha($dados['senhaAdm'])
                ->setPerfil($dados['perfilAdm'])
                ->setAtivo($dados['ativoAdm'])
                ->setDataCadastro(new DateTime())
            ;

            echo "Mesa cadastrada com sucesso!\n";

            // throw new Exception("Testando Rollback\n");
            $conn->commit();

        } catch (Exception $e) {
            echo "Erro ao cadastrar mesa: " . $e->getMessage() . "\n";
            $conn->rollBack();

        }

        return parent::processar($dados);
    }
}
