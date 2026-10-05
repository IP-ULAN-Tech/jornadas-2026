# Jornadas Técnico-Científicas IPS 2026

Site em PHP para apresentar as Jornadas, consultar submissões e enviar trabalhos. O projeto pode ser executado localmente com XAMPP, Apache e MariaDB

## Ambiente local

1. Inicie Apache e MySQL no XAMPP.
2. No phpMyAdmin, crie o banco `jornadas` e importe `sql/schema.sql` ou o dump `jornadas_ips_2026.sql`, conforme o banco que estiver usando.
3. Configure o acesso ao banco em `inc/config.php` de acordo com o usuário MariaDB local.
4. Acesse `http://localhost/jornadas-2026-main/`.

O banco local usado durante a configuração deste projeto já estava populado com 21 tabelas e 146 registros. Para esse ambiente, foi criado o usuário `jornadas`, com host `127.0.0.1`, banco `jornadas` e senha `12345678`. Essa senha é apenas uma configuração local de desenvolvimento; não a reutilize em produção.

Os comandos usados para criar esse usuário foram:

```sql
CREATE USER IF NOT EXISTS 'jornadas'@'localhost' IDENTIFIED BY '12345678';
CREATE USER IF NOT EXISTS 'jornadas'@'127.0.0.1' IDENTIFIED BY '12345678';

ALTER USER 'jornadas'@'localhost' IDENTIFIED BY '12345678';
ALTER USER 'jornadas'@'127.0.0.1' IDENTIFIED BY '12345678';

GRANT ALL PRIVILEGES ON jornadas.* TO 'jornadas'@'localhost';
GRANT ALL PRIVILEGES ON jornadas.* TO 'jornadas'@'127.0.0.1';
FLUSH PRIVILEGES;
```

## Frontend

O visual combina o CSS existente em `assets/css/site.css` com utilitários gerados pelo Tailwind CSS v4. O menu responsivo e acessível é escrito em TypeScript. O PHP continua sendo servido diretamente pelo Apache; Node.js e npm são necessários apenas para compilar os assets.

Instale as dependências e gere os arquivos compilados:

```bash
npm install
npm run build
```

O build gera `assets/css/utilities.css` e `assets/js/menu.js`. Execute-o novamente depois de editar `assets/ts/`, `assets/css/tailwind.css` ou classes Tailwind nos templates PHP.

Para verificar os tipos sem gerar os arquivos:

```bash
npm run typecheck
```

05.10.2025 (Ultimas alteraçoes)

Hoje trabalhei em várias melhorias no site das Jornadas. Ajustei as imagens para passarem automaticamente, mesmo quando o cursor está sobre elas, e dei uma nova organização à seção “O Evento”, destacando o texto, os números e a fotografia.

Também revisei os menus para funcionarem melhor em diferentes tamanhos de tela. Padronizei os links nas páginas inicial, de submissão e de consulta, corrigi o contraste do menu móvel e criei uma opção recolhível para a navegação do painel administrativo. Além disso, ajustei tabelas e formulários para se adaptarem a telas menores.

Testei as páginas públicas em diferentes larguras, de celulares a computadores, e confirmei que não há transbordamento horizontal nesses tamanhos. Também verifiquei a sintaxe do PHP e do TypeScript. Como o painel exige autenticação, testei o comportamento do menu administrativo num ambiente isolado. Deixei o site rodando localmente em http://localhost:8000.