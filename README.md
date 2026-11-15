# Kriko’s — Pastéis e Churros

Sistema de atendimento com cardápios, pedidos por mesa, fila da cozinha e registro de pagamentos presenciais. Desenvolvido em PHP, Doctrine ORM, MySQL e Bootstrap.

## 1. O que instalar

| Ferramenta | Requisito | Uso |
| --- | --- | --- |
| PHP | 8.4.1 ou superior | Executar o sistema |
| Composer | 2.x | Instalar as dependências PHP |
| MySQL | 8.0 ou superior, ou MariaDB compatível com o schema | Armazenar os dados |
| Git | Opcional para quem baixar o ZIP | Clonar e atualizar o projeto |
| Node.js | 20 ou superior, opcional | Executar os testes do carrinho |

Instale o PHP pelo site oficial do PHP, o Composer pelo site do Composer e o MySQL pelo instalador do MySQL. No Windows, adicione as pastas dos executáveis ao `PATH` e abra um novo terminal depois da instalação.
O PHP precisa das extensões `pdo_mysql`, `mbstring`, `dom` e `SimpleXML`. Para conferir o PHP que o terminal está usando, execute:

```text
php -v
php --ini
php -m
composer --version
mysql --version
```

Se uma extensão estiver desabilitada, edite o `php.ini` indicado por `php --ini`. No Windows, habilite as extensões disponíveis removendo o `;` no início das linhas correspondentes, por exemplo `extension=pdo_mysql` e `extension=mbstring`. Algumas extensões já vêm habilitadas na distribuição do PHP.

Não é necessário instalar Apache nem executar `npm install` para usar o sistema. O Bootstrap e os arquivos do frontend já estão em `public/assets/`.

## 2. Baixar o projeto e instalar as dependências

Clone o repositório, substituindo o endereço abaixo pelo endereço deste projeto:

```text
git clone <URL_DO_REPOSITORIO> Kriko-s
cd Kriko-s
composer install
composer check-platform-reqs
```

Se preferir baixar o ZIP, extraia os arquivos, abra o terminal na pasta que contém `composer.json` e execute os dois comandos do Composer.

Use `composer install` para instalar as versões registradas em `composer.lock`. A pasta `vendor/` será criada automaticamente. O arquivo `composer.lock` deve continuar no repositório para que outras pessoas instalem as mesmas versões.

## 3. Criar o banco

Inicie o serviço do MySQL ou MariaDB antes de continuar. No Windows, use o aplicativo **Serviços**; o nome do serviço depende da instalação, como `MySQL80`.

Na raiz do projeto, abra o cliente do MySQL:

```text
mysql -u root -p
```

Digite a senha definida na instalação. Dentro do console do MySQL, execute:

```sql
SOURCE src/Repository/Schema/SchemaDb.sql;
SHOW TABLES FROM krikos_db;
```

O script cria o banco `krikos_db` e suas tabelas. Execute essa importação uma única vez em um ambiente novo. Se as tabelas já existirem, utilize o banco existente; o script não é uma migração nem uma restauração de dados.

Também é possível abrir e executar `src/Repository/Schema/SchemaDb.sql` no MySQL Workbench ou importar o arquivo pelo phpMyAdmin.

O usuário usado na importação precisa de permissão para criar o banco e as tabelas. Para usar outro nome de banco, ajuste as instruções `CREATE DATABASE` e `USE` do script antes da importação e configure `KRIKOS_DB_NAME` com esse mesmo nome.

## 4. Configurar a conexão

A configuração fica em `bootstrap.php` e lê as seguintes variáveis de ambiente:

| Variável | Valor padrão |
| --- | --- |
| `KRIKOS_DB_HOST` | `127.0.0.1` |
| `KRIKOS_DB_PORT` | `3306` |
| `KRIKOS_DB_NAME` | `krikos_db` |
| `KRIKOS_DB_USER` | `root` |
| `KRIKOS_DB_PASSWORD` | Senha vazia |

Defina os valores no mesmo terminal em que vai executar o sistema. Use o usuário e a senha do seu banco.

### Windows — PowerShell

```powershell
$env:KRIKOS_DB_HOST = '127.0.0.1'
$env:KRIKOS_DB_PORT = '3306'
$env:KRIKOS_DB_NAME = 'krikos_db'
$env:KRIKOS_DB_USER = 'root'
$env:KRIKOS_DB_PASSWORD = 'SUA_SENHA_DO_MYSQL'
```

### Linux e macOS — Bash ou Zsh

```bash
export KRIKOS_DB_HOST='127.0.0.1'
export KRIKOS_DB_PORT='3306'
export KRIKOS_DB_NAME='krikos_db'
export KRIKOS_DB_USER='root'
export KRIKOS_DB_PASSWORD='SUA_SENHA_DO_MYSQL'
```

Essas configurações valem para o terminal atual e os processos iniciados por ele. Ao abrir outro terminal, configure as variáveis novamente ou cadastre-as nas configurações de ambiente do sistema operacional.

**O projeto não carrega arquivos `.env` automaticamente.** Criar esse arquivo não configura a conexão. Não coloque senhas pessoais no código nem envie suas configurações locais para o Git.

## 5. Criar o primeiro acesso Master

O banco novo não contém usuários, produtos nem mesas. Para cadastrar o primeiro usuário, gere um hash de senha no terminal:

```text
php -r "echo password_hash('TroqueEstaSenha123!', PASSWORD_DEFAULT), PHP_EOL;"
```

Substitua `TroqueEstaSenha123!` pela senha que você vai usar, com pelo menos oito caracteres. Copie o hash completo exibido.

No cliente do MySQL ou no Workbench, selecione o banco configurado e execute o SQL abaixo. Substitua o nome, o e-mail e `COLE_AQUI_O_HASH` pelos seus dados; mantenha o hash entre aspas simples.

```sql
USE krikos_db;

INSERT INTO administradores (nome, email, senha, perfil, ativo)
VALUES ('Administrador', 'admin@example.com', 'COLE_AQUI_O_HASH', 'MASTER', 'S');
```

O login usa o e-mail informado no cadastro e a senha original, não o hash. Esse procedimento só é necessário para o primeiro acesso; depois, o Master pode cadastrar os demais usuários pelo sistema.

## 6. Iniciar o sistema

Execute os comandos na raiz do projeto, com as variáveis do banco já configuradas.

### Windows

```powershell
.\iniciar.bat
```

O arquivo inicia o servidor na porta `8765`. Para escolher outra porta:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\iniciar.ps1 -Port 8766
```

### Qualquer sistema com PHP instalado

```text
php -S 127.0.0.1:8765 -t public
```

Abra **http://127.0.0.1:8765** no navegador. A página inicial direciona para o login. Se escolher outra porta, use a mesma porta no endereço.

Mantenha o terminal aberto durante o uso. Para encerrar o servidor, pressione `Ctrl+C`. O MySQL também precisa continuar em execução.

A raiz pública é a pasta `public/`. O servidor embutido do PHP serve para desenvolvimento local; uma hospedagem deve configurar seu servidor web para essa pasta.

## 7. Configurar os cadastros e usar o sistema

Entre com o perfil `MASTER` e cadastre:

1. Categorias: `Pastel` ou `Pastéis`, e `Churros`. Para porções, use `Porções de pastéis` e `Porções de churros`.
2. Produtos nas categorias correspondentes, com status ativo. Informe o preço em reais no formulário; o banco armazena centavos inteiros.
3. Mesas ativas, com números únicos.
4. Acessos da equipe na seção **Acessos**, escolhendo o perfil adequado.

| Perfil | Tela inicial | Funções |
| --- | --- | --- |
| `MASTER` | `master.php` | Cadastrar categorias, produtos, mesas e acessos; receber e finalizar pedidos |
| `GARCOM` | `index.php` | Consultar cardápios, enviar pedidos por mesa, receber e finalizar pedidos |
| `COZINHA` | `cozinha.php` | Consultar a fila, iniciar o preparo e marcar pedidos como prontos |

O garçom adiciona produtos ao carrinho, informa uma mesa ativa e envia o pedido. Pastéis e churros podem estar no mesmo pedido; o carrinho, a mesa e as observações são preservados ao trocar de cardápio na mesma aba.

Na cozinha, use **Atualizar fila** para consultar novos pedidos. Depois de marcar um pedido como **Pronto**, o garçom ou Master abre **Pedidos e pagamentos**, confirma o valor recebido e escolhe a forma de pagamento. Após a confirmação, fica disponível a ação **Finalizar pedido**.

O sistema registra recebimentos presenciais. Selecionar Pix ou cartão não realiza uma cobrança online. A seção **Mais vendidos** considera as quantidades de pedidos pagos que não foram cancelados.

## 8. Contatos opcionais

As variáveis abaixo ativam os contatos do cardápio e da recuperação de acesso:

| Variável | Formato | Uso |
| --- | --- | --- |
| `KRIKOS_WHATSAPP` | País, DDD e número, apenas dígitos | Botão de WhatsApp e contato de suporte |
| `KRIKOS_INSTAGRAM` | Nome do perfil | Botão do Instagram |
| `KRIKOS_SUPPORT_EMAIL` | E-mail válido | Suporte quando o WhatsApp não está configurado |

Configure-as como as variáveis do banco, antes de iniciar o servidor. A tela **Esqueci minha senha** apresenta o contato de suporte; ela não envia mensagens nem redefine a senha automaticamente.

## 9. Testes

Com o banco configurado e o Node.js instalado, execute:

```text
composer test
```

Para executar as verificações separadamente:

```text
php tests/pedido.integration.php
php tests/regressao.integration.php
node --test tests/cart.test.cjs
php bin/control orm:validate-schema --skip-sync
```

Os testes PHP usam o banco configurado, criam registros temporários e desfazem suas transações ao terminar. Prefira um banco de desenvolvimento. O comando de validação do Doctrine confere os mapeamentos sem alterar as tabelas; `--skip-sync` não compara o schema do banco.

## 10. Problemas comuns

| Problema | Como resolver |
| --- | --- |
| `php`, `composer` ou `mysql` não reconhecido | Confira a instalação e o `PATH`; depois abra outro terminal. |
| PHP abaixo de 8.4.1 | Instale uma versão compatível e confira qual executável aparece em `php -v`. |
| `could not find driver` | Habilite `pdo_mysql` no `php.ini` usado pelo terminal e reinicie o servidor PHP. |
| `vendor/autoload.php` não encontrado | Execute `composer install` na raiz do projeto. |
| `Access denied` | Confira usuário, senha e permissões do MySQL. |
| Conexão recusada | Inicie o MySQL e confira host e porta. |
| Banco ou tabela não encontrado | Confira `KRIKOS_DB_NAME` e a importação do schema. |
| Porta em uso | Escolha outra porta ao iniciar o servidor. |
| Cardápio vazio | Confira se existem produtos ativos e se os nomes das categorias correspondem aos descritos acima. |
| Login não funciona | Confira e-mail, senha original, perfil e campo `ativo = 'S'` no cadastro. |

## Estrutura do projeto

```text
public/                 Páginas, API, imagens, JavaScript e CSS
includes/               Sessão, autorização, funções e templates compartilhados
src/Controller/         Entrada das operações do backend
src/Services/           Validação, cadastros, autenticação e pagamentos
src/Repository/         Consultas e schema SQL
src/Entity/             Entidades do sistema
config/xml/             Mapeamentos do Doctrine
bin/control             Comandos de console do Doctrine
tests/                  Testes de integração e do carrinho
bootstrap.php           Inicialização do Doctrine e conexão com o banco
iniciar.bat              Inicialização no Windows
iniciar.ps1              Servidor local com porta configurável
```

As dependências e arquivos locais, como `vendor/`, `node_modules/`, `.local/`, logs e configurações `.env`, são ignorados pelo Git. Mantenha `composer.json`, `composer.lock`, o schema SQL e os scripts de inicialização no repositório.
