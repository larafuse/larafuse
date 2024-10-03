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
    public $seedBasePath = 'Database\Seeders\\';

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
        // $this->handleSeed();
        // $this->handlePolicy();
        // $this->handleResource();
        // $this->runMigration();


        // Rodar migration


        // Tratar os labels dos fields

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

    private function handleModelMigration()
    {
        // Criar model c/ migration
        $command = "make:model {$this->data['title']} -m";
        Artisan::call($command);

        // Caminho do arquivo da Model e da Migration
        $modelPath = app_path("Models/{$this->data['title']}.php");
        $migrationPath = database_path('migrations/' . now()->format('Y_m_d_His') . '_create_' . strtolower($this->data['title']) . 's_table.php');

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

    private function handleSeed()
    {
        // Criar seed
        // Inserir seed no DatabaseSeeder
    }

    private function handlePolicy()
    {
        // Criar Policy
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
