<?php

namespace Kriko\Services\ListService;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Kriko\Entity\Administrador;
use Kriko\Entity\Mesa;

class CadastrarAdministrador
{
    public function __construct(private EntityManager $entity_manager)
    { }

    public function cadastrarMesa(string $nome,
                                  string $email,
                                  string $senha,
                                  string $perfil,
                                  string $ativo
                                )
                                
    {
        $conn = $this->entity_manager->getConnection();
        $conn->beginTransaction();

        try{
            $admin = new Administrador();
            $admin
                ->setNome($nome)
                ->setEmail($email)
                ->setSenha($senha)
                ->setPerfil($perfil)
                ->setAtivo($ativo)
                ->setDataCadastro(new DateTime())
            ;

            echo "Mesa cadastrada com sucesso!\n";

            // throw new Exception("Testando Rollback\n");
            $conn->commit();

        } catch (Exception $e) {
            echo "Erro ao cadastrar mesa: " . $e->getMessage() . "\n";
            $conn->rollBack();

        }
    }
}
