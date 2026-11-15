# Kriko-s
Projeto interdisciplinar de banco de dados. O frontend PHP e Bootstrap está em `public/` e usa o banco do backend.

## Rodar localmente

No Windows, execute `iniciar.bat` e abra <http://127.0.0.1:8765>.
Mantenha o terminal aberto e use `Ctrl+C` para encerrar. Para usar outra porta,
execute `powershell -NoProfile -ExecutionPolicy Bypass -File .\iniciar.ps1 -Port 8766`.
O servidor usa a conexão configurada em `bootstrap.php` e serve a pasta `public/`.

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

### Acesso da equipe

O carrinho mantém pastéis e churros no mesmo pedido ao navegar entre os cardápios,
na mesma aba. Mesa e observações também são preservadas. O envio usa
uma requisição assíncrona; o carrinho só é limpo após a confirmação do servidor.

Para verificar esse fluxo, execute `node --test tests/cart.test.cjs`.
O teste `php tests/pedido.integration.php` verifica itens, total e pagamento no
banco configurado e desfaz os registros criados ao terminar.

Ao abrir a raiz do site, o usuário é direcionado para o login em `painel.php`.
O perfil cadastrado para o e-mail define a tela após entrar:

- `GARCOM`: início em `index.php`, com acesso aos cardápios. Os pedidos ficam associados ao garçom conectado.
- `MASTER`: cadastros de categorias, produtos e mesas em `master.php`.
- `COZINHA`: fila em `cozinha.php`, com itens, observações e ações para iniciar o preparo e marcar como pronto.

A cozinha exibe pedidos abertos, em preparação e prontos, ordenados do mais antigo ao mais recente.
Use **Atualizar fila** para consultar novos pedidos. As páginas exigem o perfil correspondente.

### Recebimento presencial

Ao enviar o pedido, a forma de pagamento fica a definir e o status fica pendente.
A forma de pagamento é escolhida apenas na confirmação do recebimento.

O link **Pedidos e pagamentos**, disponível para `GARCOM` e `MASTER`, abre `pedidos.php`.
Depois que a cozinha marcar o pedido como **Pronto**, clique em **Confirmar recebimento**,
confira mesa e valor, selecione a forma de pagamento efetivamente recebida e confirme o
recebimento integral. O sistema registra o pagamento como **Pago**, com data, horário e
identificação do responsável. Em seguida, use **Finalizar pedido** para encerrar o atendimento.
Não é possível finalizar sem pagamento confirmado. A cozinha não confirma pagamentos.
Esse fluxo usa as tabelas existentes, sem alteração do schema.

Cadastre os produtos ativos nas categorias `Pastel` ou `Pastéis` e `Churros`. Para exibir porções, use `Porções de pastéis` e `Porções de churros`. O preço é armazenado em centavos. A fileira “Mais vendidos” soma as quantidades dos pedidos pagos que não foram cancelados.

Defina `KRIKOS_WHATSAPP` com país, DDD e número em dígitos para ativar o botão de WhatsApp. `KRIKOS_INSTAGRAM` ativa o botão do perfil. A home usa os recortes transparentes `public/imagens/pastel-recortado.png` e `public/imagens/churros-recortado.png`; as fotos originais estão preservadas na mesma pasta.

Na tela de cardápio, cada produto pode ser adicionado ao pedido. O envio usa as rotinas `pedido`, `itemPedido`, `atualizarPedido` e `pagamento` de `UserControl`; a equipe confirma o pagamento na mesa, sem cobrança online. Para associar pedidos a um atendente específico, defina `KRIKOS_WEB_ORDER_ADMIN_ID` com o ID de um administrador ativo. Se houver somente um administrador ativo, ele é identificado automaticamente. O cliente informa o número de uma mesa ativa.

O painel interno fica em `public/painel.php`. Entre com um administrador ativo. Ele permite cadastrar categorias, produtos, mesas e acessos; abrir pedidos e adicionar itens; atualizar pedidos; e registrar/aprovar pagamentos. As permissões seguem os perfis `MASTER`, `GARCOM` e `COZINHA`. Senhas antigas em texto são convertidas para hash no primeiro login; novos acessos já são gravados com hash.

A tela de recuperação de acesso usa `KRIKOS_WHATSAPP` como contato de suporte. Como alternativa, defina `KRIKOS_SUPPORT_EMAIL`. Sem uma dessas variáveis, a tela orienta o usuário a procurar o administrador responsável.
