# Kriko-s
Projeto interdisciplinar de banco de dados.

Site de pastéis e churros em PHP com Bootstrap 5.3.8. A interface usa fundo claro, amarelo manteiga e vermelho queimado, com navegação adaptada para computador e celular.

## Executar

Copie os arquivos para a pasta `htdocs/krikos` do XAMPP, inicie o Apache e acesse `http://localhost/krikos/`.

Também é possível iniciar o servidor de desenvolvimento na raiz do projeto:

```sh
php -S localhost:8000
```

Depois, acesse `http://localhost:8000/`.

## Arquivos

- `index.php`: início e acesso aos cardápios.
- `pastel.php` e `churros.php`: dados dos sabores e preços.
- `includes/cardapio.php`: apresentação compartilhada dos cardápios.
- `includes/header.php` e `includes/footer.php`: navegação e rodapé.
- `style.css`: cores e tipografia sobre Bootstrap.
- `assets/vendor`: arquivos locais oficiais do Bootstrap, com licença MIT nos cabeçalhos.
- `imagens`: fotos e arquivos da marca.

O código próprio tem comentários em português. Os produtos são definidos em arrays PHP; ainda não há integração com banco de dados nem carrinho.

## Pedidos

Configure `$whatsapp` em `includes/cardapio.php` com o telefone real da loja, incluindo país e DDD, somente dígitos. Sem telefone, o site informa que o contato ainda não está disponível.

## Verificação

Os arquivos PHP foram verificados com `php -l`. As três páginas, os links e o menu móvel foram conferidos no navegador.
