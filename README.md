# E-Market Desafio Técnico PHP

- **Solicitação Original**: Programa de mercado com *Cadastro de Tipo de Produto* com percentual de impostos, *Cadastro de Produto* e *Tela de Venda* com seleção de produtos e suas respectivas quantidades, totalizando preço e impostos, permitindo salvar a venda.

## Para facilitar a validação, foi feito deploy para um servidor online:

- https://emarket.pollauf.net/

- Login: admin

- Senha: admin

## Considerações gerais

### Front-end

- Os arquivos e pastas do diretório raiz, com exceção da pasta /api, fazem parte do build (spa) do Vue.js

- O repositório com o projeto front-end do **Vue.js** é o seguinte:

- https://github.com/pollauf/emarket_front

### Back-end

- Os códigos do back-end se encontram na pasta **/api**

- Foi desenvolvido em **PHP 7.4 puro**, sem utilizar frameworks ou bibliotecas

- Os códigos foram desenvolvidos seguindo a **Programação Orientada a Objetos**

- Criou-se uma arquitetura de API, onde o index.php recebe as requisições HTTP e faz o roteamento

- As rotas da API estão configuradas em **api/routes.php** e os controllers estão na pasta **api/app/controllers**

- As classes desenvolvidas para o embasamento da arquitetura do projeto se encontram em **api/app/lib**

- A conexão com o banco de dados é configurada pelo arquivo **api/config/database.php**

### Como testar em ambiente local

- Clone ou baixe esse repositório

- Certifique-se de que os módulos do postgree estão habilitados no php.ini, sendo eles: *extension=php_pdo_pgsql.dll* e *extension=php_pgsql.dll*

- Crie uma base de dados postgree e execute **pg_restore** no arquivo de dump **emarket_postgres.dump**

- Configure a conexão no arquivo **api/config/database.php**

- Execute **php -S localhost:8080** no diretório raiz desse projeto

- Abra o navegador em **localhost:8080** e faça login com as credenciais:

- *Login*: admin; *Senha*: admin

- Pronto, o sistema está pronto para ser usado!