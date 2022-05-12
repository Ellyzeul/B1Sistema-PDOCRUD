# B1Sistema-PDOCRUD

Sistema de visualização dos dados de compra dos clientes e gerenciamento de fase do processo

## **Importante**

Esse projeto depende umbilicalmente da biblioteca PDOCRUD, que não é open-source, tampouco de uso gratuito, então garanta ter uma distribuição dessa biblioteca antes de trabalhar nesse projeto.

[Link para download](https://github.com/B1GabrielAugusto/PDOCRUD_distro/raw/main/PDOCRUD.zip)

Acesse esse link com login realizado no GitHub em uma conta com acesso ao repositório.

## Instalação do PDOCRUD

Ao ter uma distribuição em mãos

![Pasta com a distribuição](.github/images/procrud_zipped_folder.png)

Mova a pasta ```script``` para dentro da pasta ```vendor``` do projeto

## Configuração do PDOCRUD

No arquivo ```vendor/pdocrud/config/config.php``` colocar o seguinte código

![Trecho de código de configuração](.github/images/pdocrud_config.png)

Lembrando que a maior parte desse código já vem por padrão no arquivo, então em vez de apenas replicar, adapte o código já existente.
