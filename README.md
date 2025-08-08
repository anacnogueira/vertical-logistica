🚀 Visão Geral do Projeto
Este projeto Laravel fornece uma API para normalizar dados de pedidos e produtos provenientes de um sistema legado em um formato de arquivo de largura fixa. A API processa o arquivo, extrai as informações de usuários, pedidos e produtos, agrega os totais dos pedidos e retorna os dados em uma estrutura JSON padronizada e hierárquica.

O objetivo principal é transformar dados brutos e padronizados (com preenchimento de zeros e espaços) em uma representação mais limpa e estruturada, facilitando o consumo por outras aplicações ou sistemas.

✨ Princípios de Design
Este projeto foi desenvolvido com foco nos princípios SOLID e nas Object Calisthenics para garantir código limpo, testável, manutenível e escalável:

Princípio da Responsabilidade Única (SRP): As responsabilidades foram segregadas em classes distintas:

FileUploadRequest: Validação da requisição HTTP.

LineParser: Parseamento de linhas individuais de largura fixa.

OrderNormalizationMapper: Mapeamento e agregação dos dados normalizados.

NormalizationController: Orquestração do fluxo da requisição.

\*Resource: Formatação da resposta JSON.

Teste Unitário Robusto: Cada componente lógico é testado isoladamente para garantir sua funcionalidade e permitir refatorações seguras.

🛠️ Tecnologias Utilizadas
PHP 8.2+

Laravel 12.x

Laravel Sail (ambiente de desenvolvimento via Docker)

PHPUnit 10+ (para testes)

📦 Instalação do Projeto
Siga os passos abaixo para configurar o projeto em sua máquina local utilizando Laravel Sail:

Clone o repositório:

Bash

git clone [<URL_DO_SEU_REPOSITORIO>](https://github.com/anacnogueira/vertical-logistica)
cd vertical-logistica
Instale as dependências do Composer e inicie o Sail:

Bash

composer install
./vendor/bin/sail up -d
Isso iniciará os contêineres Docker necessários para o seu ambiente Laravel em segundo plano.

Copie o arquivo de ambiente e gere a chave da aplicação (dentro do Sail):

Bash

./vendor/bin/sail artisan key:generate
Se você não tiver um .env ainda, copie o .env.example:

Bash

cp .env.example .env
Acesse o projeto:
O projeto estará acessível via http://localhost.

🚀 Como Usar a API
Endpoint
URL: /api/order/normalize-txt

Método: POST

Content-Type: multipart/form-data

Parâmetros da Requisição
A API espera um arquivo de texto de largura fixa no corpo da requisição.

Campo Tipo Descrição
file File O arquivo .txt contendo os dados do sistema legado. Tamanho máximo de 10MB.

Exportar para as Planilhas
Estrutura do Arquivo de Entrada (Largura Fixa)
Cada linha do arquivo representa uma parte de um pedido e segue a seguinte estrutura de largura fixa. Os campos numéricos são preenchidos com '0' à esquerda, e os campos de texto com espaços à esquerda (alinhados à direita) ou à direita (alinhados à esquerda) dentro de sua largura total, conforme o padrão.

Campo Tamanho Tipo Descrição
id usuário 10 Numérico ID único do usuário.
nome 45 Texto Nome do usuário. Preenchido com espaços.
id pedido 10 Numérico ID único do pedido.
id produto 10 Numérico ID único do produto.
valor do produto 12 Decimal Valor do item do produto. Preenchido com espaços.
data compra 8 Numérico (yyyymmdd) Data da compra do item.

Exportar para as Planilhas
Exemplo de Linha do Arquivo de Entrada:

0000000070 Palmer Prosacco00000007530000000003 1836.7420210308
Resposta da API (JSON)
A API retornará uma estrutura JSON normalizada, agrupando os produtos por pedido e os pedidos por usuário, com o total calculado para cada pedido.

Exemplo de Resposta:

JSON

[
{
"user_id": 70,
"name": "Palmer Prosacco",
"orders": [
{
"order_id": 753,
"total": "1836.74",
"date": "2021-03-08",
"products": [
{
"product_id": 3,
"value": "1836.74"
}
]
}
]
},
{
"user_id": 1,
"name": "Zarelli",
"orders": [
{
"order_id": 123,
"total": "1024.48",
"date": "2021-12-01",
"products": [
{
"product_id": 111,
"value": "512.24"
},
{
"product_id": 122,
"value": "512.24"
}
]
}
]
}
]
Exemplo de Uso com curl (dentro do Sail)
Para interagir com a API enquanto o Sail está rodando, você pode usar:

Bash

curl -X POST \
 http://localhost/api/order/normalize-txt \
 -H 'Accept: application/json' \
 -H 'Content-Type: multipart/form-data' \
 -F 'file=@path/to/your/fixed_width_example.txt'
Substitua path/to/your/fixed_width_example.txt pelo caminho real do seu arquivo de teste no seu sistema de arquivos local. O Sail lida com o mapeamento de volumes.

🧪 Executando os Testes (com Sail)
Para garantir a qualidade e o funcionamento correto da aplicação, execute a suíte de testes unitários e de feature utilizando o Sail:

Bash

./vendor/bin/sail artisan test
Os testes estão localizados nas pastas tests/Unit (para LineParser e OrderNormalizationMapper) e tests/Feature (para a NormalizationController). Eles usam o PHPUnit com atributos PHP 8+ (#[Test]).

🛑 Parando o Sail
Para parar os contêineres Docker do Sail:

Bash

./vendor/bin/sail stop
Para parar e remover os contêineres:

Bash

./vendor/bin/sail down
🤝 Contribuição
Sinta-se à vontade para contribuir com melhorias! Para isso:

Faça um fork do repositório.

Crie uma nova branch (git checkout -b feature/sua-feature).

Faça suas alterações e escreva testes para elas.

Commit suas mudanças (git commit -m 'feat: adicionei nova funcionalidade X').

Envie para a branch (git push origin feature/sua-feature).

Abra um Pull Request.

📄 Licença
Este projeto está licenciado sob a Licença MIT.
