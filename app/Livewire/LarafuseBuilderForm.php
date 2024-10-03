<?php

namespace App\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class LarafuseBuilderForm extends Component  implements HasForms
{
    use InteractsWithForms;

    public $data = [
        'title' => '',
        'model' => '',
        'table' => '',
        'seed' => '',
        'resource' => '',
        'simple_resource' => true,
        'create_migration' => true,
        'run_migration' => false,
        'create_policy' => true,
        'table_structure' => [
            [
                "name" => "name",
                "label" => "Nome",
                "type" => "string",
                "table_relationship" => null,
                "nullable" => false,
                "default" => null
            ],
            [
                "name" => "user_id",
                "label" => "Criador",
                "type" => "fk",
                "table_relationship" => "users",
                "nullable" => true,
                "default" => null
            ]
        ],
        'relationships' => [
            [
                "type" => "belongsTo",
                "name" => "user",
                "model" => "App\Models\User",
                "column" => "user_id"
            ],
            [
                "type" => "hasMany",
                "name" => "users",
                "model" => "App\Models\User",
                "column" => "user_id"
            ]
        ],

        'hasSoftdeletes' => true,
        'hasTimestamps' => true,
    ];

    public $modelBasePath = 'App\Models\\';
    public $seedBasePath = '';

    public function mount()
    {
        $this->data['model'] = $this->modelBasePath;
        $this->data['seed'] = $this->seedBasePath;


        $this->data['title'] = 'Teste';
        $this->data['model'] .= 'Teste';
        $this->data['table'] .= 'testes';
        $this->data['seed'] .= 'TesteSeeder';
        $this->data['resource'] = 'TesteResource';
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nome do Módulo (No singular)')
                    ->reactive()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('model', $this->modelBasePath . $state);

                        $tableName = Str::snake(Str::plural($state));
                        $set('table', $tableName);

                        $set('seed', $this->seedBasePath . $state . 'Seeder');

                        $set('resource', $state . 'Resource');
                    })
                    ->required(),

                Forms\Components\Section::make('Arquivos')
                    ->schema([
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->required(),
                        Forms\Components\TextInput::make('table')
                            ->label('Tabela')
                            ->required(),

                        Forms\Components\TextInput::make('seed')
                            ->label('Seed')
                            ->required(),

                        Forms\Components\TextInput::make('resource')
                            ->label('Resource')
                            ->required(),

                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 3,
                            'lg' => 4,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                            ->schema([
                                Forms\Components\Checkbox::make('simple_resource')
                                    ->label('Simple Resource')
                                    ->inline(),
                                Forms\Components\Checkbox::make('create_migration')
                                    ->label('Criar migration')
                                    ->inline(),
                                Forms\Components\Checkbox::make('run_migration')
                                    ->label('Rodar migration')
                                    ->inline(),
                                Forms\Components\Checkbox::make('create_policy')
                                    ->label('Criar Policy')
                                    ->inline()
                            ]),
                    ]),

                Forms\Components\Section::make('Estrutura de Tabela')
                    ->schema([
                        Forms\Components\Repeater::make('table_structure')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('name', Str::snake($state));
                                    }),

                                Forms\Components\TextInput::make('label')
                                    ->label('Nome do Campo (Rótulo)')
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label('Tipo de Coluna')
                                    ->options([
                                        'Numéricos' => [
                                            'bigInteger' => 'Big Integer',
                                            'decimal' => 'Decimal',
                                            'double' => 'Double',
                                            'float' => 'Float',
                                            'integer' => 'Inteiro',
                                            'mediumInteger' => 'Inteiro Médio',
                                            'smallInteger' => 'Inteiro Pequeno',
                                            'tinyInteger' => 'Inteiro Minúsculo',
                                        ],
                                        'Texto' => [
                                            'char' => 'Caractere',
                                            'string' => 'Texto Curto',
                                            'text' => 'Texto',
                                            'mediumText' => 'Texto Médio',
                                            'longText' => 'Texto Longo',
                                            'enum' => 'Enumeração',
                                        ],
                                        'Datas/Tempos' => [
                                            'date' => 'Data',
                                            'dateTime' => 'Data e Hora',
                                            'time' => 'Hora',
                                            'timestamp' => 'Timestamp',
                                        ],
                                        'Outros' => [
                                            'binary' => 'Binário',
                                            'boolean' => 'Booleano',
                                            'json' => 'JSON',
                                            'uuid' => 'UUID',
                                            'fk' => 'Relacionamento',
                                        ],
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->live(),


                                Forms\Components\Select::make('table_relationship')
                                    ->label('Tabela de Relacionamento')
                                    ->options($this->getAllTables())
                                    ->native(false)
                                    ->searchable()
                                    ->hidden(function (Get $get) {

                                        if ($get('type') != null && $get('type') == 'fk') {
                                            return false;
                                        }
                                        return true;
                                    }),

                                Forms\Components\Checkbox::make('nullable')
                                    ->label('Nulo')
                                    ->inline(false),

                                Forms\Components\TextInput::make('default')
                                    ->label('Valor Padrão'),


                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add coluna')
                            ->reorderableWithButtons(),



                    ]),

                Forms\Components\Section::make('Relacionamentos')
                    ->schema([
                        Forms\Components\Repeater::make('relationships')
                            ->label('')
                            ->schema([

                                Forms\Components\Select::make('type')
                                    ->label('Tipo de Relacionamento')
                                    ->options([
                                        'hasMany' => 'Has Many',
                                        'hasOne' => 'Has One',
                                        'belongsTo' => 'Belongs To',
                                        'belongsToMany' => 'Belongs To Many',
                                    ])
                                    ->native(false)
                                    ->searchable(),


                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required(),


                                Forms\Components\Select::make('model')
                                    ->label('Model')
                                    ->options($this->getAllModels())
                                    ->native(false)
                                    ->searchable(),

                                Forms\Components\TextInput::make('column')
                                    ->label('Coluna na Tabela')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add relacionamento')
                            ->reorderableWithButtons(),
                    ]),


                Forms\Components\Section::make('Opções')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 3,
                            'lg' => 4,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                            ->schema([
                                Forms\Components\Checkbox::make('hasSoftdeletes')
                                    ->label('Soft Deletes')
                                    ->inline(),
                                Forms\Components\Checkbox::make('hasTimestamps')
                                    ->label('Timestamps')
                                    ->inline(),
                            ]),
                    ])





            ])

            ->statePath('data');
    }

    public function create()
    {

        $this->handleModelMigration();
        $this->handleSeeder();
        $this->handlePolicy();
        // $this->runMigration();
        // $this->handleResource();

    }

    public function render()
    {
        return view('livewire.larafuse-builder-form');
    }

    private function getAllTables(): array
    {
        // Verifica o driver do banco de dados para ajustar a consulta correta
        $databaseDriver = config('database.default');

        if ($databaseDriver === 'mysql') {
            // Para MySQL, utilizamos a consulta abaixo
            $tables = DB::select('SHOW TABLES');
        } elseif ($databaseDriver === 'pgsql') {
            // Para PostgreSQL
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        } elseif ($databaseDriver === 'sqlite') {
            // Para SQLite
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        } elseif ($databaseDriver === 'sqlsrv') {
            // Para SQL Server
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE'");
        } else {
            // Caso o driver não seja suportado
            return [];
        }

        $map = array_map('current', $tables);
        $arr = [];
        foreach ($map as $value) {
            $arr[$value] = $value;
        }

        // Extraímos apenas os nomes das tabelas
        return $arr;
    }

    private function getAllModels(): array
    {
        // Caminho para a pasta onde as models geralmente estão localizadas
        $modelPath = app_path('Models');

        // Verifica se o diretório existe
        if (!is_dir($modelPath)) {
            return [];
        }

        // Instancia um objeto de DirectoryIterator para percorrer a pasta de models
        $models = [];
        foreach (new \DirectoryIterator($modelPath) as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isFile()) {
                continue;
            }

            // Verifica se o arquivo tem a extensão .php
            if ($fileInfo->getExtension() === 'php') {
                // Obtém o nome da classe com namespace completo
                $className = 'App\\Models\\' . $fileInfo->getBasename('.php');
                if (class_exists($className)) {
                    $models[] = $className;
                }
            }
        }

        $arr = [];
        foreach ($models as $value) {
            $arr[$value] = $value;
        }

        return $arr;
    }

    private function handlePolicy()
    {
        // Obter o nome da policy com base no modelo
        $policyName = $this->data['title'] . 'Policy';

        // Caminho para o arquivo da policy (normalmente armazenado em 'app/Policies')
        $policyPath = app_path("Policies/{$policyName}.php");

        // Verifica se o arquivo da policy existe
        if (file_exists($policyPath)) {
            // Lê o conteúdo da policy
            $policyContent = file_get_contents($policyPath);

            // Substitui todos os comentários // por return true;
            $updatedContent = str_replace('//', 'return true;', $policyContent);

            // Reescreve o arquivo da policy com as mudanças
            file_put_contents($policyPath, $updatedContent);
        } else {
            // Exibe uma mensagem de erro se o arquivo não existir (caso necessário)
            throw new \Exception("Policy file not found: {$policyPath}");
        }
    }

    private function handleSeeder()
    {
        // Caminho do arquivo DatabaseSeeder
        $seederPath = database_path('seeders/DatabaseSeeder.php');

        // Lê o conteúdo do arquivo DatabaseSeeder
        $seederContent = file_get_contents($seederPath);

        // O código que queremos adicionar
        $callSeederCode = "\$this->call({$this->data['seed']}::class);";

        // Verifica se o código já existe (para evitar duplicação)
        if (!str_contains($seederContent, $callSeederCode)) {
            // Divide o conteúdo da classe em linhas
            $lines = explode(PHP_EOL, $seederContent);

            // Localiza o índice da última chave "}" no arquivo
            $lastBraceIndex = array_key_last($lines);

            // Adiciona o código antes da última chave "}"
            array_splice($lines, $lastBraceIndex - 2, 0, "        {$callSeederCode}");

            // Reescreve o arquivo DatabaseSeeder com a nova linha adicionada
            file_put_contents($seederPath, implode(PHP_EOL, $lines));
        }
    }

    private function handleModelMigration()
    {
        // Criar model
        $command = "make:model {$this->data['title']} --seed --policy --requests";
        Artisan::call($command);

        $migrationName = 'create_' . strtolower($this->data['title']) . 's_table';
        $command = "make:migration {$migrationName}  --create {$this->data['table']}";
        Artisan::call($command);

        // Caminho do arquivo da Model e da Migration
        $modelPath = app_path("Models/{$this->data['title']}.php");
        $migrationPath = database_path('migrations/' . $this->getLastMigrationFile());


        // Adicionar SoftDeletes na Model, se aplicável
        if ($this->data['hasSoftdeletes']) {
            $this->addSoftDeletesToModel($modelPath);
            $this->addSoftDeletesToMigration($migrationPath);
        }

        // Tratar estrutura da model
        $this->addFillableToModel($modelPath);

        // Tratar estrutura da migration
        $this->addColumnsToMigration($migrationPath);

        // Tratar relacionamentos
        $this->handleRelationships($modelPath);
    }

    private function getLastMigrationFile()
    {
        // Caminho da pasta de migrations
        $migrationsPath = database_path('migrations');

        // Pega todos os arquivos da pasta de migrations
        $files = scandir($migrationsPath);

        // Filtra apenas arquivos que terminam com ".php" (ignora pastas ou arquivos sem extensão php)
        $migrationFiles = array_filter($files, function ($file) {
            return str_ends_with($file, '.php');
        });

        // Ordena os arquivos em ordem alfabética (naturalmente já são listados assim)
        sort($migrationFiles);

        // Retorna o último arquivo na ordem alfabética
        return end($migrationFiles);
    }

    private function handleRelationships($modelPath)
    {
        // Itera sobre os relacionamentos
        foreach ($this->data['relationships'] as $relationship) {
            // Adiciona o relacionamento na model atual
            $this->addRelationshipToModel($relationship, $modelPath);

            // Adiciona o relacionamento reverso na model relacionada, se necessário
            $relatedModelPath = app_path('Models/' . class_basename($relationship['model']) . '.php');
            if (file_exists($relatedModelPath)) {
                $this->addReverseRelationship($relationship, $relatedModelPath);
            }
        }
    }

    private function addRelationshipToModel($relationship, $modelPath)
    {
        $relationshipMethod = $this->generateRelationshipMethod($relationship);

        // Lê o conteúdo da model
        $modelContent = file_get_contents($modelPath);

        // Verifica se o método de relacionamento já foi adicionado para evitar duplicação
        if (!str_contains($modelContent, "function {$relationship['name']}")) {
            // Divide o conteúdo em linhas
            $lines = explode(PHP_EOL, $modelContent);

            // Localiza o índice da última chave }
            $lastBraceIndex = array_key_last($lines);

            // Adiciona o método de relacionamento duas linhas antes da última chave }
            array_splice($lines, $lastBraceIndex - 1, 0, $relationshipMethod . PHP_EOL);

            // Reescreve a model com o método de relacionamento
            file_put_contents($modelPath, implode(PHP_EOL, $lines));
        }
    }

    private function addReverseRelationship($relationship, $relatedModelPath)
    {
        // Remove a extensão .php e obtém apenas o nome da classe
        $reverseName = pathinfo($relatedModelPath, PATHINFO_FILENAME);

        $reverseRelationship = [
            'type' => $this->getReverseRelationshipType($relationship['type']),
            'name' => lcfirst($reverseName), // Converte a primeira letra para minúscula
            'model' => "App\\Models\\{$reverseName}",
            'column' => $relationship['column']
        ];

        // Adiciona o relacionamento reverso na model relacionada
        $this->addRelationshipToModel($reverseRelationship, $relatedModelPath);
    }

    private function generateRelationshipMethod($relationship)
    {
        // Gera o método de relacionamento conforme o tipo
        switch ($relationship['type']) {
            case 'belongsTo':
                return "    public function {$relationship['name']}()\n    {\n        return \$this->belongsTo(\\{$relationship['model']}::class, '{$relationship['column']}');\n    }";
            case 'hasMany':
                return "    public function {$relationship['name']}()\n    {\n        return \$this->hasMany(\\{$relationship['model']}::class, '{$relationship['column']}');\n    }";
            case 'hasOne':
                return "    public function {$relationship['name']}()\n    {\n        return \$this->hasOne(\\{$relationship['model']}::class, '{$relationship['column']}');\n    }";
            default:
                return '';
        }
    }

    private function getReverseRelationshipType($type)
    {
        // Determina o relacionamento reverso com base no tipo original
        switch ($type) {
            case 'belongsTo':
                return 'hasMany'; // Ou 'hasOne' dependendo do caso
            case 'hasMany':
            case 'hasOne':
                return 'belongsTo';
            default:
                return '';
        }
    }


    private function addColumnsToMigration($migrationPath)
    {
        // Lê o conteúdo da migration
        $migrationContent = file_get_contents($migrationPath);

        // Armazena as colunas a serem adicionadas
        $columns = [];

        // Itera sobre a estrutura da tabela para adicionar as colunas
        foreach ($this->data['table_structure'] as $field) {
            $column = $this->generateColumnDefinition($field);
            $columns[] = "            \$table->$column;";
        }

        // Converte as colunas para uma string
        $columnsString = implode(PHP_EOL, $columns) . PHP_EOL;

        // Divide o conteúdo da migration em linhas
        $lines = explode(PHP_EOL, $migrationContent);

        // Localiza a linha com $table->id(); e insere as novas colunas logo após
        foreach ($lines as $index => $line) {
            if (str_contains($line, '$table->id();')) {
                // Insere as colunas logo após $table->id();
                array_splice($lines, $index + 1, 0, $columnsString);
                break;
            }
        }

        // Reescreve o arquivo da migration com as colunas
        file_put_contents($migrationPath, implode(PHP_EOL, $lines));
    }

    private function generateColumnDefinition($field)
    {
        $type = $field['type'];
        $name = $field['name'];
        $nullable = $field['nullable'] ? '->nullable()' : '';
        $default = $field['default'] !== null ? "->default('{$field['default']}')" : '';

        // Geração de colunas com base no tipo
        switch ($type) {
            case 'string':
                return "string('{$name}'){$nullable}{$default}";

            case 'integer':
            case 'int':
                return "integer('{$name}'){$nullable}{$default}";

            case 'boolean':
            case 'bool':
                return "boolean('{$name}'){$nullable}{$default}";

            case 'fk': // Foreign key
                return "foreignId('{$name}'){$nullable}->constrained('{$field['table_relationship']}')";


            default:
                return "{$type}('{$name}'){$nullable}{$default}";
        }
    }

    private function addFillableToModel($modelPath)
    {
        // Lê o conteúdo da model
        $modelContent = file_get_contents($modelPath);

        // Verifica se a propriedade fillable já existe na model
        if (!str_contains($modelContent, '$fillable')) {
            // Extrai os campos da estrutura da tabela
            $fillableFields = [];
            foreach ($this->data['table_structure'] as $field) {
                $fillableFields[] = "'{$field['name']}'";
            }

            // Cria a string do fillable
            $fillableString = PHP_EOL . "    protected \$fillable = [" . implode(', ', $fillableFields) . "];" . PHP_EOL . PHP_EOL;

            // Divide o conteúdo da model em linhas
            $lines = explode(PHP_EOL, $modelContent);

            // Localiza o índice do último fechamento de chave "}" no arquivo
            $lastBraceIndex = array_key_last($lines);

            // Adiciona o $fillable uma linha antes do último "}"
            array_splice($lines, $lastBraceIndex - 1, 0, $fillableString);

            // Reescreve o arquivo da model
            file_put_contents($modelPath, implode(PHP_EOL, $lines));
        }
    }

    private function addSoftDeletesToModel($modelPath)
    {
        // Lê o conteúdo da model
        $modelContent = file_get_contents($modelPath);

        // Verifica se o trait SoftDeletes já está presente
        if (!str_contains($modelContent, 'SoftDeletes')) {
            // Divide o conteúdo da model em linhas
            $lines = explode(PHP_EOL, $modelContent);


            // Procura o local após o namespace para adicionar o import do SoftDeletes
            foreach ($lines as $index => $line) {
                // Verifica a linha que contém a declaração de namespace
                if (str_contains($line, 'namespace')) {
                    // Adiciona o import logo após o namespace
                    array_splice($lines, $index + 1, 0, "use Illuminate\\Database\\Eloquent\\SoftDeletes;");
                    break;
                }
            }

            // Procura a linha onde HasFactory está e adiciona o SoftDeletes
            foreach ($lines as &$line) {
                if (str_contains($line, "use HasFactory;")) {
                    $line = "\t use HasFactory, SoftDeletes;";
                    break;
                }
            }

            // Reescreve o arquivo da model
            file_put_contents($modelPath, implode(PHP_EOL, $lines));
        }
    }

    private function addSoftDeletesToMigration($migrationPath)
    {
        // Lê o conteúdo da migration
        $migrationContent = file_get_contents($migrationPath);

        // Verifica se a coluna softDeletes() já foi adicionada
        if (!str_contains($migrationContent, 'softDeletes')) {
            // Divide o conteúdo da migration em linhas
            $lines = explode(PHP_EOL, $migrationContent);

            // Procura onde está o timestamps() e adiciona softDeletes() logo após
            foreach ($lines as &$line) {
                if (str_contains($line, 'timestamps();')) {
                    $line .= PHP_EOL . "            \$table->softDeletes();";
                    break;
                }
            }

            // Reescreve o arquivo da migration
            file_put_contents($migrationPath, implode(PHP_EOL, $lines));
        }
    }


    private function handleResource()
    {
        // Criar resource
    }


    private function runMigration()
    {
        Artisan::call('migrate');
    }
}
