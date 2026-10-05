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
function env_carregar(string $caminho): void
{
    if (!is_file($caminho)) {
        return;
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas === false) {
        return;
    }

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        // Ignora comentários
        if ($linha === '' || $linha[0] === '#') {
            continue;
        }

        // Divide em CHAVE=valor
        $pos = strpos($linha, '=');
        if ($pos === false) {
            continue;
        }

        $chave = trim(substr($linha, 0, $pos));
        $valor = trim(substr($linha, $pos + 1));

        // Remove aspas se existirem
        if (strlen($valor) >= 2) {
            $primeiro = $valor[0];
            $ultimo = $valor[strlen($valor) - 1];
            if (($primeiro === '"' && $ultimo === '"') || ($primeiro === "'" && $ultimo === "'")) {
                $valor = substr($valor, 1, -1);
            }
        }

        // Converte "true"/"false"/"null" (sem aspas) para boolean/null
        $valorLower = strtolower($valor);
        if ($valorLower === 'true')  $valor = '1';
        if ($valorLower === 'false') $valor = '';

        // Não sobrepõe variáveis já definidas no sistema
        if (getenv($chave) !== false) {
            continue;
        }

        putenv($chave . '=' . $valor);
        $_ENV[$chave] = $valor;
        $_SERVER[$chave] = $valor;
    }
}

// function env(string $chave, $padrao = null)
// {
//     $valor = getenv($chave);

//     if ($valor === false) {
//         return $padrao;
//     }

//     return $valor;
// }

function env_bool(string $chave, bool $padrao = false): bool
{
    $valor = env($chave);
    if ($valor === null) return $padrao;

    $lower = strtolower(trim((string) $valor));
    return in_array($lower, ['1', 'true', 'sim', 'yes', 'on'], true);
}

function env_int(string $chave, int $padrao = 0): int
{
    $valor = env($chave);
    if ($valor === null || $valor === '') return $padrao;
    return (int) $valor;
}
