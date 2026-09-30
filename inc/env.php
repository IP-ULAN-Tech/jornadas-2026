<?php
declare(strict_types=1);

/**
 * Leitor mínimo de configuração por variáveis de ambiente (sem Composer).
 *
 * Prioridade: variáveis reais do ambiente (getenv / $_SERVER) > ficheiro .env
 * O ficheiro .env é procurado, por ordem, em:
 *   1. caminho indicado em JORNADAS_ENV_FILE
 *   2. pasta acima da raiz do site  (../jornadas.env)  <- recomendado
 *   3. raiz do site                 (./.env)
 */

function env_file_values(): array
{
    static $values = null;
    if ($values !== null) return $values;
    $values = [];

    $root = dirname(__DIR__);
    $candidates = array_filter([
        getenv('JORNADAS_ENV_FILE') ?: null,
        dirname($root) . '/jornadas.env',
        $root . '/.env',
    ]);

    foreach ($candidates as $path) {
        if (!is_file($path) || !is_readable($path)) continue;
        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
            [$k, $v] = explode('=', $line, 2);
            $v = trim($v);
            if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && $v[-1] === $v[0]) {
                $v = substr($v, 1, -1);
            }
            $values[trim($k)] = $v;
        }
        break; // usa o primeiro ficheiro encontrado
    }
    return $values;
}

/** Valor da variável, ou null se não estiver definida (vazio conta como definido). */
function env_raw(string $key): ?string
{
    $v = getenv($key);
    if ($v !== false) return (string)$v;
    if (isset($_SERVER[$key]) && is_string($_SERVER[$key])) return $_SERVER[$key];
    $file = env_file_values();
    return array_key_exists($key, $file) ? $file[$key] : null;
}

function env(string $key, ?string $default = null): ?string
{
    return env_raw($key) ?? $default;
}

/** Variável obrigatória: se faltar, regista o erro e devolve 500 sem expor detalhes. */
function env_required(string $key): string
{
    $v = env_raw($key);
    if ($v === null) {
        error_log("[jornadas] Configuração em falta: variável $key");
        if (PHP_SAPI !== 'cli') http_response_code(500);
        exit('Erro de configuração do servidor.');
    }
    return $v;
}
