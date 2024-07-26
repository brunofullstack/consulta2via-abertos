# Consultar Faturas em Aberto

Este projeto é uma aplicação web simples que permite aos usuários consultar faturas em aberto utilizando um CPF ou CNPJ. A aplicação faz uso de tecnologias como PHP, Bootstrap, jQuery e DataTables.

## Funcionalidades

- **Consulta de Faturas**: Os usuários podem inserir um CPF ou CNPJ para consultar as faturas em aberto.
- **Detalhamento de Cobranças**: Ao clicar em uma cobrança listada, os usuários podem ver detalhes adicionais, incluindo um link para download do boleto em PDF e o código de barras.
- **Máscara de Input**: O campo de entrada de CPF/CNPJ tem uma máscara para facilitar a inserção correta dos dados.
- **Tabela Interativa**: As faturas são exibidas em uma tabela interativa com paginação e suporte a busca, utilizando DataTables.

## Tecnologias Utilizadas

- **HTML5** e **CSS3**: Estrutura básica e estilo da página.
- **Bootstrap 5**: Framework CSS para design responsivo e componentes prontos.
- **jQuery**: Biblioteca JavaScript para manipulação do DOM e chamadas AJAX.
- **DataTables**: Plugin jQuery para criar tabelas dinâmicas e interativas.
- **PHP**: Linguagem de backend para processar as requisições e consultas à API.
- **APIs Bemtevi**: Utilizadas para consulta de clientes e cobranças.

## Instalação

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/brunofullstack/consulta2via-abertos.git
   ```
2. **Navegue até o diretório do projeto**:
   ```bash
   cd seu-repositorio
   ```
3. **Configure o ambiente**:
   - Certifique-se de que você possui um servidor web com suporte a PHP.
   - Insira o token de acesso no arquivo `index.html` ou `config.php`.

## Como Usar

1. **Insira o CPF/CNPJ**: Digite o número no campo de entrada.
2. **Consultar**: Clique no botão "Consultar" para buscar as faturas.
3. **Visualizar Detalhes**: Clique em uma linha da tabela para ver os detalhes da cobrança e baixar o boleto.

## API Utilizada

A aplicação utiliza as APIs da Ksys Sistemas de Gestão:

- **Consulta de Clientes**: `https://api-bemtevi.ksys.net.br/cliente`
- **Consulta de Cobranças**: `https://api-bemtevi.ksys.net.br/cobrancas`
- **Consulta de Segunda Via de Boleto**: `consultar_segunda_via.php`

## Contribuição

Se desejar contribuir, por favor, faça um fork do repositório e crie uma pull request. Para grandes mudanças, abra uma issue primeiro para discutir o que você gostaria de mudar.

## Licença

Este projeto é licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

Sinta-se à vontade para ajustar as seções conforme necessário e adicionar mais detalhes específicos do projeto.
