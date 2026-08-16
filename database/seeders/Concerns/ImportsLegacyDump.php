<?php

namespace Database\Seeders\Concerns;

use mysqli;
use PDO;
use RuntimeException;

trait ImportsLegacyDump
{
    private ?PDO $legacyPdo = null;

    /**
     * Import ex_db/db_qa2025.sql into a scratch database and return a PDO
     * connection to it. The dump has no application code depending on it,
     * so it is safe to drop and re-import on every seeder run.
     */
    protected function legacyPdo(): PDO
    {
        if ($this->legacyPdo instanceof PDO) {
            return $this->legacyPdo;
        }

        $host = config('database.connections.mysql.host');
        $port = (int) config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = 'db_qa2025_legacy';

        $bootstrap = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $bootstrap->exec("DROP DATABASE IF EXISTS `{$database}`");
        $bootstrap->exec("CREATE DATABASE `{$database}` CHARACTER SET utf8 COLLATE utf8_unicode_ci");

        $mysqli = new mysqli($host, $username, $password, $database, $port);
        $mysqli->set_charset('utf8');

        $sql = file_get_contents(base_path('ex_db/db_qa2025.sql'));

        $mysqli->multi_query($sql);

        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());

        if ($mysqli->error) {
            $error = $mysqli->error;
            $mysqli->close();

            throw new RuntimeException("Failed to import ex_db/db_qa2025.sql: {$error}");
        }

        $mysqli->close();

        return $this->legacyPdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
