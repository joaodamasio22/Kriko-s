# Kriko’s — Sistema de gerenciamento de pedidos

Sistema desenvolvido para uma pastelaria e churreria, com cardápio digital, gerenciamento de pedidos por mesa, fila de preparo e registro de pagamentos presenciais.

## Funcionalidades

- Autenticação e controle de acesso por perfil (Master, Garçom e Cozinha).
- Cadastro de categorias, produtos, mesas e usuários da equipe.
- Cardápios digitais de pastéis e churros com carrinho dinâmico.
- Registro de pedidos e seus itens no banco de dados.
- Acompanhamento do preparo pela cozinha.
- Confirmação de pagamentos presenciais via Pix, dinheiro ou cartão.
- Exibição dos produtos mais vendidos.

## Tecnologias utilizadas

PHP orientado a objetos, MySQL, Doctrine ORM 3, Doctrine DBAL 4, mapeamento XML, Composer, HTML5, CSS3, JavaScript e Bootstrap. O backend utiliza DTO (Data Transfer Object), Chain of Responsibility e Repository.

## 1. Requisitos mínimos

| Ferramenta | Requisito |
| --- | --- |
| PHP | 8.4.1 ou superior, em versão compatível com as dependências |
| Composer | 2.x |
| MySQL | 8.0.16 ou superior recomendado (suporte às restrições CHECK) |
| Git | Necessário para clonar; opcional se baixar o ZIP |
| Navegador | Chrome, Firefox, Edge ou equivalente atualizado |
| Node.js | 20 ou superior recomendado; somente para testes JavaScript |

MariaDB pode ser utilizado caso seja compatível com o schema SQL, mas não foi validado nesta revisão.

Extensões PHP necessárias: `pdo`, `pdo_mysql`, `mbstring`, `dom`, `SimpleXML`, `xml`, `ctype`, `iconv`, `session` e `json` (algumas já fazem parte da instalação padrão).

Confira o ambiente:

```bash
php -v
php --ini
php -m
composer --version
mysql --version
```

No Windows, verifique se os executáveis estão no PATH e se o PHP utilizado no terminal atende à versão mínima. Não é necessário instalar Apache, executar `npm install` ou utilizar Docker. Os arquivos do Bootstrap já estão incluídos no projeto.

## 2. Baixar o projeto

```bash
git clone https://github.com/joaodamasio22/Kriko-s.git
cd Kriko-s
```

Alternativamente, baixe o ZIP do repositório, extraia-o e abra o terminal na pasta que contém `composer.json`.

## 3. Instalar as dependências

```bash
composer install
composer check-platform-reqs
```

O Composer criará a pasta `vendor/` e instalará as versões registradas em `composer.lock`. Não utilize `composer update` como etapa obrigatória de instalação.

## 4. Criar o banco de dados

Inicie o serviço MySQL. O script de criação está em:

```text
src/Repository/Schema/SchemaDb.sql
```

Na raiz do projeto, abra o cliente MySQL (ajuste a porta conforme sua instalação):

```bash
mysql -h 127.0.0.1 -P 3307 -u root -p
```

Informe a senha do seu banco; se estiver vazia, pressione Enter. Dentro do console MySQL, execute:

```sql
SOURCE src/Repository/Schema/SchemaDb.sql;
SHOW TABLES FROM krikos_db;
```

Também é possível executar o arquivo pelo MySQL Workbench ou phpMyAdmin.

O script cria o banco `krikos_db` e sete tabelas: `administradores`, `mesas`, `categorias`, `produtos`, `pedidos`, `itens_pedido` e `pagamentos`. Execute em um banco novo: o script não é uma migração e não deve ser reaplicado sobre tabelas existentes.

## 5. Configurar a conexão

Abra `bootstrap.php`, na raiz do projeto. Ajuste os parâmetros da conexão para o seu ambiente:

```php
$param = [
    'driver' => 'pdo_mysql',
    'host' => '127.0.0.1',
    'port' => 3307,
    'user' => 'root',
    'password' => '',
    'dbname' => 'krikos_db',
    'charset' => 'utf8mb4',
];
```

A porta configurada no repositório é **3307**. Se o MySQL estiver utilizando a porta padrão **3306**, altere `port` e use a mesma porta ao importar o banco. Ajuste também o usuário e a senha.

O código atual não lê variáveis de ambiente para a conexão nem carrega `.env` automaticamente. Não publique credenciais pessoais no GitHub. Para uso além do ambiente local, utilize um usuário de banco dedicado e uma configuração de credenciais segura.

## 6. Criar o primeiro usuário Master

O schema não inclui dados iniciais. Antes de acessar o sistema, crie um administrador.

Gere um hash para uma senha de sua escolha (mínimo de oito caracteres):

```bash
php -r "echo password_hash('TroqueEstaSenha123!', PASSWORD_DEFAULT), PHP_EOL;"
```

Substitua a senha de exemplo por uma senha própria. Copie o hash completo e execute no MySQL:

```sql
USE krikos_db;

INSERT INTO administradores (nome, email, senha, perfil, ativo)
VALUES ('Administrador', 'admin@example.com', 'COLE_AQUI_O_HASH', 'MASTER', 'S');
```

Substitua o e-mail e o marcador do hash pelos seus dados. No login, utilize a senha original, não o hash. Os demais usuários podem ser cadastrados pelo painel Master.

## 7. Executar o projeto

Na raiz do projeto, execute:

```bash
php -S 127.0.0.1:8765 -t public
```

Abra no navegador: **http://127.0.0.1:8765**.

O sistema direcionará para a tela de login. Mantenha o terminal e o MySQL em execução. Para encerrar o servidor, pressione `Ctrl+C`.

Caso a porta esteja ocupada, utilize outra, como `8766`, e ajuste o endereço no navegador. A raiz pública deve ser `public/`. O servidor embutido do PHP é destinado ao desenvolvimento local, não à produção.

## 8. Utilizar o sistema

Entre como Master e cadastre:

1. Categorias com os nomes `Pastel` (ou `Pastéis`) e `Churros`. Para porções, utilize `Porções de pastéis` e `Porções de churros`.
2. Produtos ativos associados às categorias. Informe os preços em reais no formulário; o banco armazena valores em centavos inteiros.
3. Mesas ativas com números únicos.
4. Usuários com os perfis `GARCOM` e `COZINHA`.

| Perfil | Página | Responsabilidade |
| --- | --- | --- |
| MASTER | `master.php` | Cadastros, confirmação de pagamentos e finalização de pedidos |
| GARCOM | `index.php` | Cardápios, carrinho, envio de pedidos, pagamentos e finalização |
| COZINHA | `cozinha.php` | Fila de pedidos, início do preparo e marcação como pronto |

Para testar o fluxo completo, entre como Garçom, selecione os produtos, informe uma mesa ativa e envie o pedido. Entre como Cozinha, atualize a fila e marque o pedido como pronto após o preparo. Por fim, como Garçom ou Master, acesse Pedidos e pagamentos, confirme o recebimento e finalize o pedido.

O sistema registra pagamentos presenciais; não efetua cobranças online nem integra diretamente com operadoras de cartão ou Pix. A recuperação de senha apresenta contatos de suporte, sem redefinição automática.

## 9. Estrutura do projeto

```text
Kriko-s/
├── bin/control              # Console do Doctrine
├── config/xml/              # Mapeamento das entidades
├── includes/                # Sessão, autenticação e componentes compartilhados
├── public/                  # Páginas, API e recursos do frontend
│   ├── api/pedidos.php       # Recebimento do carrinho em JSON
│   └── assets/              # JavaScript e bibliotecas locais
├── src/
│   ├── Chain/               # Chain of Responsibility
│   ├── Controller/          # Controle das operações
│   ├── DTO/                 # Transporte de dados
│   ├── Entity/              # Entidades do Doctrine
│   ├── Repository/          # Consultas e schema SQL
│   └── Services/            # Regras de negócio e validações
├── tests/                   # Testes de integração e do carrinho
├── bootstrap.php            # Inicialização e conexão com o banco
├── composer.json
└── composer.lock
```

## 10. Testes e validação

Com as dependências instaladas, banco de desenvolvimento configurado e Node.js disponível:

```bash
composer test
```

Para executar separadamente:

```bash
php tests/pedido.integration.php
php tests/regressao.integration.php
node --test tests/cart.test.cjs
php bin/control orm:validate-schema --skip-sync
```

Os testes PHP utilizam o banco configurado e trabalham com transações para desfazer registros de teste. Utilize um banco de desenvolvimento. O comando Doctrine acima valida os mapeamentos sem comparar a estrutura física do banco. Para comparar também o schema, execute `php bin/control orm:validate-schema`.

## Problemas comuns

| Erro | Solução |
| --- | --- |
| PHP abaixo de 8.4.1 | Atualize o PHP e confira `php -v`. |
| `could not find driver` | Habilite `pdo_mysql` no PHP utilizado pelo terminal. |
| `vendor/autoload.php` não encontrado | Execute `composer install`. |
| `Access denied` | Confira usuário, senha e permissões do MySQL. |
| Conexão recusada | Inicie o MySQL e confira a porta em `bootstrap.php`. |
| Banco ou tabela inexistente | Importe o schema SQL e confira `dbname`. |
| Cardápio vazio | Cadastre produtos ativos nas categorias reconhecidas. |
| Login inválido | Confira o e-mail, senha original, perfil e `ativo = 'S'`. |

## Configurações opcionais

As variáveis de ambiente `KRIKOS_WHATSAPP` (número com país e DDD), `KRIKOS_INSTAGRAM` (perfil) e `KRIKOS_SUPPORT_EMAIL` (e-mail) configuram contatos do cardápio e suporte. Defina-as no ambiente do servidor PHP antes de iniciá-lo.
