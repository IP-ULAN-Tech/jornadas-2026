-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28-Set-2026 às 19:34
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `jornadas_ips_2026`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `nivel` tinyint(4) NOT NULL DEFAULT 2,
  `notas` varchar(255) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `admins`
--

INSERT INTO `admins` (`id`, `nome`, `email`, `password_hash`, `ativo`, `nivel`, `notas`, `ultimo_acesso`, `criado_em`) VALUES
(1, 'waltercio ivulo', 'waltercio@gmail.com', '$2y$12$ZpLAE9ypLT7n1Pz5kEZhlOIQyNVrRY88DzpigRcTYjAubJdEHyusi', 1, 1, NULL, '2026-09-28 10:04:25', '2026-09-28 09:25:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cronograma`
--

CREATE TABLE `cronograma` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` varchar(20) NOT NULL,
  `texto` varchar(180) NOT NULL,
  `tipo` enum('normal','evento') NOT NULL DEFAULT 'normal',
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `cronograma`
--

INSERT INTO `cronograma` (`id`, `data`, `texto`, `tipo`, `ordem`) VALUES
(1, '15 SET', 'Lançamento oficial do Regulamento', 'normal', 1),
(2, '15 OUT', 'Prazo de submissão', 'normal', 2),
(3, '03 NOV', 'Notificação de aceitação', 'normal', 3),
(4, '08 NOV', 'Entrega das versões finais', 'normal', 4),
(5, '12 NOV', 'Dia 01 das Jornadas', 'evento', 5),
(6, '13 NOV', 'Dia 02 e Premiação', 'evento', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `eixos`
--

CREATE TABLE `eixos` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(10) NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `eixos`
--

INSERT INTO `eixos` (`id`, `numero`, `titulo`, `ordem`, `ativo`, `criado_em`) VALUES
(1, '01', 'Recursos Naturais e Sustentabilidade', 1, 1, '2026-09-25 15:20:48'),
(2, '02', 'Infraestrutura e Desenvolvimento Tecnológico', 2, 1, '2026-09-25 15:20:48'),
(3, '03', 'Saúde e Gestão Pública', 3, 1, '2026-09-25 15:20:48'),
(4, '04', 'Educação e Formação Humana', 4, 1, '2026-09-25 15:20:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `eixo_cursos`
--

CREATE TABLE `eixo_cursos` (
  `id` int(10) UNSIGNED NOT NULL,
  `eixo_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(180) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `eixo_cursos`
--

INSERT INTO `eixo_cursos` (`id`, `eixo_id`, `nome`, `ordem`) VALUES
(1, 1, 'Engenharia em Geologia', 1),
(2, 1, 'Engenharia de Minas', 2),
(3, 1, 'Engenharia de Metalurgia e Materiais', 3),
(4, 2, 'Engenharia de Construção Civil', 1),
(5, 2, 'Engenharia Electromecânica', 2),
(6, 2, 'Engenharia Informática', 3),
(7, 3, 'Enfermagem', 1),
(8, 3, 'Administração e Gestão', 2),
(9, 4, 'Ensino de História', 1),
(10, 4, 'Ensino de Matemática', 2),
(11, 4, 'Ensino de Geografia', 3),
(12, 4, 'Ensino Primário', 4),
(13, 4, 'Educação de Infância', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `galeria_itens`
--

CREATE TABLE `galeria_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(200) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `inova_blocos`
--

CREATE TABLE `inova_blocos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `inova_blocos`
--

INSERT INTO `inova_blocos` (`id`, `titulo`, `descricao`, `ordem`, `ativo`) VALUES
(1, 'Exposição', 'Os projectos ficam expostos durante os dois dias das Jornadas, com contacto directo com o público, docentes, empresas e instituições convidadas.', 1, 1),
(2, 'Projectos admitidos', 'Protótipos funcionais, modelos demonstrativos, aplicações informáticas e sistemas de automação desenvolvidos pelos estudantes do IPS.', 2, 1),
(3, 'Empreendedorismo', 'Soluções para problemas reais da comunidade, com enfoque em saúde, educação, mineração, construção e outras áreas.', 3, 1),
(4, 'Relação institucional', 'Ligação entre o Instituto Politécnico de Saurimo e o tecido empresarial e institucional da Lunda Sul.', 4, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `inova_criterios`
--

CREATE TABLE `inova_criterios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(180) NOT NULL,
  `percentagem` tinyint(3) UNSIGNED NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `inova_criterios`
--

INSERT INTO `inova_criterios` (`id`, `nome`, `percentagem`, `ordem`) VALUES
(1, 'Originalidade e criatividade da ideia', 25, 1),
(2, 'Rigor técnico e científico', 25, 2),
(3, 'Relevância para o contexto angolano', 20, 3),
(4, 'Qualidade da apresentação e demonstração', 20, 4),
(5, 'Potencial de aplicação real', 10, 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `inova_listas`
--

CREATE TABLE `inova_listas` (
  `id` int(10) UNSIGNED NOT NULL,
  `grupo` enum('participacao','aceites') NOT NULL,
  `texto` varchar(255) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `inova_listas`
--

INSERT INTO `inova_listas` (`id`, `grupo`, `texto`, `ordem`) VALUES
(1, 'participacao', 'Todos os estudantes regularmente matriculados no IPS.', 1),
(2, 'participacao', 'Individualmente ou em grupos de até 4 elementos.', 2),
(3, 'participacao', 'São permitidos grupos interdisciplinares.', 3),
(4, 'aceites', 'Protótipos funcionais e modelos demonstrativos', 1),
(5, 'aceites', 'Aplicações informáticas e sistemas de automação', 2),
(6, 'aceites', 'Soluções para problemas reais da comunidade', 3),
(7, 'aceites', 'Estudos de caso com aplicação prática em Angola', 4),
(8, 'aceites', 'Inovações em saúde, educação, mineração e construção', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `inova_projetos`
--

CREATE TABLE `inova_projetos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) NOT NULL,
  `curso` varchar(150) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `inova_projetos`
--

INSERT INTO `inova_projetos` (`id`, `titulo`, `descricao`, `imagem`, `curso`, `ordem`, `ativo`, `criado_em`) VALUES
(1, 'teste', 'nnn', 'inova/20260928120750-7527f44f.jpg', 'mmm', 1, 1, '2026-09-28 11:07:50'),
(2, 'teste2', 'hhhhh', 'inova/20260928120843-0065e8ae.jpg', 'vvv', 2, 1, '2026-09-28 11:08:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `acao` varchar(60) NOT NULL,
  `detalhe` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `logs`
--

INSERT INTO `logs` (`id`, `admin_id`, `acao`, `detalhe`, `ip`, `criado_em`) VALUES
(1, 1, 'login', NULL, '::1', '2026-09-28 09:25:32'),
(2, 1, 'editar', 'slides #1', '::1', '2026-09-28 09:37:24'),
(3, 1, 'editar', 'slides #1', '::1', '2026-09-28 10:03:06'),
(4, 1, 'login', NULL, '::1', '2026-09-28 10:04:25'),
(5, 1, 'editar', 'slides #2', '::1', '2026-09-28 10:12:54'),
(6, 1, 'editar', 'slides #3', '::1', '2026-09-28 10:13:04'),
(7, 1, 'editar', 'slides #3', '::1', '2026-09-28 10:13:18'),
(8, 1, 'editar', 'slides #4', '::1', '2026-09-28 10:13:29'),
(9, 1, 'editar', 'programa_dias #1', '::1', '2026-09-28 10:48:47'),
(10, 1, 'editar', 'programa_dias #2', '::1', '2026-09-28 10:49:23'),
(11, 1, 'criar', 'premio_itens', '::1', '2026-09-28 11:05:51'),
(12, 1, 'criar', 'premio_fotos', '::1', '2026-09-28 11:06:20'),
(13, 1, 'criar', 'inova_projetos', '::1', '2026-09-28 11:07:50'),
(14, 1, 'criar', 'inova_projetos', '::1', '2026-09-28 11:08:43'),
(15, 1, 'criar', 'galeria_itens #1', '::1', '2026-09-28 11:09:40'),
(16, NULL, 'nova_submissao', 'id=2 tipo=projeto_inova', '::1', '2026-09-28 11:15:28'),
(17, 1, 'editar', 'prazos #1', '::1', '2026-09-28 11:26:22'),
(18, 1, 'editar', 'prazos #1', '::1', '2026-09-28 11:26:29'),
(19, 1, 'editar', 'cronograma #1', '::1', '2026-09-28 11:26:37'),
(20, 1, 'editar', 'cronograma #1', '::1', '2026-09-28 11:27:00'),
(21, 1, 'editar', 'premios #1', '::1', '2026-09-28 11:38:51'),
(22, NULL, 'nova_submissao', 'id=3 tipo=projeto_inova', '::1', '2026-09-28 14:18:17'),
(23, 1, 'atualizar_submissao', 'id=3 status=aceite', '::1', '2026-09-28 15:44:36'),
(24, 1, 'editar', 'local', '::1', '2026-09-28 15:48:08'),
(25, 1, 'editar', 'slides #1', '::1', '2026-09-28 16:02:28'),
(26, 1, 'editar', 'eixos #1', '::1', '2026-09-28 16:09:37'),
(27, 1, 'editar', 'eixos #1', '::1', '2026-09-28 16:13:02'),
(28, 1, 'editar', 'programa_dias #1', '::1', '2026-09-28 16:13:44'),
(29, 1, 'eliminar', 'premios #1', '::1', '2026-09-28 16:18:50'),
(30, 1, 'eliminar', 'galeria_itens #1', '::1', '2026-09-28 16:19:24'),
(31, 1, 'criar_admin', 'email=benvindomanamersh45@gmail.com nivel=2', '::1', '2026-09-28 16:22:07'),
(32, 1, 'logout', NULL, '::1', '2026-09-28 16:22:17'),
(33, NULL, 'login', 'nivel=2', '::1', '2026-09-28 18:15:49'),
(34, NULL, 'logout', NULL, '::1', '2026-09-28 18:32:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `letra` varchar(4) NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `modalidades`
--

INSERT INTO `modalidades` (`id`, `letra`, `titulo`, `descricao`, `ordem`) VALUES
(1, 'A', 'Comunicação Oral', 'Apresentações com duração de 15 minutos, seguidas de 5 minutos para perguntas e debate.', 1),
(2, 'B', 'Poster Científico', 'Formato A1. O poster deve conter os seguintes elementos:', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modalidade_itens`
--

CREATE TABLE `modalidade_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `modalidade_id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(120) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `modalidade_itens`
--

INSERT INTO `modalidade_itens` (`id`, `modalidade_id`, `texto`, `ordem`) VALUES
(1, 2, 'Título', 1),
(2, 2, 'Autores', 2),
(3, 2, 'Introdução', 3),
(4, 2, 'Metodologia', 4),
(5, 2, 'Resultados', 5),
(6, 2, 'Conclusões', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prazos`
--

CREATE TABLE `prazos` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` varchar(80) NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `prazos`
--

INSERT INTO `prazos` (`id`, `data`, `titulo`, `destaque`, `ordem`) VALUES
(1, '15 de Outubro de 2026', 'Prazo de submissão', 1, 1),
(2, '3 de Novembro', 'Notificação de aceitação', 0, 2),
(3, '8 de Novembro', 'Entrega das versões finais', 0, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `prazo_itens`
--

CREATE TABLE `prazo_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `prazo_id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(180) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `prazo_itens`
--

INSERT INTO `prazo_itens` (`id`, `prazo_id`, `texto`, `ordem`) VALUES
(1, 1, 'Resumos', 1),
(2, 1, 'Comunicações', 2),
(3, 1, 'Posters', 3),
(4, 1, 'Fichas de inscrição dos projectos da INOVA IPS', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `premios`
--

CREATE TABLE `premios` (
  `id` int(10) UNSIGNED NOT NULL,
  `lugar` varchar(60) NOT NULL,
  `nome` varchar(180) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `premios`
--

INSERT INTO `premios` (`id`, `lugar`, `nome`, `foto`, `descricao`, `destaque`, `ordem`) VALUES
(2, '2.º Lugar', NULL, NULL, NULL, 0, 2),
(3, '3.º Lugar', NULL, NULL, NULL, 0, 3),
(4, 'Menção Honrosa', NULL, NULL, NULL, 0, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `premio_fotos`
--

CREATE TABLE `premio_fotos` (
  `id` int(10) UNSIGNED NOT NULL,
  `premio_id` int(10) UNSIGNED NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `nome` varchar(150) DEFAULT NULL,
  `legenda` varchar(200) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `premio_itens`
--

CREATE TABLE `premio_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `premio_id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(255) NOT NULL,
  `nota` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `premio_itens`
--

INSERT INTO `premio_itens` (`id`, `premio_id`, `texto`, `nota`, `ordem`) VALUES
(4, 2, 'Diploma de Mérito', NULL, 1),
(5, 2, 'Trofeu INOVA IPS 2026', NULL, 2),
(6, 3, 'Diploma de Mérito', NULL, 1),
(7, 3, 'Trofeu INOVA IPS 2026', NULL, 2),
(8, 4, 'Diploma de Reconhecimento', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `programa_dias`
--

CREATE TABLE `programa_dias` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(10) NOT NULL,
  `data` varchar(80) NOT NULL,
  `semana` varchar(40) NOT NULL,
  `intro` varchar(255) NOT NULL,
  `flyer` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `programa_dias`
--

INSERT INTO `programa_dias` (`id`, `numero`, `data`, `semana`, `intro`, `flyer`, `ordem`) VALUES
(1, 'Dia 01', '12 de Novembro de 2026', 'Quinta-feira', 'Primeiro dia das Jornadas Técnico-Científicas', 'programa/20260928171344-f8d572c4.jpg', 1),
(2, 'Dia 02', '13 de Novembro de 2026', 'Sexta-feira', 'Segundo dia das Jornadas Técnico-Científicas', 'programa/20260928114923-04b20d88.jpg', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `settings`
--

CREATE TABLE `settings` (
  `chave` varchar(80) NOT NULL,
  `valor` text DEFAULT NULL,
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `settings`
--

INSERT INTO `settings` (`chave`, `valor`, `atualizado_em`) VALUES
('evento_contexto', '51.º Aniversário da Independência de Angola', '2026-09-25 15:20:48'),
('evento_data', '12 – 13 de Novembro de 2026', '2026-09-25 15:20:48'),
('evento_edicao', 'Edição 2026', '2026-09-25 15:20:48'),
('evento_instituicao', 'Universidade Lueji A\'Nkonde', '2026-09-25 15:20:48'),
('evento_lema', 'Saber para Construir · Investigar para Transformar', '2026-09-25 15:20:48'),
('evento_local_curto', 'Saurimo, Lunda Sul', '2026-09-25 15:20:48'),
('evento_organizacao', 'Instituto Politécnico de Saurimo', '2026-09-25 15:20:48'),
('evento_tema', 'Ciência e Tecnologia ao Serviço do Desenvolvimento de Angola', '2026-09-25 15:20:48'),
('evento_titulo_1', 'Jornadas', '2026-09-25 15:20:48'),
('evento_titulo_2', 'Técnico-Científicas', '2026-09-25 15:20:48'),
('galeria_brevemente_nota', 'A galeria de registos fotográficos das Jornadas será disponibilizada após o evento.', '2026-09-25 15:20:48'),
('galeria_brevemente_texto', 'Brevemente', '2026-09-25 15:20:48'),
('galeria_estado', 'brevemente', '2026-09-25 15:20:48'),
('inova_imagem', NULL, '2026-09-25 15:20:48'),
('inova_intro', 'A INOVA IPS 2026 é o espaço de exibição e competição dos projectos de inovação desenvolvidos pelos estudantes do Instituto Politécnico de Saurimo.', '2026-09-25 15:20:48'),
('local_cidade', 'Saurimo — Lunda Sul', '2026-09-25 15:20:48'),
('local_contacto', '', '2026-09-28 15:48:08'),
('local_endereco', 'tx', '2026-09-28 15:48:08'),
('local_indicacoes', '', '2026-09-28 15:48:08'),
('local_mapa', '', '2026-09-25 15:20:48'),
('local_mapa_url', '', '2026-09-28 15:48:08'),
('local_nome', 'Instituto Politécnico de Saurimo', '2026-09-25 15:20:48'),
('local_pais', 'Angola', '2026-09-25 15:20:48'),
('numeros_cursos', '13', '2026-09-25 15:20:48'),
('numeros_dias', '02', '2026-09-25 15:20:48'),
('numeros_eixos', '04', '2026-09-25 15:20:48'),
('numeros_feira', 'INOVA IPS', '2026-09-25 15:20:48'),
('premiacao_brevemente_nota', 'Os vencedores da INOVA IPS 2026 serão anunciados durante a cerimónia de encerramento, no dia 13 de Novembro de 2026.', '2026-09-25 15:20:48'),
('premiacao_brevemente_texto', 'Brevemente', '2026-09-25 15:20:48'),
('premiacao_estado', 'brevemente', '2026-09-25 15:20:48'),
('rodape_lema', 'Unidade, Trabalho e Compromisso', '2026-09-25 15:20:48'),
('site_logo', 'geral/20260928171659-7a1490fc.png', '2026-09-28 16:16:59'),
('sobre_abertura', 'As Jornadas Científicas constituem uma iniciativa estratégica de promoção da investigação e do saber académico no seio do Instituto Politécnico de Saurimo. ', '2026-09-28 16:12:53'),
('sobre_imagem', 'sobre/20260928110442-c448aee3.jpg', '2026-09-28 10:04:42'),
('sobre_legenda', 'Edição anterior das Jornadas Científicas', '2026-09-25 15:20:48'),
('sobre_paragrafo_1', 'A edição de 2026 decorre no contexto das celebrações do 51.º aniversário da Independência de Angola e valoriza o conhecimento científico e tecnológico produzido no IPS, afirmando o papel da instituição no desenvolvimento da Lunda Sul e do país.', '2026-09-25 15:20:48'),
('sobre_paragrafo_2', 'Paralelamente, a Feira de Inovação Tecnológica INOVA IPS 2026 constitui o espaço de exibição e competição dos projectos desenvolvidos pelos estudantes, aproximando o Instituto do tecido empresarial e institucional da região.', '2026-09-25 15:20:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `slides`
--

CREATE TABLE `slides` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(10) NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `legenda` varchar(200) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `slides`
--

INSERT INTO `slides` (`id`, `numero`, `titulo`, `legenda`, `imagem`, `ordem`, `ativo`, `criado_em`) VALUES
(1, '01', 'Campus do Instituto Politécnico de Saurimo e', 'Campus do Instituto Politécnico de Saurimo', 'slides/20260928110306-30452a26.jpg', 1, 1, '2026-09-25 15:20:48'),
(2, '02', 'Laboratórios de engenharia', 'Laboratórios de engenharia', 'slides/20260928111254-0595d686.jpg', 2, 1, '2026-09-25 15:20:48'),
(3, '03', 'Edição anterior das Jornadas', 'Edição anterior das Jornadas', 'slides/20260928111318-30a756d0.jpg', 3, 1, '2026-09-25 15:20:48'),
(4, '04', 'Projectos da INOVA IPS', 'Projectos da INOVA IPS', 'slides/20260928111329-0507bc06.jpg', 4, 1, '2026-09-25 15:20:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `submissoes`
--

CREATE TABLE `submissoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('comunicacao','poster','projeto_inova') NOT NULL,
  `eixo` varchar(10) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `autores` text NOT NULL,
  `instituicao` varchar(190) DEFAULT NULL,
  `email` varchar(190) NOT NULL,
  `telefone` varchar(40) DEFAULT NULL,
  `resumo` text NOT NULL,
  `palavras_chave` varchar(255) DEFAULT NULL,
  `area_inova` varchar(60) DEFAULT NULL,
  `elementos_equipa` text DEFAULT NULL,
  `ficheiro_original` varchar(255) DEFAULT NULL,
  `ficheiro_guardado` varchar(255) DEFAULT NULL,
  `ficheiro_tamanho` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pendente','em_analise','aceite','rejeitado') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `avaliado_por` int(10) UNSIGNED DEFAULT NULL,
  `avaliado_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `submissoes`
--

INSERT INTO `submissoes` (`id`, `tipo`, `eixo`, `titulo`, `autores`, `instituicao`, `email`, `telefone`, `resumo`, `palavras_chave`, `area_inova`, `elementos_equipa`, `ficheiro_original`, `ficheiro_guardado`, `ficheiro_tamanho`, `status`, `observacoes`, `avaliado_por`, `avaliado_em`, `criado_em`, `atualizado_em`) VALUES
(1, 'projeto_inova', NULL, 'teste', 'benvindo mana', 'ooo', 'benvindomanamersh45@gmail.com', '940946194', 'hhhhhhhmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', 'gg,uu,', 'tecnologia', 'bbbb', 'Factura reciboSETEMBRO.pdf', '20260928104245-95d9c1cc.pdf', 114602, 'pendente', NULL, NULL, NULL, '2026-09-28 09:42:45', '2026-09-28 09:42:45'),
(2, 'projeto_inova', '01', 'Projecto RR', 'Benvindo Mana, Jamba Batista,Terencio Narciso', 'Instituto Superior da Lunda Sul', 'benvindomanamersh45@gmail.com', '940946100', 'gbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'kk,uuu,', 'tecnologia', 'nnnn,jjjj,ooo,rrrq.', 'akademiadb.pdf', '20260928121526-522fe230.pdf', 326280, 'pendente', NULL, NULL, NULL, '2026-09-28 11:15:26', '2026-09-28 11:15:26'),
(3, 'projeto_inova', NULL, 'y', 'bb,ii', 'Instituto Superior da Lunda Sul', 'graca@gmail.com', '940946190', 'eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', 'kk,uuu,', 'tecnologia', 'nnnn.hhhhyyw,nnns', '1akademiadb.pdf', '20260928151815-8b987196.pdf', 326490, 'aceite', '', 1, '2026-09-28 15:44:36', '2026-09-28 14:18:15', '2026-09-28 15:44:36');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `cronograma`
--
ALTER TABLE `cronograma`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `eixos`
--
ALTER TABLE `eixos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `eixo_cursos`
--
ALTER TABLE `eixo_cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eixo_id` (`eixo_id`);

--
-- Índices para tabela `galeria_itens`
--
ALTER TABLE `galeria_itens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `inova_blocos`
--
ALTER TABLE `inova_blocos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `inova_criterios`
--
ALTER TABLE `inova_criterios`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `inova_listas`
--
ALTER TABLE `inova_listas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `inova_projetos`
--
ALTER TABLE `inova_projetos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ordem` (`ordem`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_criado` (`criado_em`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Índices para tabela `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `modalidade_itens`
--
ALTER TABLE `modalidade_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modalidade_id` (`modalidade_id`);

--
-- Índices para tabela `prazos`
--
ALTER TABLE `prazos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `prazo_itens`
--
ALTER TABLE `prazo_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prazo_id` (`prazo_id`);

--
-- Índices para tabela `premios`
--
ALTER TABLE `premios`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `premio_fotos`
--
ALTER TABLE `premio_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_premio` (`premio_id`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices para tabela `premio_itens`
--
ALTER TABLE `premio_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `premio_id` (`premio_id`);

--
-- Índices para tabela `programa_dias`
--
ALTER TABLE `programa_dias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`chave`);

--
-- Índices para tabela `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `submissoes`
--
ALTER TABLE `submissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_criado` (`criado_em`),
  ADD KEY `avaliado_por` (`avaliado_por`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `cronograma`
--
ALTER TABLE `cronograma`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `eixos`
--
ALTER TABLE `eixos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `eixo_cursos`
--
ALTER TABLE `eixo_cursos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `galeria_itens`
--
ALTER TABLE `galeria_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `inova_blocos`
--
ALTER TABLE `inova_blocos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `inova_criterios`
--
ALTER TABLE `inova_criterios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `inova_listas`
--
ALTER TABLE `inova_listas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `inova_projetos`
--
ALTER TABLE `inova_projetos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `modalidade_itens`
--
ALTER TABLE `modalidade_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `prazos`
--
ALTER TABLE `prazos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `prazo_itens`
--
ALTER TABLE `prazo_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `premios`
--
ALTER TABLE `premios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `premio_fotos`
--
ALTER TABLE `premio_fotos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `premio_itens`
--
ALTER TABLE `premio_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `programa_dias`
--
ALTER TABLE `programa_dias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `submissoes`
--
ALTER TABLE `submissoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `eixo_cursos`
--
ALTER TABLE `eixo_cursos`
  ADD CONSTRAINT `eixo_cursos_ibfk_1` FOREIGN KEY (`eixo_id`) REFERENCES `eixos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `modalidade_itens`
--
ALTER TABLE `modalidade_itens`
  ADD CONSTRAINT `modalidade_itens_ibfk_1` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `prazo_itens`
--
ALTER TABLE `prazo_itens`
  ADD CONSTRAINT `prazo_itens_ibfk_1` FOREIGN KEY (`prazo_id`) REFERENCES `prazos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `premio_fotos`
--
ALTER TABLE `premio_fotos`
  ADD CONSTRAINT `fk_foto_premio` FOREIGN KEY (`premio_id`) REFERENCES `premios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `premio_itens`
--
ALTER TABLE `premio_itens`
  ADD CONSTRAINT `premio_itens_ibfk_1` FOREIGN KEY (`premio_id`) REFERENCES `premios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `submissoes`
--
ALTER TABLE `submissoes`
  ADD CONSTRAINT `submissoes_ibfk_1` FOREIGN KEY (`avaliado_por`) REFERENCES `admins` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
