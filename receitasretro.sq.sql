-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 179.188.16.33
-- Generation Time: 26-Jun-2026 às 11:11
-- Versão do servidor: 5.7.32-35-log
-- PHP Version: 5.6.40-0+deb8u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `receitas_retro`
--
CREATE DATABASE IF NOT EXISTS `receitas_retro` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `receitas_retro`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `receita_id` int(11) DEFAULT NULL,
  `estrelas` int(11) DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(6, 'Bolos'),
(7, 'Doces'),
(4, 'Massas'),
(5, 'Pães'),
(3, 'Salgados'),
(2, 'Veganas'),
(1, 'Vegetarianas');

-- --------------------------------------------------------

--
-- Estrutura da tabela `compartilhamentos`
--

CREATE TABLE `compartilhamentos` (
  `id` int(11) NOT NULL,
  `receita_id` int(11) DEFAULT NULL,
  `plataforma` enum('WhatsApp','Facebook','Instagram') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `compartilhado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `imagens`
--

CREATE TABLE `imagens` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caminho` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receita_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `imagens`
--

INSERT INTO `imagens` (`id`, `nome`, `tipo`, `caminho`, `receita_id`) VALUES
(1, 'bolo-de-cenoura.jpeg', 'image/jpeg', '../uploads/img_686f3948191225.67137738.jpeg', NULL),
(2, 'coxinha-vegana.jpeg', 'image/jpeg', '../uploads/img_686f3b24e4ba64.60885954.jpeg', NULL),
(3, 'PÃ£o-de-forma.jpg', 'image/jpeg', '../uploads/img_686fe863e4e5e9.01341031.jpg', NULL),
(4, 'doce-de-goiaba.jpg', 'image/jpeg', '../uploads/img_686fe977513056.26782348.jpg', NULL),
(5, 'torta-de-broculis.jpg', 'image/jpeg', '../uploads/img_686fec109a0379.14625686.jpg', NULL),
(6, 'pastel-de-feira.jpg', 'image/jpeg', '../uploads/img_686fef3565eb72.32941041.jpg', NULL),
(7, 'pÃ£o-de-alho.jpg', 'image/jpeg', '../uploads/img_686ff1f38676e7.14075765.jpg', NULL),
(8, 'bolo-de-maracuja.jpeg', 'image/jpeg', '../uploads/img_68758d092f46c3.92149737.jpeg', NULL),
(9, 'doce_de_jaca.jpg', 'image/jpeg', '../uploads/img_6875a90d03a7c9.41534641.jpg', NULL),
(10, 'bolo-de-maracujÃ¡.jpg', 'image/jpeg', '../uploads/img_6877f84cbe5929.66031016.jpg', NULL),
(11, 'torta-de-palmito.jpg', 'image/jpeg', '../uploads/img_68786265948638.95172668.jpg', NULL),
(12, 'Canjica-Nordestina.jpg', 'image/jpeg', '../uploads/img_68786308d5f869.64000996.jpg', NULL),
(13, 'torta-de-palmito.jpg', 'image/jpeg', '../uploads/img_687864fb1873b6.05857573.jpg', NULL),
(14, 'torta-de-palmito.jpg', 'image/jpeg', '../uploads/img_6878662b0f3c51.73997134.jpg', NULL),
(15, 'torta-de-palmito.jpg', 'image/jpeg', '../uploads/img_68786708ab3f93.28563191.jpg', NULL),
(16, 'torta-de-palmito.jpg', 'image/jpeg', '../uploads/img_687867efbb0e39.39490325.jpg', NULL),
(17, 'pao-caseiro.jpg', 'image/jpeg', '../uploads/img_6878696c48c048.40338754.jpg', NULL),
(18, 'bolo-caseiro.jpg', 'image/jpeg', '../uploads/img_68786b80d906d4.81717457.jpg', NULL),
(19, 'tapi.jpeg', 'image/jpeg', '../uploads/img_68792889e3aab6.64864984.jpeg', NULL),
(20, 'download.jpg', 'image/jpeg', '../uploads/img_6879288f60ffc9.16860633.jpg', NULL),
(21, 'danoninho vegano.JPG', 'image/jpeg', '../uploads/img_687929759c95b6.86346852.JPG', NULL),
(22, 'bolinho de arroz.jpg', 'image/jpeg', '../uploads/img_68a3788fef6fc5.83959111.jpg', NULL),
(23, 'biscoitos.jpg', 'image/jpeg', '../uploads/img_68a6624d3f63b7.28414298.jpg', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `receitas`
--

CREATE TABLE `receitas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ingredientes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `modo_preparo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `aprovado` tinyint(1) DEFAULT '0',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pendente','liberado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `receitas`
--

INSERT INTO `receitas` (`id`, `titulo`, `descricao`, `ingredientes`, `modo_preparo`, `imagem`, `categoria_id`, `usuario_id`, `aprovado`, `criado_em`, `status`) VALUES
(44, 'Bolo de cenoura', '', '2 cenouras mÃ©dias\novo\n3 ovos\nÃ³leo\n1/2 xÃ­cara de Ã³leo\nfermento em pÃ³ quÃ­mico\n1 colher (chÃ¡) de fermento em pÃ³\nfarinha de trigo\n2 xÃ­caras de trigo\naÃ§Ãºcar\n1 xÃ­cara de aÃ§Ãºcar\nCobertura de chocolate\nmargarina\n1 colher de margarina\nleite condensado\n1/2 lata de leite condensado\nchocolate em pÃ³\n3 colheres de chocolate em pÃ³\nleite\n1 xÃ­cara de leite.', 'Modo de preparo : 50min\n1\nMassa\nColoque no liquidificador a cenoura descascada e picada, os ovos inteiros e o azeite, bata atÃ© formar um creme.\n2\nEm uma vasilha separada (pode ser a bacia da batedeira) coloque o trigo e o aÃ§Ãºcar.\n3\nJunte o creme do liquidificador e bata por alguns minutos.\n4\nPor Ãºltimo, coloque o fermento em pÃ³ e mexa bem.\n5\nColoque para assar em forno mÃ©dio, preaquecido, por aproximadamente 40 minutos, ou atÃ© dourar.', '../uploads/img_686f3948191225.67137738.jpeg', 6, 10, 0, '2025-07-10 03:53:44', 'liberado'),
(45, 'Coxinha Vegana', '', '1 embalagem de FilÃ© de Frango IncrÃ­vel! 100% Vegetal\n2 colheres de sopa de azeite\n1 Â½ xÃ­cara de molho de tomate\nÂ½  lata de milho drenado\nÂ¼ xÃ­cara de azeitona preta Azapa picadinha\n1 colher de sopa de amido de milho\nÂ½ xÃ­cara de chÃ¡ de leite vegetal\n2 colheres de sopa de folhas de salsinha picadas\nPara a massa da coxinha\n1 e 1/2 xÃ­cara de Ã¡gua\n1 colher (chÃ¡) de aÃ§afrÃ£o\n8 colheres de sopa de Ã³leo de soja\n4 xÃ­caras de chÃ¡ de farinha de trigo\nSal a gosto\nPimenta-do-reino a gosto\nPara a finalizaÃ§Ã£o\nÂ½ xÃ­cara de Ã¡gua\n1 xÃ­cara de farinha de rosca\n2 xÃ­caras de Ã³leo.', 'Em uma frigideira em fogo mÃ©dio, adicione o azeite e junte o FilÃ© de Frango IncrÃ­vel! 100% Vegetal ainda congelado e grelhe por 8 minutos, virando para que doure por igual. Retire o IncrÃ­vel filÃ© do fogo e pique. Reserve;\nEm uma panela em fogo mÃ©dio, junte o incrÃ­vel filÃ© picado, a passata, o milho, a azeitona e o leite com amido dissolvido. Tempere com sal e pimenta-do-reino e cozinhe atÃ© engrossar. Retire o recheio do fogo e espere esfriar por completo.\nPara a massa da coxinha\nEm uma panela grande, coloque a Ã¡gua, o sal e a pimenta-do-reino;\nAdicione o aÃ§afrÃ£o e misture bem atÃ© que a Ã¡gua fique amarelada;\nAcrescente o Ã³leo e leve ao fogo alto atÃ© ferver;\nAbaixe o fogo e adicione a farinha de trigo, mexendo atÃ© formar uma massa homogÃªnea, que se solte do fundo da panela;\nDesligue o fogo e transfira a massa ainda morna para uma superfÃ­cie lisa e levemente enfarinhada;\nSove atÃ© a massa ficar lisinha;\nCubra com plÃ¡stico filme e deixe descansar por 10 minutos;\nDepois, abra a massa e divida em bolinhas.\nPara a finalizaÃ§Ã£o\nEm uma panela, preaqueÃ§a o Ã³leo em fogo mÃ©dio;\nFaÃ§a um furinho na bolinha de massa de coxinha vegana, adicione parte do recheio e modele no formato de coxinha;\nUmedeÃ§a as coxinhas veganas na Ã¡gua, passe na farinha de rosca e frite no Ã³leo preaquecido por cerca de 7 minutos ou atÃ© estarem douradas. Sirva na sequÃªncia.', '../uploads/img_686f3b24e4ba64.60885954.jpeg', 2, 10, 0, '2025-07-10 04:01:41', 'liberado'),
(46, 'PÃ£o de forma caseiro', '', '5 xÃ­caras (chÃ¡) de farinha de trigo\n2 colheres (chÃ¡) de fermento biolÃ³gico seco (cerca de 6 g)\n2 colheres (chÃ¡) de aÃ§Ãºcar\n1 xÃ­cara (chÃ¡) de Ã¡gua morna\nÂ½ xÃ­cara (chÃ¡) de leite em temperatura ambiente\n2 colheres (sopa) de manteiga em ponto pomada\n2 colheres (chÃ¡) de sal.', 'Retire o leite e a manteiga da geladeira antes de comeÃ§ar a preparar a receita - eles precisam estar em temperatura ambiente. Se a manteiga nÃ£o estiver macia (no ponto de pomada) leve para rodar no micro-ondas por alguns segundos.\nNuma tigela pequena misture o fermento e o aÃ§Ãºcar com Â¼ de xÃ­cara (chÃ¡) da Ã¡gua morna atÃ© dissolver. Deixe descansar por cerca de 5 minutos, atÃ© comeÃ§ar a espumar.\nNuma tigela grande, misture a farinha com o sal e abra um buraco no centro. Junte o fermento dissolvido e misture aos poucos com a farinha, do centro para a borda. Acrescente o restante de Ã¡gua morna aos poucos, misturando com a mÃ£o para incorporar. Regue com o leite, tambÃ©m aos poucos, sem parar de misturar e amassar com a mÃ£o.\nAssim que a farinha tiver absorvido os lÃ­quidos, junte a manteiga e amasse bem para incorporar - nÃ£o se assuste, parece que nÃ£o vai dar certo, a manteiga demora para ser absorvida pela massa. Aperte, amasse, estique e amasse novamente atÃ© ficar com a textura macia e Ãºmida - marque 10 minutos no relÃ³gio! Se preferir, sove a massa na bancada ou na batedeira com o gancho.\nModele uma bola, volte a massa para a tigela e cubra com filme (ou pano de prato). Deixe descansar por 1 hora atÃ© dobrar de tamanho.\nUnte com manteiga 2 fÃ´rmas para bolo inglÃªs de 22 cm x 10 cm (se a fÃ´rma for antiaderente nÃ£o hÃ¡ necessidade de untar).\nAssim que tiver crescido, transfira a massa para a bancada e divida ao meio. Achate delicadamente cada uma das metades atÃ© formar um retÃ¢ngulo. Para modelar os pÃ£es: dobre a base do retÃ¢ngulo (lado maior da massa) atÃ© o centro e aperte delicadamente; cubra com a base oposta e aperte bem para selar; dobre cerca de 4 cm das laterais (use a fÃ´rma de bolo inglÃªs como referÃªncia para o tamanho) e aperte bem as emendas para selar.\nTransfira as massas, com a emenda voltada para baixo, para as fÃ´rmas untadas. Cubra com um pano de prato limpo (ou filme) e deixe crescer por mais 40 minutos. Quando faltar 20 minutos para o tempo do pÃ£o preaqueÃ§a o forno a 180 ÂºC (temperatura mÃ©dia).\nLeve ao forno para assar por cerca de 40 minutos atÃ© os pÃ£es crescerem e dourarem. Retire do forno e, com cuidado, desenforme sobre uma grelha - se o pÃ£o ficar na fÃ´rma ou sobre uma superfÃ­cie lisa pode acumular vapor e amolecer a casca.', '../uploads/img_686fe863e4e5e9.01341031.jpg', 5, 12, 0, '2025-07-10 16:20:52', 'liberado'),
(47, 'Doce de Goiaba', '', '1 kg de goiabas vermelhas maduras.\nmeia xÃ­cara (chÃ¡) de Ã¡gua.\n2 xÃ­caras (chÃ¡) de aÃ§Ãºcar.\n2 colheres (sopa) de suco de limÃ£o.', 'Lave bem as goiabas, descasque e corte ao meio. Retire as sementes com uma colher.\n2.\nEm um liquidificador, bata a polpa com a Ã¡gua atÃ© obter um purÃª homogÃªneo.\n3.\nPasse o purÃª por uma peneira para eliminar qualquer resÃ­duo de sementes. Em seguida, em uma panela grande, adicione o purÃª peneirado, o aÃ§Ãºcar e o suco de limÃ£o.\n4.\nLeve ao fogo baixo, mexendo sempre com uma colher de pau, por cerca de 30 a 40 minutos ou atÃ© o doce engrossar e soltar do fundo da panela.\n5.\nQuando atingir o ponto desejado (mais cremoso ou mais firme), desligue o fogo.\n6.\nTransfira o doce para potes de vidro esterilizados, espere esfriar e conserve na geladeira.', '../uploads/img_686fe977513056.26782348.jpg', 7, 12, 0, '2025-07-10 16:25:27', 'liberado'),
(48, 'EmpadÃ£o de brÃ³colis', '', '2 xÃ­caras (chÃ¡) de farinha de trigo\n1 xÃ­cara (chÃ¡) de Ã³leo\n3 ovos\n1 colher (sopa) de sal\n1 xÃ­cara (chÃ¡) de leite\n2 colheres (sopa) de queijo parmesÃ£o ralado\n1 colher (sopa) de fermento em pÃ³\nRecheio\n2 colheres (sopa) de azeite\n2 dentes de alho espremidos\n1 cebola picada\n3 xÃ­caras (chÃ¡) de buquÃªs de brÃ³colis prÃ© cozidos\nSal e pimenta-do-reino a gosto\n200g de queijo mussarela ralado.', 'Para o recheio, em uma panela, coloque o azeite, o alho espremido, a cebola picada e frite por 5 minutos.\nJunte o brÃ³colis prÃ©-cozido, sal, pimenta-do-reino a gosto e refogue por 5 minutos, mexendo de vez em quando.\nRetire do fogo e deixe esfriar.\nNo liquidificador, bata todos os ingredientes da massa.\nEm uma forma de 20 x 30cm, coloque metade da massa, o brÃ³colis refogado e temperado e, por cima, coloque a mussarela, espalhando bem.\nCubra com o restante da massa e leve ao forno, preaquecido, por 30 minutos ou atÃ© dourar levemente.', '../uploads/img_686fec109a0379.14625686.jpg', 4, 13, 0, '2025-07-10 16:36:32', 'liberado'),
(49, 'Pastel de feira', '', '3 xÃ­caras de farinha de trigo\nÃ¡gua\n1 xÃ­cara de Ã¡gua morna (ou um pouco mais)\nÃ³leo\n3 colheres (sopa) de Ã³leo (de soja, milho, girassol ou algodÃ£o)\ncachaÃ§a\n1 colher (sopa) de aguardente\nsal\n1 colher (sopa) rasa de sal\nfarinha de trigo\nfarinha de trigo para trabalhar a massa.', 'Modo de preparo : 30min\nPreparo : 1min\nEspera : 5min\n1\nColoque a farinha misturada com o sal em uma vasilha ou uma mesa e abra um buraco no meio.\n2\nNesse buraco, coloque o Ã³leo, a aguardente e um pouco de Ã¡gua.\n3\nMisture a Ã¡gua e a farinha aos poucos, cada vez pegando um pouco mais de farinha da borda do buraco.\n4\nQuando a massa estiver ficando dura, coloque mais Ã¡gua.\n5\nA massa deve ficar macia.\n6\nSe estiver um pouco grudenta, nÃ£o tem problema.\n7\nSe estiver muito grudenta, coloque mais farinha.\n8\nSe estiver dura, coloque mais Ã¡gua.\n9\nEm uma superfÃ­cie enfarinhada, abra a massa com o auxÃ­lio de um rolo, de forma que ela fique bem fina.\n10\nSe nÃ£o ficar fina, ela nÃ£o fica crocante depois de fritar.\n11\nRecheie a gosto, e feche com um garfo ou com o verso de uma faca.\n12\nFrite em Ã³leo quente (nÃ£o muito) em fogo mÃ©dio-alto e escorra com o auxÃ­lio de uma escumadeira, antes de deixar para secar em papel absorvente.', '../uploads/img_686fef3565eb72.32941041.jpg', 3, 13, 0, '2025-07-10 16:49:57', 'liberado'),
(50, 'PÃ£o de alho com crosta de queijo', '', 'PÃ£o italiano;\n3 colheres de sopa de manteiga;\n4 dentes de alho;\nQueijo mussarela;\nOrÃ©gano e sal a gosto.', 'Corte o pÃ£o ao meio. Lembre-se de deixar bastante miolo.\nEm um recipiente, coloque a manteiga com o alho triturado, o sal e o orÃ©gano.\nMisture tudo atÃ© que fique com a consistÃªncia de uma pasta.\nPasse a pasta nas duas partes do pÃ£o e, em seguida, coloque bastante queijo mussarela.\nCubra com papel alumÃ­nio e leve para assar por 10 minutos em forno prÃ©-aquecido a 180Â°C.\nEspere derreter ou gratinar e sirva em seguida.', '../uploads/img_686ff1f38676e7.14075765.jpg', 1, 10, 0, '2025-07-10 17:01:40', 'liberado'),
(52, 'Doce de jaca', '', 'Ingredientes (15 porÃ§Ãµes)\nÂ½ jaca pequena\naÃ§Ãºcar\n3 xÃ­caras de chÃ¡ de aÃ§Ãºcar\nsuco de limÃ£o\n2 colheres de sopa de suco de limÃ£o.', 'Modo de preparo : 35min\n1\nAbrir a jaca ao meio, separar os gomos e retirar as sementes. Reservar.\n2\nColocar o aÃ§Ãºcar em uma panela, levar ao fogo, sem parar de mexer, por 15 minutos, ou atÃ© dourar o aÃ§Ãºcar.\n3\nAbaixar o fogo, adicionar cuidadosamente 4 xÃ­caras de chÃ¡ de Ã¡gua e misturar atÃ© o aÃ§Ãºcar dissolver.\n4\nAdicionar a jaca, mexer de vez em quando, por 8 minutos, ou atÃ© ficar macia e com uma calda dourada e encorpada.\n5\nDesligar o fogo e acrescentar o suco de limÃ£o.\n6\nEsperar esfriar e colocar em uma compoteira. Servir com queijo.', '../uploads/img_6875a90d03a7c9.41534641.jpg', 7, 11, 0, '2025-07-15 01:04:13', 'liberado'),
(53, 'Bolo MaracujÃ¡', '', '3 ovos\nsuco de maracujÃ¡\n1 xÃ­cara chÃ¡ de suco de maracujÃ¡ natural\nÃ³leo\n1 xÃ­cara chÃ¡ de Ã³leo\naÃ§Ãºcar\n2 xÃ­caras de aÃ§Ãºcar\nfarinha de trigo\n2 xÃ­caras de farinha de trigo\nfermento em pÃ³ quÃ­mico\n1 colher de sopa de fermento em pÃ³\nmaracujÃ¡\nPolpa de 2 maracujÃ¡s com semente\naÃ§Ãºcar\n1 xÃ­cara de aÃ§Ãºcar para fazer uma calda grossa.', 'Modo de preparo : 40min\n1\nNo liquidificador coloque os ovos, o suco de maracujÃ¡, o Ã³leo.\n2\nBater mais ou menos uns 3 minutos.\n3\nEm uma vasilha coloque o aÃ§Ãºcar, a farinha de trigo e o pÃ³ royal, misture bem.\n4\nAcrescente o lÃ­quido do liquidificador, mexa bem.\n5\nLeve ao forno prÃ©-aquecido, por cerca de 30 minutos, ou atÃ© dourar por cima.\n6\nPrepare a calda e coloque por cima do bolo ainda quente.\n7\nCalda:\nLeve ao fogo a polpa de 2 maracujÃ¡s e cerca de 1 xÃ­cara de aÃ§Ãºcar, deixe ferver, mexa de vez em quando.', '../uploads/img_6877f84cbe5929.66031016.jpg', 6, 13, 0, '2025-07-16 19:06:52', 'liberado'),
(55, 'Canjica Nordestina', '', '10 espigas de milho.\n1 litro de leite de coco.\n2 xÃ­caras rasas de aÃ§Ãºcar.\n1 colher de sobremesa de sal.\n2 colheres de sopa de manteiga.', '1. Lave bem as espigas e retire os milhos com uma faca bem afiada.\n2. Bata os grÃ£os no liquidificador com metade do leite de coco.\n3. Coe a mistura em uma peneira fina, extraindo o caldo.\n4. Leve ao fogo com o sal, mexendo sempre.\n5. Acrescente o restante do leite de coco atÃ© engrossar.\n6. Quando a massa comeÃ§ar a cair lentamente da colher, adicione o aÃ§Ãºcar.\n7. Mexa vigorosamente atÃ© incorporar bem.\n8. Adicione a manteiga e cozinhe por cerca de 30 minutos.', '../uploads/img_68786308d5f869.64000996.jpg', 2, 11, 0, '2025-07-17 02:42:16', 'liberado'),
(59, 'Torta de Palmito', '', '700 g de farinha de trigo\n2 ovos\nSal a gosto\n1 colher (cafÃ©) de aÃ§Ãºcar\n1 colher (chÃ¡) de fermento biolÃ³gico\n3 colheres (sopa) de manteiga\n1 xÃ­cara de leite\n1 gema para pincelar.\nRecheio\n1 vidro de palmito em pedaÃ§os\n1 colher da Ã¡gua do palmito\n1 colher de Ã³leo\n3 dentes de alho picados\n1 cebola picada\n1/2 lata de ervilha\n1/2 lata de milho\nSalsinha picada a gosto\nSal a gosto\n1 xÃ­cara de leite\n2 colheres rasas de farinha de trigo\n2 tomates picados.', 'Massa:\nColocar todos os ingredientes em um refratÃ¡rio e mexer bem com as mÃ£os, atÃ© desgrudar das mÃ£os.\nForme uma bola e divida em duas partes.\nAbra bem as duas partes com um rolo.\nRecheio:\nColocar Ã³leo, alho e cebola em uma panela e deixe atÃ© comeÃ§ar a dourar.\nAcrescente tomate, palmito, Ã¡gua do palmito, ervilha e milho.\nMexa bem, espere o tomate murchar.\nEm um copo, coloque leite e farinha e mexa atÃ© desmanchar a farinha.\nAcrescente essa mistura na panela com os outros ingredientes.\nSe engrossar muito, acrescente um pouco de leite.\nMexa um pouco e desligue. Reserve.\nMontagem:\nAbra as 2 partes da massa.\nUnte uma assadeira com manteiga e farinha.\nPreencha o fundo da assadeira com uma parte da massa, apertando nas laterais para grudar na forma.\nEspalhe o recheio sobre esta parte.\nCubra o recheio com a outra parte da massa, pressionando os lados para grudar.\nSe sobrar massa, use para enfeitar.\nBata levemente a gema e pincele a massa.\nLeve ao forno em fogo mÃ©dio por cerca de 50 minutos (ou atÃ© dourar).\nRetire do forno, espere esfriar e desenforme.', '../uploads/img_687867efbb0e39.39490325.jpg', 4, 11, 0, '2025-07-17 03:03:12', 'liberado'),
(60, 'PÃ£o Caseiro', '', '1 kg de farinha de trigo\n2 copos de leite morno\n2 colheres (sopa) de aÃ§Ãºcar\n1 colher (sopa) de sal\n2 ovos\n1/2 copo de Ã³leo\n2 tabletes de fermento biolÃ³gico fresco (30g).', 'Dissolva o fermento no leite morno com o aÃ§Ãºcar e deixe descansar por 10 minutos.\nEm uma tigela, misture a farinha, sal, ovos, Ã³leo e o leite com fermento.\nSove a massa por 10 minutos atÃ© ficar homogÃªnea.\nCubra e deixe descansar por 1 hora.\nModele os pÃ£es e coloque em formas untadas.\nDeixe crescer por mais 30 minutos.\nAsse em forno prÃ©-aquecido a 180Â°C por cerca de 35 a 40 minutos.', '../uploads/img_6878696c48c048.40338754.jpg', 5, 11, 0, '2025-07-17 03:09:32', 'liberado'),
(61, 'Bolo de FubÃ¡', '', '3 ovos\n2 xÃ­caras de aÃ§Ãºcar\n2 xÃ­caras de fubÃ¡\n1/2 xÃ­cara de Ã³leo\n2 xÃ­caras de leite\n1 colher de sopa de fermento em pÃ³.', 'Bata os ovos com o aÃ§Ãºcar, fubÃ¡, Ã³leo e leite no liquidificador.\nMisture o fermento delicadamente com uma colher.\nDespeje em forma untada e leve ao forno a 180Â°C por 35 a 40 minutos.', '../uploads/img_68786b80d906d4.81717457.jpg', 6, 18, 0, '2025-07-17 03:18:24', 'liberado'),
(62, 'Tapioca', '', '2 colheres (sopa) de goma de tapioca hidratada (pode ser a tradicional ou a granulada)\nRecheio a gosto (queijo, coco ralado, manteiga, frango desfiado, goiabada, etc.)\nÃ“leo ou manteiga (opcional, para untar)', 'Se estiver usando goma hidratada pronta (jÃ¡ peneirada), basta mexer para homogeneizar.\nCaso esteja usando a goma seca (granulada), hidrate-a com um pouco de Ã¡gua (cerca de 1/2 xÃ­cara de Ã¡gua para 1 xÃ­cara de goma) e deixe descansar por algumas horas ou de um dia para o outro, atÃ© ficar Ãºmida e uniforme.', '../uploads/img_68792889e3aab6.64864984.jpeg', 3, 20, 0, '2025-07-17 16:44:57', 'liberado'),
(63, 'MacarrÃ£o Ã  carbonara', '', 'acon picado a gosto\nqueijo ralado\nqueijo ralado a gosto\novo\n3 ovos\nsal\nsal\npimenta-do-reino\npimenta-do-reino a gosto\nmacarrÃ£o\nmacarrÃ£o de sua escolha (espaguete, fusili,etc.)\ncreme de leite\ncreme de leite se quiser dar um toque diferente Ã  receita', 'Frite bem o bacon, atÃ© ficar crocante (pode-se adicionar salame picado).\n2\nColoque o macarrÃ£o para cozinhar em Ã¡gua e sal.\n3\nNo refratÃ¡rio onde serÃ¡ servido o macarrÃ£o, bata bem os ovos com um garfo.\n4\nTempere com sal e pimenta a gosto, e junte o queijo ralado, tambÃ©m a gosto.\n5\nQuando o macarrÃ£o estiver pronto, escorra e coloque (bem quente) sobre a mistura de ovos, misture bem.\n6\nO calor da massa cozinha os ovos.\n7\nColoque o bacon, ainda quente, sobre o macarrÃ£o e sirva.', '../uploads/img_6879288f60ffc9.16860633.jpg', 4, 21, 0, '2025-07-17 16:45:03', 'liberado'),
(64, 'Danoninho Vegano', '', 'Inhame cozido\nmorangos\nlimÃ£o', 'bata todos os ingredientes no liquidificador atÃ© ficar homogÃªneo, caso queira, adoce.', '../uploads/img_687929759c95b6.86346852.JPG', 2, 14, 0, '2025-07-17 16:48:53', 'liberado'),
(65, 'Bolinho de arroz', '', '2 xÃ­caras (chÃ¡) de arroz cozido\nqueijo ralado\n1/2 xÃ­cara (chÃ¡) de queijo ralado\nleite\n1/2 xÃ­cara (chÃ¡) de leite\ncheiro-verde\n2 colheres (sopa) de cheiro-verde picado\nfermento em pÃ³ quÃ­mico\n1 colher (sopa) de fermento em pÃ³\namido de milho\n1/2 xÃ­cara (chÃ¡) de amido de milho\nfarinha de trigo\n1/2 xÃ­cara (chÃ¡) de farinha de trigo\novo\n3 ovos\nÃ³leo\nÃ³leo para fritar.', 'Modo de preparo : 30min\n1\nEm um recipiente, misture todos os ingredientes atÃ© criar uma massa firme e encorpada.\n2\nMolde os bolinhos e frite-os no Ã³leo quente, atÃ© que fiquem dourados.\n3\nEscorra sobre papel absorvente.', '../uploads/img_68a3788fef6fc5.83959111.jpg', 1, 24, 0, '2025-08-18 19:01:36', 'liberado'),
(66, 'Biscoitos Caseiros', '', '1 xÃ­cara (chÃ¡) de farinha de trigo\nÂ¼ de xÃ­cara (chÃ¡) de aÃ§Ãºcar\n75 g de manteiga em temperatura ambiente\nÂ½ colher (chÃ¡) de extrato de baunilha (opcional).', 'Numa tigela, misture a farinha com o aÃ§Ãºcar. Adicione a manteiga, o extrato de baunilha e amasse bem com as mÃ£os atÃ© formar uma massa lisa.\nDivida a massa em duas porÃ§Ãµes e modele cada metade num rolinho com cerca de 2,5 cm de diÃ¢metro. Embale com filme (ou papel-manteiga) e leve Ã  geladeira para firmar por 30 minutos (se preferir, prepare no dia anterior).\nPreaqueÃ§a o forno a 180 ÂºC (temperatura mÃ©dia).\nCorte cada rolinho em fatias de cerca de 1 cm de espessura. Transfira para uma assadeira grande antiaderente, deixando cerca de 1 cm entre cada um (caso nÃ£o seja antiaderente, unte a assadeira com manteiga e polvilhe com farinha de trigo). Com um garfo, aperte levemente cada biscoito para marcar.\nLeve ao forno para assar por cerca de 15 minutos, ou atÃ© que os biscoitos fiquem levemente dourados na borda â€” eles terminam de firmar depois de esfriar. Retire do forno e deixe esfriar antes de servir ou armazenar.', '../uploads/img_68a6624d3f63b7.28414298.jpg', 6, 25, 0, '2025-08-21 00:03:25', 'liberado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tokens_recuperacao`
--

CREATE TABLE `tokens_recuperacao` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira_em` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('usuario','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'usuario',
  `ativo` tinyint(1) DEFAULT '0',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `senha`, `tipo`, `ativo`, `criado_em`, `status`) VALUES
(9, 'Administrador', 'admin@retro.com', '(48)99685-9855', '$2y$10$halE40oGVmk4DEZehBMWTOSbF4/cD/B8STpR.gA8CatHD50y.5MHO', 'admin', 1, '2025-07-09 22:17:53', 'liberado'),
(10, 'MeprÃ´nio Pereira Ramos', 'mepronio@gmail.com', NULL, '$2y$10$LgvdC.6FddBUmFc.j3uB4OaJpE/dHlUspT6Qq0oXUf7CFHGV9fvYS', 'usuario', 0, '2025-07-10 03:45:57', 'liberado'),
(11, 'Marcos Silva Barreto', 'marcos@gmail.com', NULL, '$2y$10$H4wwJ76wMZBztKCEjMxXhOzrkUbtjQ9bl0AHRQapAGglOS/jrsxfq', 'usuario', 0, '2025-07-10 03:48:12', 'liberado'),
(12, 'Mikovisk Linieve', 'mikovisk@gmail.com', NULL, '$2y$10$ZD4kVGMyJBQqDRoI/55TDuYtKXM0749mYl2c5QJ7zC01l.UpVBL1O', 'usuario', 0, '2025-07-10 16:12:17', 'liberado'),
(13, 'Vanisclaudio Motunga Liberato', 'vanisclaudio@gmail.com', NULL, '$2y$10$LeOQxFM20E2PEMqLtgH2UOTpzxpvHZpbD9ivdJW1PflkfssM3tOqS', 'usuario', 0, '2025-07-10 16:15:55', 'liberado'),
(14, 'Gencen Abelino', 'gencen@gmail.com', NULL, '$2y$10$CEFnYr5OvjOQkF3UYrj2geKd9slKejnAjTTvZfSES6H7j5qd0X.t.', 'usuario', 0, '2025-07-11 01:21:22', 'liberado'),
(15, 'Goreti Maria Wisintainer', 'goreti.maria.wisintainer@gmail.com', NULL, '$2y$10$.v34xAxZnPMYARV43qbG3ekgEOTx6shEMZNBvViPwzRqeDCYrumTy', 'usuario', 0, '2025-07-14 11:02:11', 'liberado'),
(17, 'Vasily Vinovik', 'vasily@gmail.com', NULL, '$2y$10$ZGr765MZFbF5kXmhtaJ.NOo5EWMlZksBMzHoHbjRSlF1KGRjjkUf2', 'usuario', 0, '2025-07-15 00:08:29', 'liberado'),
(18, 'Nikolai Vladislav', 'nikolai@gmail.com', NULL, '$2y$10$gM8NDgu1N3eLDJkBC3wNxeN3b7AlW6yyoPL3bDRR.jgTDvT.3FMSO', 'usuario', 0, '2025-07-15 00:13:05', 'liberado'),
(19, 'Svetlana Irina', 'svetlana@hotmail.com', NULL, '$2y$10$uS1jPwK7zXHcKRYQFGNiWuJrHLMSIqyQpszTrjyJ7awp0dX2/iiva', 'usuario', 0, '2025-07-15 00:24:46', 'liberado'),
(20, 'Germano Coelho', 'germano.dc@aluno.ifsc.edu.br', NULL, '$2y$10$yMHNLXRzAYrlOKVlKNWlsOwm79X4vnmrQBc/SC9hAxSHk1Iwuy7Qm', 'usuario', 0, '2025-07-17 16:42:27', 'liberado'),
(21, 'catia Machado', 'catia.reis@ifsc.edu.br', NULL, '$2y$10$FY8WhY4EusTW7iSrQmzoReni26kzH1.M5tPeCzX2LWFSXYm8cM0ZC', 'usuario', 0, '2025-07-17 16:42:42', 'liberado'),
(22, 'Anderson Orleans', 'andersondorleans@gmail.com', NULL, '$2y$10$ii4TLAWSvPfIdM15DQ9AEOsR4TJl0lDLujDEzHb9Zgc6KyiLqNybS', 'usuario', 0, '2025-07-17 16:42:45', 'liberado'),
(23, 'Sandro Algusto', 'sandro@gmail.com', NULL, '$2y$10$p3Ab8X4i6GcsZNldGVcem.Gxm82P3KOeX8pktqHfGpE9lSAzklqge', 'usuario', 0, '2025-08-11 11:12:07', 'liberado'),
(24, 'Kardoso', 'kardoso@gmail.com', NULL, '$2y$10$.ZqycY6g4j8ZGz0RUhOeye7GUXU14/Yqe6spTGcgKntBvtiVPbLSm', 'usuario', 0, '2025-08-18 18:51:19', 'liberado'),
(25, 'Lakivisk Niev', 'lakivisk@hotmail.com', NULL, '$2y$10$s8qAsLOH3kwkhPiXMzgQJOwwUiAi3M6dvBF1ikkfYRc1ObhmtdmPW', 'usuario', 0, '2025-08-20 23:57:57', 'liberado'),
(26, 'Alob', 'bolatechproducoes@gmail.com', NULL, '$2y$10$JALsTMwYBWtfpFLmfRFasu5r.qRwJ37JyEf2v5dmXe3BwjDG6bYSe', 'usuario', 0, '2026-03-28 13:55:57', 'liberado');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `receita_id` (`receita_id`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `compartilhamentos`
--
ALTER TABLE `compartilhamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receita_id` (`receita_id`);

--
-- Indexes for table `imagens`
--
ALTER TABLE `imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receita_id` (`receita_id`);

--
-- Indexes for table `receitas`
--
ALTER TABLE `receitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `tokens_recuperacao`
--
ALTER TABLE `tokens_recuperacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `compartilhamentos`
--
ALTER TABLE `compartilhamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imagens`
--
ALTER TABLE `imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `receitas`
--
ALTER TABLE `receitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `tokens_recuperacao`
--
ALTER TABLE `tokens_recuperacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_avaliacoes_receita` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avaliacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `compartilhamentos`
--
ALTER TABLE `compartilhamentos`
  ADD CONSTRAINT `fk_compartilhamentos_receita` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `imagens`
--
ALTER TABLE `imagens`
  ADD CONSTRAINT `imagens_ibfk_1` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `receitas`
--
ALTER TABLE `receitas`
  ADD CONSTRAINT `fk_receitas_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_receitas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `tokens_recuperacao`
--
ALTER TABLE `tokens_recuperacao`
  ADD CONSTRAINT `fk_tokens_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
