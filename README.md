# Kriko-s
Projeto interdisciplinar de banco de dados. O frontend PHP e Bootstrap está em `public/` e usa o banco do backend.

## Rodar localmente

Use PHP 8.4.1 ou superior com PDO MySQL, Composer e MySQL ou MariaDB.

1. Execute `composer install` na raiz do repositório.
2. Para um banco novo, importe `src/Repository/Schema/SchemaDb.sql`, que cria o banco `krikos_db`. Se já houver um banco com o schema, use a conexão dele.
3. Ajuste as variáveis do banco conforme o ambiente e rode `php -S 127.0.0.1:8765 -t public`. A raiz pública do servidor deve apontar para `public/`.

| Variável | Padrão |
| --- | --- |
| `KRIKOS_DB_HOST` | `127.0.0.1` |
| `KRIKOS_DB_PORT` | `3306` |
| `KRIKOS_DB_NAME` | `krikos_db` |
| `KRIKOS_DB_USER` | `root` |
| `KRIKOS_DB_PASSWORD` | vazio |

## Cardápios

Cadastre os produtos ativos nas categorias `Pastel` ou `Pastéis` e `Churros`. Para exibir porções, use `Porções de pastéis` e `Porções de churros`. O preço é armazenado em centavos. A fileira “Mais vendidos” soma as quantidades dos pedidos pagos que não foram cancelados.

Defina `KRIKOS_WHATSAPP` com país, DDD e número em dígitos para ativar o botão de WhatsApp. `KRIKOS_INSTAGRAM` ativa o botão do perfil. A home usa os recortes transparentes `public/imagens/pastel-recortado.png` e `public/imagens/churros-recortado.png`; as fotos originais estão preservadas na mesma pasta.

Na tela de cardápio, cada produto pode ser adicionado ao pedido. O envio usa as rotinas `pedido`, `itemPedido`, `atualizarPedido` e `pagamento` de `UserControl`; a equipe confirma o pagamento na mesa, sem cobrança online. Para associar pedidos a um atendente específico, defina `KRIKOS_WEB_ORDER_ADMIN_ID` com o ID de um administrador ativo. Se houver somente um administrador ativo, ele é identificado automaticamente. O cliente informa o número de uma mesa ativa.

O painel interno fica em `public/painel.php`. Entre com um administrador ativo. Ele permite cadastrar categorias, produtos, mesas e acessos; abrir pedidos e adicionar itens; atualizar pedidos; e registrar/aprovar pagamentos. As permissões seguem os perfis `MASTER`, `GARCOM` e `COZINHA`. Senhas antigas em texto são convertidas para hash no primeiro login; novos acessos já são gravados com hash.
