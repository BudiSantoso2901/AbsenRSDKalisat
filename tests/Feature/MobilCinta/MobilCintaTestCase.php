<?php

namespace Tests\Feature\MobilCinta;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

abstract class MobilCintaTestCase extends TestCase
{
    protected bool $runFinalSchemaMigration = true;

    private array $originalDatabaseEnvironment = [];

    private array $legacyColumnsBeforeModuleMigrations = [];
    private array $legacyMobilCintaColumnsBeforeFinalMigration = [];

    protected function setUp(): void
    {
        foreach (['DB_CONNECTION', 'DB_DATABASE'] as $key) {
            $this->originalDatabaseEnvironment[$key] = [
                'process' => getenv($key),
                'env' => $_ENV[$key] ?? null,
                'server' => $_SERVER[$key] ?? null,
            ];
        }

        $this->setEnvironmentValue('DB_CONNECTION', 'sqlite');
        $this->setEnvironmentValue('DB_DATABASE', ':memory:');

        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::connection('sqlite')->getPdo();

        $this->createLegacySchemaFixture();
        $this->legacyColumnsBeforeModuleMigrations = $this->legacyColumns();
        $this->runMobilCintaMigrations();
        $this->legacyMobilCintaColumnsBeforeFinalMigration = $this->legacyMobilCintaColumns();

        if ($this->runFinalSchemaMigration) {
            $this->runFinalMigration();
            $this->runSchemaAdjustmentMigration();
        }
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            foreach ($this->originalDatabaseEnvironment as $key => $values) {
                if ($values['process'] === false) {
                    putenv($key);
                } else {
                    putenv($key.'='.$values['process']);
                }

                if ($values['env'] === null) {
                    unset($_ENV[$key]);
                } else {
                    $_ENV[$key] = $values['env'];
                }

                if ($values['server'] === null) {
                    unset($_SERVER[$key]);
                } else {
                    $_SERVER[$key] = $values['server'];
                }
            }
        }
    }

    private function setEnvironmentValue(string $key, string $value): void
    {
        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    protected function legacyColumnsBeforeModuleMigration(): array
    {
        return $this->legacyColumnsBeforeModuleMigrations;
    }

    protected function legacyColumns(): array
    {
        $columns = [];

        foreach (['users', 'pegawai', 'absensi', 'absenkontens', 'ruangans'] as $table) {
            $columns[$table] = Schema::getColumnListing($table);
        }

        return $columns;
    }

    protected function legacyMobilCintaColumnsBeforeFinalMigration(): array
    {
        return $this->legacyMobilCintaColumnsBeforeFinalMigration;
    }

    protected function legacyMobilCintaColumns(): array
    {
        $columns = [];

        foreach ([
            'mobil_cinta_roles', 'mobil_cinta_accounts', 'mobil_cinta_vehicles', 'mobil_cinta_guarantors',
            'mobil_cinta_trip_requests', 'mobil_cinta_assignments', 'mobil_cinta_journeys', 'mobil_cinta_status_histories',
        ] as $table) {
            $columns[$table] = Schema::getColumnListing($table);
        }

        return $columns;
    }

    private function createLegacySchemaFixture(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan');
            $table->timestamps();
        });

        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('nip')->unique();
            $table->date('tanggal_lahir');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('id_jabatan');
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_jam_kerja');
            $table->string('foto_pegawai')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        Schema::create('absenkontens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->unsignedBigInteger('id_ruangan')->nullable();
            $table->timestamps();
        });
    }

    protected function runMobilCintaMigrations(): void
    {
        $migrationFiles = [
            '2026_10_26_000001_create_mobil_cinta_roles_table.php',
            '2026_10_26_000002_create_mobil_cinta_accounts_table.php',
            '2026_10_26_000003_create_mobil_cinta_vehicles_table.php',
            '2026_10_26_000004_create_mobil_cinta_guarantors_table.php',
            '2026_10_26_000005_create_mobil_cinta_trip_requests_table.php',
            '2026_10_26_000006_create_mobil_cinta_assignments_table.php',
            '2026_10_26_000007_create_mobil_cinta_journeys_table.php',
            '2026_10_26_000008_create_mobil_cinta_status_histories_table.php',
        ];

        foreach ($migrationFiles as $migrationFile) {
            $exitCode = Artisan::call('migrate', [
                '--path' => ['database/migrations/'.$migrationFile],
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                throw new RuntimeException(Artisan::output());
            }
        }
    }

    protected function runFinalMigration(): void
    {
        $exitCode = Artisan::call('migrate', [
            '--path' => ['database/migrations/2026_10_28_000001_create_mobil_cinta_final_schema.php'],
            '--force' => true,
        ]);

        if ($exitCode !== 0) {
            throw new RuntimeException(Artisan::output());
        }
    }

    protected function runSchemaAdjustmentMigration(): void
    {
        $exitCode = Artisan::call('migrate', [
            '--path' => ['database/migrations/2026_10_29_000001_rename_mobil_cinta_journey_coordinates_add_driver_name.php'],
            '--force' => true,
        ]);
        if ($exitCode !== 0) {
            throw new RuntimeException(Artisan::output());
        }
    }

    protected function runCleanupMigration(): void
    {
        $exitCode = Artisan::call('migrate', [
            '--path' => ['database/migrations/2026_10_29_000002_cleanup_legacy_mobil_cinta_tables.php'],
            '--force' => true,
        ]);
        if ($exitCode !== 0) {
            throw new RuntimeException(Artisan::output());
        }
    }
}
