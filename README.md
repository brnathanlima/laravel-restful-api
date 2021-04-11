## Configurando o Ambiente de Desenvolvimento

1. Clone este repositório;
2. Acesse a pasta que foi criada do projeto;
3. Crie o arquivo `.env` a partir de uma cópia do arquivo `.env-example` e preencha as informaçõs de conexão com o banco de dados;
4. Instale as dependências executando o comando `composer install` na pasta do projeto;
5. Gere uma chave para a aplicação executando o comando `php artisan key:generate`;
6. Execute as migrations e popule a base de dados com o comando `php artisan migrate --seed`;
7. Prepare o pacote Laravel Passport para uso com o comando `php artisan passport:install`
8. Crie um cliente para autenticação por senhas com o comando `php artisan passport:client --password`
9. Inicie o servidor da aplicação com o comando `php artisan serve`.
10. Acesse a página com a documentação da API [no endereço padrão](http://127.0.0.1:8000/documentation) ou em outro que a aplicação estiver rodando.
11. Opcionalmente você poderá analisar a aplicação pelo [Telescope](http://127.0.0.1:8000/telescope)
