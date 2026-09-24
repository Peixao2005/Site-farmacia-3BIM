# Site de Farmácia - PW2 (3º Bimestre)

## Estrutura
- config.php -> conexão com o banco de dados (MySQL/PDO)
- schema.sql -> script para criar o banco e as tabelas (com dados de exemplo)
- index.php -> página visível ao CLIENTE (catálogo de medicamentos com busca)
- criptografia.php -> demonstração dos tipos de criptografia no PHP (M1)
- crud_medicamentos.php -> CRUD administrativo de medicamentos (M2)
- crud_clientes.php -> CRUD administrativo de clientes (M2)
- crud_vendas.php -> CRUD administrativo de vendas (M2)
- css/style.css -> estilo visual do site

## Como rodar (XAMPP / WampServer / Laragon)
1. Copie a pasta "farmacia_site" para dentro de htdocs (XAMPP) ou www (WampServer).
2. Abra o phpMyAdmin e importe o arquivo schema.sql (ele cria o banco "farmacia" e as tabelas).
3. Confira em config.php se o usuário/senha do MySQL estão corretos (padrão XAMPP: user=root, senha vazia).
4. Acesse no navegador: http://localhost/farmacia_site/index.php
5. Para acessar a área administrativa, use os links no menu superior:
   - Admin: Medicamentos
   - Admin: Clientes
   - Admin: Vendas
6. Para ver a demonstração de criptografia (M1), acesse:
   http://localhost/farmacia_site/criptografia.php

## Observações para a entrega no GitHub
- Suba esta pasta inteira no repositório do grupo, mantendo a estrutura de pastas (css/ junto dos .php).
- Cada integrante do grupo deve fazer pelo menos um commit relacionado ao projeto (isso é avaliado no M3 - Análise do GitHub).
- Antes de entregar, teste cadastrar, editar e excluir em cada um dos 3 CRUDs para garantir que tudo funciona.
