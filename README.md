## Larafuse Builder Form - Projeto README

### Mode de usar
Para usar o Larafuse, basta no seu terminal criar um projeto do composer com:

<pre>
composer create-project larafuse/larafuse my-project
</pre>

Executando, o composer irá criar um projeto na pasta com toda a estrtura Laravel + FilamentPHP pronta para o uso.

### Introdução

**Larafuse Builder Form** é uma estrutura Laravel que possui algumas configurações Filament já implementadas com o objetivo de acelerar o desenvolvimento de sistemas administrativos, utilizando FilamentPHP de forma principal em sua stack. Com ele você pode automatizar a criação de módulos no Laravel. Este permite que os desenvolvedores criem modelos, migrações, políticas, recursos e seeders de maneira simplificada, com base na estrutura de dados fornecida através de um formulário dinâmico.

O projeto é ideal para acelerar o desenvolvimento de sistemas com **CRUD** (Create, Read, Update, Delete), fornecendo uma interface intuitiva para a definição de tabelas e relacionamentos, além de gerar automaticamente as classes necessárias para interagir com o banco de dados.

### Funcionalidades

1. **Criação Automática de Modelos**: O Larafuse Builder gera automaticamente os modelos Laravel com base no nome e estrutura da tabela fornecida.
2. **Criação de Migrações**: Gera migrações de banco de dados associadas ao modelo, com suporte para tipos de colunas comuns, como `string`, `text`, `boolean`, e relacionamentos `fk` (chave estrangeira).
3. **Geração de Seeders**: O seeder é gerado automaticamente e vinculado ao `DatabaseSeeder`, permitindo a fácil população do banco de dados com dados de exemplo.
4. **Gerenciamento de Relacionamentos**: O componente permite a definição de relacionamentos (`belongsTo`, `hasMany`, `hasOne`, etc.), além de gerar métodos de relacionamento nas models e configurar os relacionamentos reversos.
5. **Criação de Recursos do Filament**: Gera classes de recursos do Filament com esquemas de formulário e tabela automáticos, permitindo gerenciar as entidades via painel administrativo.
6. **Geração de Políticas**: Cria e edita automaticamente políticas de autorização para os modelos, aplicando permissões básicas.
7. **Suporte a SoftDeletes e Timestamps**: Permite ativar soft deletes e timestamps, gerando automaticamente as colunas e métodos relacionados nas models e migrações.
8. **Execução de Migrações**: Além de criar a migração, o sistema pode rodar automaticamente as migrações no banco de dados.

### Como Funciona

#### 1. **Formulário de Configuração**

O componente utiliza um formulário gerado dinamicamente com base no **Filament**, onde o usuário define os detalhes do novo módulo, incluindo:

- **Título do Módulo**: Nome singular do módulo (por exemplo, "Produto").
- **Model, Tabela, Seed, Resource**: Campos para definir o modelo, tabela de banco de dados, seeder e recurso associados ao módulo.
- **Estrutura da Tabela**: Defina as colunas e seus tipos (texto, números, datas, etc.), além de configurar se os campos são obrigatórios, se permitem nulos e seus valores padrão.
- **Relacionamentos**: Configure relacionamentos entre as entidades, como `hasMany`, `belongsTo`, `hasOne`, e mais.

#### 2. **Criação de Módulo**

Após preencher o formulário, ao submeter, o **Larafuse Builder** realiza as seguintes ações:

- **Gera Model**: Um modelo é gerado com base nas configurações fornecidas, com a definição de `$fillable`, `$softDeletes` e timestamps.
- **Gera Migração**: A migração correspondente é gerada automaticamente e pode ser executada após a criação.
- **Cria Seeder**: Um seeder é gerado e inserido no `DatabaseSeeder`.
- **Cria Relacionamentos**: Métodos de relacionamento são gerados automaticamente no modelo.
- **Cria Políticas**: Políticas de autorização básicas são geradas.
- **Cria Recursos do Filament**: Um recurso do Filament é gerado para interagir com os dados do modelo diretamente no painel administrativo.

### Como Usar

#### Requisitos

- Laravel 8.x ou superior.
- Livewire.
- Filament (para o gerenciamento do painel administrativo).

#### Passo a Passo

1. **Configurar Estrutura**: Defina a estrutura da tabela e as opções de relacionamento através do formulário.
2. **Submeter Formulário**: Submeta o formulário e o sistema automaticamente irá gerar o código correspondente.
3. **Gerenciamento via Painel Filament**: Acesse o painel Filament para gerenciar os dados utilizando os recursos gerados.

### Campos e Seções

#### Seções do Formulário

1. **Nome do Módulo**: Define o nome do módulo em singular, como "Produto" ou "Usuário".
2. **Configurações do Resource**: Define como o módulo aparecerá na navegação do painel Filament, incluindo nomes no plural, singular, grupo de navegação e opções de simplificação.
3. **Estrutura de Arquivos**: Define a model, tabela de banco de dados, seeder e resource.
4. **Estrutura da Tabela**: Configuração detalhada de colunas, tipos de dados e suas propriedades.
5. **Relacionamentos**: Define os tipos de relacionamento (hasMany, belongsTo, etc.) entre os modelos.
6. **Opções**: Ativar/desativar Soft Deletes e Timestamps.

#### Campos Suportados

Os tipos de coluna suportados incluem:
- **Numéricos**: `bigInteger`, `decimal`, `float`, `integer`, etc.
- **Texto**: `char`, `string`, `text`, `mediumText`, `longText`, `enum`.
- **Datas e Tempos**: `date`, `dateTime`, `time`, `timestamp`.
- **Outros**: `boolean`, `binary`, `json`, `uuid`, e `fk` (chave estrangeira).

#### Relacionamentos Suportados

O sistema suporta os seguintes tipos de relacionamento:

- **belongsTo**
- **hasMany**
- **hasOne**
- **belongsToMany**

### Exemplo de Uso

Aqui está um exemplo básico de como seria preenchido o formulário para criar um módulo "Produto":

- Título: `Produto`
- Model: `App\Models\Produto`
- Tabela: `produtos`
- Seed: `ProdutoSeeder`
- Resource: `ProdutoResource`

##### Estrutura da Tabela

| Nome  | Tipo       | Rótulo         | Nullable | Default  |
|-------|------------|----------------|----------|----------|
| name  | string     | Nome do Produto| false    | null     |
| price | decimal    | Preço          | false    | 0.00     |
| user_id | fk       | Usuário Criador| true     | null     |

##### Relacionamentos

| Tipo      | Nome       | Model                | Coluna        |
|-----------|------------|----------------------|---------------|
| belongsTo | user       | App\Models\User       | user_id       |
| hasMany   | orders     | App\Models\Order      | produto_id    |

### Execução

Para criar o módulo, preencha o formulário e clique em "Criar". O sistema gerará os arquivos automaticamente e você poderá visualizar o novo módulo no painel administrativo do Filament.

### Conclusão

O **Larafuse Builder** é uma ferramenta poderosa para acelerar o desenvolvimento de módulos no Laravel. Ele gera código automaticamente, economizando tempo na criação manual de arquivos, migrações e relacionamentos, permitindo que você se concentre em outros aspectos do desenvolvimento do projeto.

### Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou pull requests para melhorar o Larafuse Builder.

### Licença

Este projeto é distribuído sob a licença MIT.
