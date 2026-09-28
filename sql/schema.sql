SET NAMES utf8mb4;
SET time_zone = '+01:00';

-- ============================================================
-- ADMINISTRADORES
-- ============================================================
CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  ultimo_acesso DATETIME DEFAULT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CONFIGURAÇÕES GERAIS (chave-valor)
-- ============================================================
CREATE TABLE settings (
  chave VARCHAR(80) PRIMARY KEY,
  valor TEXT,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SLIDES DO HERO
-- ============================================================
CREATE TABLE slides (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(10) NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  legenda VARCHAR(200) DEFAULT NULL,
  imagem VARCHAR(255) DEFAULT NULL,
  ordem INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- EIXOS TEMÁTICOS
-- ============================================================
CREATE TABLE eixos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(10) NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  ordem INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eixo_cursos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  eixo_id INT UNSIGNED NOT NULL,
  nome VARCHAR(180) NOT NULL,
  ordem INT NOT NULL DEFAULT 0,
  FOREIGN KEY (eixo_id) REFERENCES eixos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PROGRAMA
-- ============================================================
CREATE TABLE programa_dias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(10) NOT NULL,
  data VARCHAR(80) NOT NULL,
  semana VARCHAR(40) NOT NULL,
  intro VARCHAR(255) NOT NULL,
  flyer VARCHAR(255) DEFAULT NULL,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INOVA IPS
-- ============================================================
CREATE TABLE inova_blocos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT NOT NULL,
  ordem INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inova_criterios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(180) NOT NULL,
  percentagem TINYINT UNSIGNED NOT NULL,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inova_listas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  grupo ENUM('participacao','aceites') NOT NULL,
  texto VARCHAR(255) NOT NULL,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PREMIAÇÃO
-- ============================================================
CREATE TABLE premios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lugar VARCHAR(60) NOT NULL,
  destaque TINYINT(1) NOT NULL DEFAULT 0,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE premio_itens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  premio_id INT UNSIGNED NOT NULL,
  texto VARCHAR(255) NOT NULL,
  nota VARCHAR(255) DEFAULT NULL,
  ordem INT NOT NULL DEFAULT 0,
  FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MODALIDADES DE PARTICIPAÇÃO
-- ============================================================
CREATE TABLE modalidades (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  letra VARCHAR(4) NOT NULL,
  titulo VARCHAR(120) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE modalidade_itens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  modalidade_id INT UNSIGNED NOT NULL,
  texto VARCHAR(120) NOT NULL,
  ordem INT NOT NULL DEFAULT 0,
  FOREIGN KEY (modalidade_id) REFERENCES modalidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PRAZOS DE SUBMISSÃO
-- ============================================================
CREATE TABLE prazos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  data VARCHAR(80) NOT NULL,
  titulo VARCHAR(120) NOT NULL,
  destaque TINYINT(1) NOT NULL DEFAULT 0,
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE prazo_itens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  prazo_id INT UNSIGNED NOT NULL,
  texto VARCHAR(180) NOT NULL,
  ordem INT NOT NULL DEFAULT 0,
  FOREIGN KEY (prazo_id) REFERENCES prazos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CRONOGRAMA
-- ============================================================
CREATE TABLE cronograma (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  data VARCHAR(20) NOT NULL,
  texto VARCHAR(180) NOT NULL,
  tipo ENUM('normal','evento') NOT NULL DEFAULT 'normal',
  ordem INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- GALERIA (secção "Brevemente" com estado configurável)
-- ============================================================
CREATE TABLE galeria_itens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  imagem VARCHAR(255) NOT NULL,
  legenda VARCHAR(200) DEFAULT NULL,
  ordem INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SUBMISSÕES
-- ============================================================
CREATE TABLE submissoes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('comunicacao','poster','projeto_inova') NOT NULL,
  eixo VARCHAR(10) DEFAULT NULL,
  titulo VARCHAR(255) NOT NULL,
  autores TEXT NOT NULL,
  instituicao VARCHAR(190) DEFAULT NULL,
  email VARCHAR(190) NOT NULL,
  telefone VARCHAR(40) DEFAULT NULL,
  resumo TEXT NOT NULL,
  palavras_chave VARCHAR(255) DEFAULT NULL,
  area_inova VARCHAR(60) DEFAULT NULL,
  elementos_equipa TEXT DEFAULT NULL,
  ficheiro_original VARCHAR(255) DEFAULT NULL,
  ficheiro_guardado VARCHAR(255) DEFAULT NULL,
  ficheiro_tamanho INT UNSIGNED DEFAULT NULL,
  status ENUM('pendente','em_analise','aceite','rejeitado')
    NOT NULL DEFAULT 'pendente',
  observacoes TEXT DEFAULT NULL,
  avaliado_por INT UNSIGNED DEFAULT NULL,
  avaliado_em DATETIME DEFAULT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_tipo (tipo),
  INDEX idx_criado (criado_em DESC),
  FOREIGN KEY (avaliado_por) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOGS
-- ============================================================
CREATE TABLE logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id INT UNSIGNED DEFAULT NULL,
  acao VARCHAR(60) NOT NULL,
  detalhe TEXT DEFAULT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_logs_criado (criado_em DESC),
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;