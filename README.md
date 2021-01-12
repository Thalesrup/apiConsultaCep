# ApiCunsulta Endereços (CEP - Logadouro)

#### About 
ApiConsultaCep usa como base para consulta http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaCepEndereco.cfm
####

### Highlights

- Utilizado Laravel Framework 8
- Implentado metodo getCEP()
- Implementado Validação de CEP (valida quantidade minima de numeros informados)
- Implementado getLogadouro() (Pode ser passado tanto cep como nome da rua, bairro cidade, ect)
- Implementado Validação para Prever acentuações nas requisições
- Implementado Callback que retorna o resultado em formato XML para CEP e Logadouro (adicionar ao final ?xml)
- Implementado metodo Abstrato para formatar array e gerar saida XML, descartando a necessidade de criar um arquivo para exibição
- Implementado Rota Callback Fail 404 formato json

##### Routes Public Buscar Por Cep

URL: 
/api/buscaEndereco/cep/{cep}
<p>
Exemplo: /api/buscaEndereco/cep/92425553 ou 92425-553
<p>
Exemplo Utilizando callback xml: /api/buscaEndereco/cep/92425553?xml
<p>
Método:
GET <p>
Rota Publica que retornar os dados do endereço em caso de sucesso <p>
Retornos Possíveis: <p>

Formato Json
```apacheconfig
200 OK
{
    success: true,
    message: "Requisição Bem Sucedida",
    data: {
        enderecos: [
          {
            LogradouroNome: "Rua João Maria da Fonseca ",
            BairroDistrito: "São José ",
            LocalidadeUF: "Canoas/RS ",
            CEP: "92425-553"
          }
        ],
    }
}
```
Formato XML
````xml
200 OK
<root>
    <title>lista Enderecos</title>
    <enderecos>
        <endereco>
            <LogradouroNome>Rua João Maria da Fonseca</LogradouroNome>
            <BairroDistrito>São José</BairroDistrito>
            <LocalidadeUF>Canoas/RS</LocalidadeUF>
            <CEP>92425-553</CEP>
        </endereco>
    </enderecos>
</root>
````

Se For Informado um CEP Inválido
```apacheconfig
400 OK
{
    success: false,
    message: "Erro Ao Validar Cep, Digite um Numero Válido Exemplo(92425553 ou 92425-553)",
    data: false
}
```

##### Routes Public Buscar Por Logadouro

URL: 
/api/buscaEndereco/logadouro/{logadouroOrCep}
<p>
Exemplo: /api/buscaEndereco/logadouro/rua caique ou 92425553
<p>
Pode ser passado tanto Rua, avenida, Cidade, CEP ect, como parametro
<p>
Exemplo Utilizando callback xml: /api/buscaEndereco/logadouro/av caique?xml
<p>
Método:
GET <p>
Rota Publica que retornar os dados do endereço em caso de sucesso <p>
Retornos Possíveis: <p>

Formato Json
```apacheconfig
200 OK
{
success: true,
message: "Requisição Bem Sucedida",
data: {
        enderecos: [
            {
                LogradouroNome: "Rua Caique Chagas de Assis ",
                BairroDistrito: "Parque Residencial Villa dos Inglezes ",
                LocalidadeUF: "Sorocaba/SP ",
                CEP: "18051-886"
            },
            {
                LogradouroNome: "Rua Caique Ferreira ",
                BairroDistrito: "Residencial Betaville ",
                LocalidadeUF: "Campo Grande/MS ",
                CEP: "79060-354"
            },
            {
                LogradouroNome: "Rua Caíque Ferreira ",
                BairroDistrito: "Jardim Alvorada ",
                LocalidadeUF: "Piracicaba/SP ",
                CEP: "13425-702"
            },
            {
                LogradouroNome: "Rua do Caíque ",
                BairroDistrito: "Recanto das Árvores ",
                LocalidadeUF: "Camaçari/BA ",
                CEP: "42807-702"
            }
        ]
    }
}
```
Formato XML
````xml
200 OK
<root>
<title>lista Enderecos</title>
    <enderecos>
        <endereco>
            <LogradouroNome>Rua Caique Chagas de Assis </LogradouroNome>
            <BairroDistrito>Parque Residencial Villa dos Inglezes </BairroDistrito>
            <LocalidadeUF>Sorocaba/SP </LocalidadeUF>
            <CEP>18051-886</CEP>
        </endereco>
        <endereco>
            <LogradouroNome>Rua Caique Ferreira </LogradouroNome>
            <BairroDistrito>Residencial Betaville </BairroDistrito>
            <LocalidadeUF>Campo Grande/MS </LocalidadeUF>
            <CEP>79060-354</CEP>
        </endereco>
        <endereco>
            <LogradouroNome>Rua Caíque Ferreira </LogradouroNome>
            <BairroDistrito>Jardim Alvorada </BairroDistrito>
            <LocalidadeUF>Piracicaba/SP </LocalidadeUF>
            <CEP>13425-702</CEP>
        </endereco>
        <endereco>
            <LogradouroNome>Rua do Caíque </LogradouroNome>
            <BairroDistrito>Recanto das Árvores </BairroDistrito>
            <LocalidadeUF>Camaçari/BA </LocalidadeUF>
            <CEP>42807-702</CEP>
        </endereco>
    </enderecos>
</root>
````

Se For Informado um Logadouro ou CEP Inválido
```apacheconfig
400 OK
{
    success: false,
    message: "Informe um CEP ou Logadouro Válido",
    data: false
}
````

#### Setup
````bash
# clone
git clone https://github.com/Thalesrup/apiConsultaCep

# Acessar projeto
cd apiConsultaCep

# Executar
composer install
php artisan serve

###Testar Request Logadouro
#Retorno json
$ curl -H 'content-type: application/json' -H 'Accept: application/json' -v -X GET http://127.0.0.1:8000/api/buscaEndereco/logadouro/rua caique

#Retorno XML
$ curl -H 'content-type: application/json' -H 'Accept: application/xml' -v -X GET http://127.0.0.1:8000/api/buscaEndereco/logadouro/rua caique?xml

# Acessar pelo Browser
http://127.0.0.1:8000/api/buscaEndereco/logadouro/rua caique

###Testar Request Rua
#Retorno json
$ curl -H 'content-type: application/json' -H 'Accept: application/json' -v -X GET http://127.0.0.1:8000/api/buscaEndereco/cep/92425553

#Retorno XML
$ curl -H 'content-type: application/json' -H 'Accept: application/xml' -v -X GET http://127.0.0.1:8000/api/buscaEndereco/cep/82425553?xml

# Acessar pelo Browser
http://127.0.0.1:8000/api/buscaEndereco/cep/82425553 ou 82425553?xml
````


