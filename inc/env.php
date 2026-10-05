<?php
declare(strict_types=1);

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

function env(string $chave, $padrao = null)
{
    $valor = getenv($chave);

    if ($valor === false) {
        return $padrao;
    }

    return $valor;
}

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