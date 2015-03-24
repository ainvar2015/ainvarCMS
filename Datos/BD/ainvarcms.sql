-- phpMyAdmin SQL Dump
-- version 4.1.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 20-03-2015 a las 21:27:36
-- Versión del servidor: 5.6.14
-- Versión de PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ainvarcms`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `body` text NOT NULL,
  `url` varchar(150) NOT NULL,
  `class` varchar(45) NOT NULL DEFAULT 'info',
  `start` varchar(15) NOT NULL,
  `end` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomas`
--

CREATE TABLE IF NOT EXISTS `idiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idioma` varchar(30) NOT NULL DEFAULT '',
  `reducido` varchar(3) NOT NULL DEFAULT '',
  `activo` tinyint(4) NOT NULL DEFAULT '0',
  `defecto` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `idiomas`
--

INSERT INTO `idiomas` (`id`, `idioma`, `reducido`, `activo`, `defecto`) VALUES
(1, 'castellano', 'es', 1, 1),
(2, 'English', 'en', 1, 0),
(3, 'Français', 'fr', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomasd`
--

CREATE TABLE IF NOT EXISTS `idiomasd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(3) NOT NULL,
  `codigo2` varchar(2) DEFAULT NULL,
  `descripcion` varchar(150) NOT NULL,
  `original` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

--
-- Volcado de datos para la tabla `idiomasd`
--

INSERT INTO `idiomasd` (`id`, `codigo`, `codigo2`, `descripcion`, `original`) VALUES
(13, 'alb', 'sq', 'Albanian', 'Shqip'),
(19, 'ara', 'ar', 'Arabic', 'العربية'),
(25, 'arm', 'hy', 'Armenian', 'Armenian'),
(37, 'aze', 'az', 'Azerbaijani', 'Azərbaycanca'),
(47, 'baq', 'eu', 'Basque', 'Euskera'),
(50, 'bel', 'be', 'Belarusian', 'Беларуская мова'),
(60, 'bos', 'bs', 'Bosnian', 'Bosanski'),
(62, 'bre', 'br', 'Breton', 'Brezhoneg'),
(64, 'bul', 'bg', 'Bulgarian', 'Български'),
(70, 'cat', 'ca', 'Catalan', 'català'),
(78, 'che', 'ce', 'Chechen', 'Chechen'),
(83, 'chi', 'zh', 'Chinese', '中文'),
(101, 'scr', 'hr', 'Croatian', 'Hrvatski'),
(103, 'cze', 'cs', 'Czech', 'Čeština'),
(105, 'dan', 'da', 'Danish', 'Dansk'),
(115, 'dut', 'nl', 'Dutch; Flemish', 'Nederlands'),
(123, 'eng', 'en', 'English', 'English'),
(127, 'epo', 'eo', 'Esperanto', 'Esperanto'),
(128, 'est', 'et', 'Estonian', 'Eesti keel'),
(136, 'fin', 'fi', 'Finnish', 'suomi'),
(139, 'fre', 'fr', 'French', 'français'),
(144, 'glg', 'gl', 'Galician', 'Galego'),
(150, 'ger', 'de', 'German', 'Deutsch'),
(158, 'gre', 'el', 'Greek', 'Ελληνικά'),
(159, 'kal', 'kl', 'Greenlandic; Kalaallisut', 'Greenlandic; Kalaallisut'),
(167, 'heb', 'he', 'Hebrew', 'עברית'),
(171, 'hin', 'hi', 'Hindi', 'हिन्दी'),
(175, 'hun', 'hu', 'Hungarian', 'Magyar'),
(178, 'ice', 'is', 'Icelandic', 'íslenska'),
(186, 'ind', 'id', 'Indonesian', 'Indonesian'),
(193, 'gle', 'ga', 'Irish', 'Gaeilge'),
(195, 'ita', 'it', 'Italian', 'Italiano'),
(196, 'jpn', 'ja', 'Japanese', '日本語'),
(214, 'kaz', 'kk', 'Kazakh', 'Kazakh'),
(224, 'kon', 'kg', 'Kongo', 'Kongo'),
(226, 'kor', 'ko', 'Korean', 'Korean'),
(230, 'kua', 'kj', 'Kuanyama; Kwanyama', 'Kuanyama; Kwanyama'),
(232, 'kur', 'ku', 'Kurdish', 'Kurdish'),
(241, 'lav', 'lv', 'Latvian', 'Latvian'),
(245, 'lit', 'lt', 'Lithuanian', 'Lithuanian'),
(249, 'lub', 'lu', 'Luba-Katanga', 'Luba-Katanga'),
(256, 'ltz', 'lb', 'Luxembourgish; Letzeburgesch', 'Luxembourgish; Letzeburgesch'),
(285, 'mol', 'mo', 'Moldavian', 'Moldavian'),
(288, 'mon', 'mn', 'Mongolian', 'Mongolian'),
(308, 'nor', 'no', 'Norwegian', 'Norsk'),
(329, 'per', 'fa', 'Persian', 'فارسی'),
(333, 'pol', 'pl', 'Polish', 'Polski'),
(334, 'por', 'pt', 'Portuguese', 'Português'),
(344, 'rum', 'ro', 'Romanian', 'Romanian'),
(347, 'rus', 'ru', 'Russian', 'Русский'),
(352, 'smo', 'sm', 'Samoan', 'Samoan'),
(363, 'scc', 'sr', 'Serbian', 'српски'),
(379, 'slo', 'sk', 'Slovak', 'slovenčina'),
(380, 'slv', 'sl', 'Slovenian', 'slovenščina'),
(382, 'som', 'so', 'Somali', 'Soomaali'),
(391, 'spa', 'es', 'Spanish; Castilian', 'Español'),
(398, 'swe', 'sv', 'Swedish', 'Svenska'),
(410, 'tha', 'th', 'Thai', 'ภาษาไทย'),
(411, 'tib', 'bo', 'Tibetan', 'བོད་ཡིག'),
(427, 'tur', 'tr', 'Turkish', 'Türkçe'),
(435, 'ukr', 'uk', 'Ukrainian', 'українська мова'),
(442, 'vie', 'vi', 'Vietnamese', 'Tiếng Việt'),
(443, 'vol', 'vo', 'Volapük', 'Volapük'),
(447, 'wln', 'wa', 'Walloon', 'Walloon'),
(456, 'yid', 'yi', 'Yiddish', 'ייִדיש');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lateral`
--

CREATE TABLE IF NOT EXISTS `lateral` (
  `id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmenu` int(11) NOT NULL DEFAULT '0',
  `subid` int(11) NOT NULL DEFAULT '0',
  `ref` varchar(50) NOT NULL,
  `menu` varchar(60) NOT NULL DEFAULT '',
  `menu_imagen` varchar(150) DEFAULT NULL,
  `ubicacion` set('0','1','2') NOT NULL,
  `param` varchar(200) DEFAULT NULL,
  `titulo` varchar(150) NOT NULL DEFAULT '',
  `titulo_imagen` varchar(150) DEFAULT NULL,
  `resumen` varchar(250) DEFAULT NULL,
  `contenido` text,
  `varios` text,
  `categorias` mediumtext,
  `usaplantilla` varchar(2) DEFAULT 'NO',
  `tipoplantilla` varchar(2) NOT NULL DEFAULT '0',
  `modulo` varchar(250) DEFAULT NULL,
  `plantilla` varchar(30) DEFAULT NULL,
  `mostrar_contenido` tinyint(4) DEFAULT '1',
  `mostrar_pie` tinyint(1) NOT NULL DEFAULT '0',
  `bloqueado` tinyint(4) DEFAULT '1',
  `administracion` tinyint(4) DEFAULT '1',
  `orden` int(11) DEFAULT '0',
  `activar` tinyint(4) DEFAULT NULL,
  `restringir` tinyint(4) DEFAULT '0',
  `ocultar` tinyint(4) DEFAULT '0',
  `idioma` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`idmenu`),
  FULLTEXT KEY `contenido` (`contenido`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_config`
--

CREATE TABLE IF NOT EXISTS `menu_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` varchar(60) NOT NULL DEFAULT '',
  `ubicacion` tinyint(4) DEFAULT NULL,
  `usar_imagen` tinyint(4) DEFAULT NULL,
  `tamano_imagen` varchar(20) DEFAULT NULL,
  `mostrar_opciones` tinyint(4) DEFAULT NULL,
  `limite` int(11) NOT NULL DEFAULT '0',
  `icono` varchar(25) DEFAULT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_permisos`
--

CREATE TABLE IF NOT EXISTS `menu_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) DEFAULT NULL,
  `anadir` tinyint(4) DEFAULT NULL,
  `anadir_submenu` tinyint(4) DEFAULT NULL,
  `anadir_idiomas` tinyint(4) DEFAULT NULL,
  `avanzadas` tinyint(4) DEFAULT NULL,
  `activar` tinyint(4) DEFAULT NULL,
  `ver` varchar(250) DEFAULT NULL,
  `acceder` varchar(250) DEFAULT NULL,
  `editar` varchar(250) DEFAULT NULL,
  `editar_idiomas` varchar(250) DEFAULT NULL,
  `cambiar` varchar(250) DEFAULT NULL,
  `ordenar` tinyint(4) DEFAULT NULL,
  `eliminar` varchar(250) NOT NULL,
  `eliminar_idiomas` varchar(250) DEFAULT NULL,
  `anadir_modulo` tinyint(4) DEFAULT NULL,
  `ver_modulo` varchar(250) DEFAULT NULL,
  `acceder_modulo` varchar(250) DEFAULT NULL,
  `eliminar_modulo` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE IF NOT EXISTS `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `valor` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `sufijo` varchar(200) CHARACTER SET utf8 NOT NULL,
  `icono` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `publicas` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `privadas` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `validar_cantidad` tinyint(4) NOT NULL DEFAULT '0',
  `cantidad` int(11) NOT NULL DEFAULT '0',
  `resto` tinyint(4) DEFAULT NULL,
  `tabla` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `activo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablaidiomas`
--

CREATE TABLE IF NOT EXISTS `tablaidiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(3) NOT NULL,
  `codigo2` varchar(2) DEFAULT NULL,
  `descripcion` varchar(150) NOT NULL,
  `original` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

--
-- Volcado de datos para la tabla `tablaidiomas`
--

INSERT INTO `tablaidiomas` (`id`, `codigo`, `codigo2`, `descripcion`, `original`) VALUES
(1, 'abk', 'ab', 'Abkhaz', 'Аҧсуа'),
(2, 'ace', NULL, 'Achinese', NULL),
(3, 'ach', NULL, 'Acoli', NULL),
(4, 'ada', NULL, 'Adangme', NULL),
(5, 'ady', NULL, 'Adyghe; Adygei', NULL),
(6, 'aar', 'aa', 'Afar', 'Afaraf'),
(7, 'afh', NULL, 'Afrihili', NULL),
(8, 'afr', 'af', 'Afrikaans', NULL),
(9, 'afa', NULL, 'Afro-Asiatic', NULL),
(10, 'ain', NULL, 'Ainu', NULL),
(11, 'aka', 'ak', 'Akan', NULL),
(12, 'akk', NULL, 'Akkadian', NULL),
(13, 'alb', 'sq', 'Albanian', 'Shqip'),
(14, 'ale', NULL, 'Aleut', NULL),
(15, 'alg', NULL, 'Algonquian languages', NULL),
(16, 'tut', NULL, 'Altaic', NULL),
(17, 'amh', 'am', 'Amharic', NULL),
(18, 'apa', NULL, 'Apache languages', NULL),
(19, 'ara', 'ar', 'Arabic', 'العربية'),
(20, 'arg', 'an', 'Aragonese', 'Aragonés'),
(21, 'arc', NULL, 'Aramaic', NULL),
(22, 'arp', NULL, 'Arapaho', NULL),
(23, 'arn', NULL, 'Araucanian', NULL),
(24, 'arw', NULL, 'Arawak', NULL),
(25, 'arm', 'hy', 'Armenian', NULL),
(26, 'rup', NULL, 'Aromanian', NULL),
(27, 'art', NULL, 'Artificial', NULL),
(28, 'asm', 'as', 'Assamese', NULL),
(29, 'ast', NULL, 'Asturian; Bable', NULL),
(30, 'ath', NULL, 'Athapascan languages', NULL),
(31, 'aus', NULL, 'Australian languages', NULL),
(32, 'map', NULL, 'Austronesian', NULL),
(33, 'ava', 'av', 'Avaric', NULL),
(34, 'ave', 'ae', 'Avestan', NULL),
(35, 'awa', NULL, 'Awadhi', NULL),
(36, 'aym', 'ay', 'Aymara', NULL),
(37, 'aze', 'az', 'Azerbaijani', 'Azərbaycanca'),
(38, 'ban', NULL, 'Balinese', NULL),
(39, 'bat', NULL, 'Baltic', NULL),
(40, 'bal', NULL, 'Baluchi', NULL),
(41, 'bam', 'bm', 'Bambara', 'Bamanankan'),
(42, 'bai', NULL, 'Bamileke languages', NULL),
(43, 'bad', NULL, 'Banda', NULL),
(44, 'bnt', NULL, 'Bantu', NULL),
(45, 'bas', NULL, 'Basa', NULL),
(46, 'bak', 'ba', 'Bashkir', NULL),
(47, 'baq', 'eu', 'Basque', 'Euskera'),
(48, 'btk', NULL, 'Batak (Indonesia)', NULL),
(49, 'bej', NULL, 'Beja', NULL),
(50, 'bel', 'be', 'Belarusian', 'Беларуская мова'),
(51, 'bem', NULL, 'Bemba', NULL),
(52, 'ben', 'bn', 'Bangla', NULL),
(53, 'ber', NULL, 'Berber', NULL),
(54, 'bho', NULL, 'Bhojpuri', NULL),
(55, 'bih', 'bh', 'Bihari', NULL),
(56, 'bik', NULL, 'Bikol', NULL),
(57, 'bin', NULL, 'Bini', NULL),
(58, 'bis', 'bi', 'Bislama', NULL),
(59, 'byn', NULL, 'Blin; Bilin', NULL),
(60, 'bos', 'bs', 'Bosnian', 'Bosanski'),
(61, 'bra', NULL, 'Brazilian Portuguese', NULL),
(62, 'bre', 'br', 'Breton', 'Brezhoneg'),
(63, 'bug', NULL, 'Buginese', NULL),
(64, 'bul', 'bg', 'Bulgarian', 'Български'),
(65, 'bua', NULL, 'Buriat', NULL),
(66, 'bpm', NULL, 'Bishnupriya Manipuri(Imarthar)', NULL),
(67, 'bur', 'my', 'Burmese', NULL),
(68, 'cad', NULL, 'Caddo', NULL),
(69, 'car', NULL, 'Carib', NULL),
(70, 'cat', 'ca', 'Catalan', 'català'),
(71, 'cau', NULL, 'Caucasian', NULL),
(72, 'ceb', NULL, 'Cebuano', NULL),
(73, 'cel', NULL, 'Celtic', NULL),
(74, 'cai', NULL, 'Central American Indian', NULL),
(75, 'chg', NULL, 'Chagatai', NULL),
(76, 'cmc', NULL, 'Chamic languages', NULL),
(77, 'cha', 'ch', 'Chamorro', NULL),
(78, 'che', 'ce', 'Chechen', NULL),
(79, 'chr', NULL, 'Cherokee', NULL),
(80, 'chy', NULL, 'Cheyenne', NULL),
(81, 'chb', NULL, 'Chibcha', NULL),
(82, 'nya', 'ny', 'Chichewa; Chewa; Nyanja', NULL),
(83, 'chi', 'zh', 'Chinese', '中文'),
(84, 'chn', NULL, 'Chinook jargon', NULL),
(85, 'chp', NULL, 'Dene Suline', NULL),
(86, 'cho', NULL, 'Choctaw', NULL),
(87, 'chu', 'cu', 'Church Slavonic', NULL),
(88, 'chk', NULL, 'Chuukese', NULL),
(89, 'chv', 'cv', 'Chuvash', NULL),
(90, 'nwc', NULL, 'Classical Newari', NULL),
(91, 'cop', NULL, 'Coptic', NULL),
(92, 'cor', 'kw', 'Cornish', 'Kernewek'),
(93, 'cos', 'co', 'Corsican', NULL),
(94, 'cre', 'cr', 'Cree', NULL),
(95, 'mus', NULL, 'Creek; Muskogean', NULL),
(96, 'crp', NULL, 'Creoles and Pidgins', NULL),
(97, 'cpe', NULL, 'Creoles and Pidgins, English-based', NULL),
(98, 'cpf', NULL, 'Creoles and Pidgins, French-based', NULL),
(99, 'cpp', NULL, 'Creoles and Pidgins, Portuguese-based', NULL),
(100, 'crh', NULL, 'Crimean Tatar; Crimean Turkish', NULL),
(101, 'scr', 'hr', 'Croatian', 'Hrvatski'),
(102, 'cus', NULL, 'Cushitic', NULL),
(103, 'cze', 'cs', 'Czech', 'Čeština'),
(104, 'dak', NULL, 'Dakota', NULL),
(105, 'dan', 'da', 'Danish', 'Dansk'),
(106, 'dar', NULL, 'Dargwa', NULL),
(107, 'day', NULL, 'Dayak', NULL),
(108, 'del', NULL, 'Delaware', NULL),
(109, 'din', NULL, 'Dinka', NULL),
(110, 'div', 'dv', 'Divehi', NULL),
(111, 'doi', NULL, 'Dogri', NULL),
(112, 'dgr', NULL, 'Dogrib', NULL),
(113, 'dra', NULL, 'Dravidian', NULL),
(114, 'dua', NULL, 'Duala', NULL),
(115, 'dut', 'nl', 'Dutch; Flemish', 'Nederlands'),
(116, 'dum', NULL, 'Dutch, Middle (ca. 1050–1350)', NULL),
(117, 'dyu', NULL, 'Dyula', NULL),
(118, 'dzo', 'dz', 'Dzongkha', 'རྫོང་ཁ'),
(119, 'efi', NULL, 'Efik', NULL),
(120, 'egy', NULL, 'Egyptian (Ancient)', NULL),
(121, 'eka', NULL, 'Ekajuk', NULL),
(122, 'elx', NULL, 'Elamite', NULL),
(123, 'eng', 'en', 'English', 'English'),
(124, 'enm', NULL, 'English, Middle (ca. 1100–1500)', NULL),
(125, 'ang', NULL, 'English, Old (ca. 450–1100)', 'Englisc'),
(126, 'myv', NULL, 'Erzya', NULL),
(127, 'epo', 'eo', 'Esperanto', 'Esperanto'),
(128, 'est', 'et', 'Estonian', 'Eesti keel'),
(129, 'ewe', 'ee', 'Ewe', 'Ɛʋɛgbɛ'),
(130, 'ewo', NULL, 'Ewondo', NULL),
(131, 'fan', NULL, 'Fang', NULL),
(132, 'fat', NULL, 'Fanti', NULL),
(133, 'fao', 'fo', 'Faroese', NULL),
(134, 'fij', 'fj', 'Fijian', NULL),
(135, 'fil', NULL, 'Filipino', NULL),
(136, 'fin', 'fi', 'Finnish', 'suomi'),
(137, 'fiu', NULL, 'Finno-Ugrian', NULL),
(138, 'fon', NULL, 'Fon', NULL),
(139, 'fre', 'fr', 'French', 'français'),
(140, 'fry', 'fy', 'Frisian', NULL),
(141, 'fur', NULL, 'Friulian', NULL),
(142, 'ful', 'ff', 'Fulah', 'Fulfulde, Pulaar, Pular'),
(143, 'gaa', NULL, 'Ga', NULL),
(144, 'glg', 'gl', 'Galician', NULL),
(145, 'lug', 'lg', 'Ganda', NULL),
(146, 'gay', NULL, 'Gayo', NULL),
(147, 'gba', NULL, 'Gbaya', NULL),
(148, 'gez', NULL, 'Geez', NULL),
(149, 'geo', 'ka', 'Georgian', 'Kartuli'),
(150, 'ger', 'de', 'German', 'Deutsch'),
(151, 'nds', NULL, 'German, Low; Low German; Saxon, Low; Low Saxon', 'Nederdüütsch, Plattdüütsch'),
(152, 'gem', NULL, 'Germanic', NULL),
(153, 'gil', NULL, 'Gilbertese; Kiribati', NULL),
(154, 'gon', NULL, 'Gondi', NULL),
(155, 'gor', NULL, 'Gorontalo', NULL),
(156, 'got', NULL, 'Gothic', 'Gutisk'),
(157, 'grb', NULL, 'Grebo', NULL),
(158, 'gre', 'el', 'Greek', 'Ελληνικά'),
(159, 'kal', 'kl', 'Greenlandic; Kalaallisut', NULL),
(160, 'grn', 'gn', 'Guarani', NULL),
(161, 'guj', 'gu', 'Gujarati', NULL),
(162, 'gwi', NULL, 'Gwich''in', NULL),
(163, 'hai', NULL, 'Haida', NULL),
(164, 'hat', 'ht', 'Haitian Creole; Haitian', 'Kreyòl ayisyen'),
(165, 'hau', 'ha', 'Hausa', 'هَوُسَ'),
(166, 'haw', NULL, 'Hawaiian', '''Ōlelo Hawai''i'),
(167, 'heb', 'he', 'Hebrew', 'עברית'),
(168, 'her', 'hz', 'Herero', NULL),
(169, 'hil', NULL, 'Hiligaynon', NULL),
(170, 'him', NULL, 'Himachali', NULL),
(171, 'hin', 'hi', 'Hindi', 'हिन्दी'),
(172, 'hmo', 'ho', 'Hiri Motu', NULL),
(173, 'hit', NULL, 'Hittite', NULL),
(174, 'hmn', NULL, 'Hmong', NULL),
(175, 'hun', 'hu', 'Hungarian', 'Magyar'),
(176, 'hup', NULL, 'Hupa', NULL),
(177, 'iba', NULL, 'Iban', NULL),
(178, 'ice', 'is', 'Icelandic', 'íslenska'),
(179, 'ido', 'io', 'Ido', NULL),
(180, 'ibo', 'ig', 'Igbo', NULL),
(181, 'ijo', NULL, 'Ijo', NULL),
(182, 'ilo', NULL, 'Iloko', NULL),
(183, 'smn', NULL, 'Inari Sami', NULL),
(184, 'inc', NULL, 'Indic', NULL),
(185, 'ine', NULL, 'Indo-European', NULL),
(186, 'ind', 'id', 'Indonesian', NULL),
(187, 'inh', NULL, 'Ingush', NULL),
(188, 'ina', 'ia', 'Interlingua (International Auxiliary Language Association)', NULL),
(189, 'ile', 'ie', 'Interlingue', NULL),
(190, 'iku', 'iu', 'Inuktitut', NULL),
(191, 'ipk', 'ik', 'Inupiaq', NULL),
(192, 'ira', NULL, 'Iranian', NULL),
(193, 'gle', 'ga', 'Irish', 'Gaeilge'),
(194, 'iro', NULL, 'Iroquoian languages', NULL),
(195, 'ita', 'it', 'Italian', 'Italiano'),
(196, 'jpn', 'ja', 'Japanese', '日本語'),
(197, 'jav', 'jv', 'Javanese', NULL),
(198, 'jbo', NULL, 'Lojban', NULL),
(199, 'jrb', NULL, 'Judeo-Arabic', NULL),
(200, 'jpr', NULL, 'Judeo-Persian', NULL),
(201, 'kbd', NULL, 'Kabardian', NULL),
(202, 'kab', NULL, 'Kabyle', NULL),
(203, 'kac', NULL, 'Kachin', NULL),
(204, 'xal', NULL, 'Kalmyk; Oirat', NULL),
(205, 'kam', NULL, 'Kamba', NULL),
(206, 'kan', 'kn', 'Kannada', NULL),
(207, 'kau', 'kr', 'Kanuri', NULL),
(208, 'krc', NULL, 'Karachay-Balkar', NULL),
(209, 'kaa', NULL, 'Kara-Kalpak', NULL),
(210, 'kar', NULL, 'Karen', NULL),
(211, 'kas', 'ks', 'Kashmiri', NULL),
(212, 'csb', NULL, 'Kashubian', NULL),
(213, 'kaw', NULL, 'Kawi', NULL),
(214, 'kaz', 'kk', 'Kazakh', NULL),
(215, 'kha', NULL, 'Khasi', NULL),
(216, 'khm', 'km', 'Khmer', NULL),
(217, 'khi', NULL, 'Khoisan', NULL),
(218, 'kho', NULL, 'Khotanese', NULL),
(219, 'kik', 'ki', 'Kikuyu; Gikuyu', NULL),
(220, 'kmb', NULL, 'Kimbundu', NULL),
(221, 'kin', 'rw', 'Kinyarwanda', NULL),
(222, 'kir', 'ky', 'Kirghiz', NULL),
(223, 'kom', 'kv', 'Komi', NULL),
(224, 'kon', 'kg', 'Kongo', NULL),
(225, 'kok', NULL, 'Konkani', NULL),
(226, 'kor', 'ko', 'Korean', NULL),
(227, 'kos', NULL, 'Kosraean', NULL),
(228, 'kpe', NULL, 'Kpelle', NULL),
(229, 'kro', NULL, 'Kru', NULL),
(230, 'kua', 'kj', 'Kuanyama; Kwanyama', NULL),
(231, 'kum', NULL, 'Kumyk', NULL),
(232, 'kur', 'ku', 'Kurdish', NULL),
(233, 'kru', NULL, 'Kurukh', NULL),
(234, 'kut', NULL, 'Kutenai', NULL),
(235, 'krl', NULL, 'Karelian', NULL),
(236, 'lad', NULL, 'Ladino', NULL),
(237, 'lah', NULL, 'Lahnda', NULL),
(238, 'lam', NULL, 'Lamba', NULL),
(239, 'lao', 'lo', 'Lao', NULL),
(240, 'lat', 'la', 'Latin', NULL),
(241, 'lav', 'lv', 'Latvian', NULL),
(242, 'lez', NULL, 'Lezghian', NULL),
(243, 'lim', 'li', 'Limburgish; Limburger; Limburgan', NULL),
(244, 'lin', 'ln', 'Lingala', NULL),
(245, 'lit', 'lt', 'Lithuanian', NULL),
(246, 'jbo', NULL, 'Lojban', NULL),
(247, 'dsb', NULL, 'Lower Sorbian', NULL),
(248, 'loz', NULL, 'Lozi', NULL),
(249, 'lub', 'lu', 'Luba-Katanga', NULL),
(250, 'lua', NULL, 'Luba-Lulua', NULL),
(251, 'lui', NULL, 'Luiseno', NULL),
(252, 'smj', NULL, 'Lule Sami', NULL),
(253, 'lun', NULL, 'Lunda', NULL),
(254, 'luo', NULL, 'Luo', NULL),
(255, 'lus', NULL, 'Lushai', NULL),
(256, 'ltz', 'lb', 'Luxembourgish; Letzeburgesch', NULL),
(257, 'mac', 'mk', 'Macedonian', NULL),
(258, 'mad', NULL, 'Madurese', NULL),
(259, 'mag', NULL, 'Magahi', NULL),
(260, 'mai', NULL, 'Maithili', NULL),
(261, 'mak', NULL, 'Makasar', NULL),
(262, 'mlg', 'mg', 'Malagasy', NULL),
(263, 'may', 'ms', 'Malay', NULL),
(264, 'mal', 'ml', 'Malayalam', NULL),
(265, 'mlt', 'mt', 'Maltese', NULL),
(266, 'mnc', NULL, 'Manchu', NULL),
(267, 'mdr', NULL, 'Mandar', NULL),
(268, 'man', NULL, 'Mandingo', NULL),
(269, 'mni', NULL, 'Manipuri', NULL),
(270, 'mno', NULL, 'Manobo languages', NULL),
(271, 'glv', 'gv', 'Manx', NULL),
(272, 'mao', 'mi', 'Māori', NULL),
(273, 'mar', 'mr', 'Marathi', NULL),
(274, 'chm', NULL, 'Mari', NULL),
(275, 'mah', 'mh', 'Marshallese', NULL),
(276, 'mwr', NULL, 'Marwari', NULL),
(277, 'mas', NULL, 'Masai', NULL),
(278, 'myn', NULL, 'Mayan languages', NULL),
(279, 'men', NULL, 'Mende', NULL),
(280, 'mic', NULL, 'Mi''kmaq; Micmac', NULL),
(281, 'min', NULL, 'Minangkabau', NULL),
(282, 'mwl', NULL, 'Mirandese', NULL),
(283, 'moh', NULL, 'Mohawk', NULL),
(284, 'mdf', NULL, 'Moksha', NULL),
(285, 'mol', 'mo', 'Moldavian', NULL),
(286, 'mkh', NULL, 'Mon-Khmer', NULL),
(287, 'lol', NULL, 'Mongo', NULL),
(288, 'mon', 'mn', 'Mongolian', NULL),
(289, 'mos', NULL, 'Mossi', NULL),
(290, 'mun', NULL, 'Munda languages', NULL),
(291, 'nah', NULL, 'Nahuatl', NULL),
(292, 'nau', 'na', 'Nauruan', NULL),
(293, 'nav', 'nv', 'Navajo; Navaho', NULL),
(294, 'nde', 'nd', 'Ndebele, North', NULL),
(295, 'nbl', 'nr', 'Ndebele, South', NULL),
(296, 'ndo', 'ng', 'Ndonga', NULL),
(297, 'nap', NULL, 'Neapolitan', NULL),
(298, 'nep', 'ne', 'Nepali', NULL),
(299, 'new', NULL, 'Newari; Nepal Bhasa', 'Nepal Bhyae'),
(300, 'nia', NULL, 'Nias', NULL),
(301, 'nic', NULL, 'Niger-Kordofanian', NULL),
(302, 'ssa', NULL, 'Nilo-Saharan', NULL),
(303, 'niu', NULL, 'Niuean', NULL),
(304, 'nog', NULL, 'Nogai', NULL),
(305, 'non', NULL, 'Norse, Old', NULL),
(306, 'nai', NULL, 'North American Indian', NULL),
(307, 'sme', 'se', 'Northern Sami', NULL),
(308, 'nor', 'no', 'Norwegian', 'Norsk'),
(309, 'nob', 'nb', 'Norwegian Bokmål', 'Norsk'),
(310, 'nno', 'nn', 'Norwegian Nynorsk', 'Norsk'),
(311, 'nub', NULL, 'Nubian languages', NULL),
(312, 'nym', NULL, 'Nyamwezi', NULL),
(313, 'nyn', NULL, 'Nyankole', NULL),
(314, 'nyo', NULL, 'Nyoro', NULL),
(315, 'nzi', NULL, 'Nzima', NULL),
(316, 'oji', 'oj', 'Ojibwa', NULL),
(317, 'ori', 'or', 'Oriya', NULL),
(318, 'orm', 'om', 'Oromo', NULL),
(319, 'osa', NULL, 'Osage', NULL),
(320, 'oss', 'os', 'Ossetian; Ossetic', NULL),
(321, 'oto', NULL, 'Otomian languages', NULL),
(322, 'pal', NULL, 'Pahlavi', NULL),
(323, 'pau', NULL, 'Palauan', NULL),
(324, 'pli', 'pi', 'Pali', 'पालि'),
(325, 'pam', NULL, 'Pampanga; Kapampangan', NULL),
(326, 'pag', NULL, 'Pangasinan', NULL),
(327, 'pap', NULL, 'Papiamento', NULL),
(328, 'paa', NULL, 'Papuan', NULL),
(329, 'per', 'fa', 'Persian', 'فارسی'),
(330, 'phi', NULL, 'Philippine', NULL),
(331, 'phn', NULL, 'Phoenician', NULL),
(332, 'pon', NULL, 'Pohnpeian', NULL),
(333, 'pol', 'pl', 'Polish', 'Polski'),
(334, 'por', 'pt', 'Portuguese', 'Português'),
(335, 'pra', NULL, 'Prakrit languages', NULL),
(336, 'pan', 'pa', 'Punjabi; Panjabi', 'ਪੰਜਾਬੀ, پنجابی'),
(337, 'pus', 'ps', 'Pushto', 'پښت'),
(338, 'que', 'qu', 'Quechua', NULL),
(339, 'roh', 'rm', 'Raeto-Romance', NULL),
(340, 'raj', NULL, 'Rajasthani', NULL),
(341, 'rap', NULL, 'Rapanui', NULL),
(342, 'rar', NULL, 'Rarotongan', NULL),
(343, 'roa', NULL, 'Romance', NULL),
(344, 'rum', 'ro', 'Romanian', NULL),
(345, 'rom', NULL, 'Romany', NULL),
(346, 'run', 'rn', 'Rundi', NULL),
(347, 'rus', 'ru', 'Russian', 'Русский'),
(348, 'sah', NULL, 'Sakha (Yakut)', 'Саха тыла'),
(349, 'sal', NULL, 'Salishan languages', NULL),
(350, 'sam', NULL, 'Samaritan Aramaic', 'ארמית, ܐܪܡܝܐ'),
(351, 'smi', NULL, 'Sami languages', NULL),
(352, 'smo', 'sm', 'Samoan', NULL),
(353, 'sad', NULL, 'Sandawe', NULL),
(354, 'sag', 'sg', 'Sango', NULL),
(355, 'san', 'sa', 'Sanskrit', 'संस्कृतम्'),
(356, 'sat', NULL, 'Santali', NULL),
(357, 'srd', 'sc', 'Sardinian', NULL),
(358, 'sas', NULL, 'Sasak', NULL),
(359, 'sco', NULL, 'Scots', NULL),
(360, 'gla', 'gd', 'Scottish Gaelic; Gaelic', 'Gàidhlig'),
(361, 'sel', NULL, 'Selkup', NULL),
(362, 'sem', NULL, 'Semitic', NULL),
(363, 'scc', 'sr', 'Serbian', 'српски'),
(364, 'srr', NULL, 'Serer', NULL),
(365, 'shn', NULL, 'Shan', NULL),
(366, 'sna', 'sn', 'Shona', NULL),
(367, 'iii', 'ii', 'Sichuan Yi', NULL),
(368, 'scn', NULL, 'Sicilian', 'Sicilianu'),
(369, 'sid', NULL, 'Sidamo', NULL),
(370, 'sgn', NULL, 'Sign languages', NULL),
(371, 'bla', NULL, 'Siksika', NULL),
(372, 'snd', 'sd', 'Sindhi', 'سنڌي، سندھی, सिन्धी'),
(373, 'sin', 'si', 'Sinhalese; Sinhala', NULL),
(374, 'sit', NULL, 'Sino-Tibetan', NULL),
(375, 'sio', NULL, 'Siouan languages', NULL),
(376, 'sms', NULL, 'Skolt Sami', NULL),
(377, 'den', NULL, 'Slave (Athapascan)', NULL),
(378, 'sla', NULL, 'Slavic', NULL),
(379, 'slo', 'sk', 'Slovak', 'slovenčina'),
(380, 'slv', 'sl', 'Slovenian', 'slovenščina'),
(381, 'sog', NULL, 'Sogdian', NULL),
(382, 'som', 'so', 'Somali', 'af Soomaali'),
(383, 'son', NULL, 'Songhai', NULL),
(384, 'snk', NULL, 'Soninke', NULL),
(385, 'wen', NULL, 'Sorbian languages', NULL),
(386, 'nso', NULL, 'Sotho, Northern; Pedi; Sepedi', NULL),
(387, 'sot', 'st', 'Sotho, Southern', NULL),
(388, 'sai', NULL, 'South American Indian', NULL),
(389, 'alt', NULL, 'Southern Altai', NULL),
(390, 'sma', NULL, 'Southern Sami', NULL),
(391, 'spa', 'es', 'Spanish; Castilian', 'español, castellano'),
(392, 'suk', NULL, 'Sukuma', NULL),
(393, 'sux', NULL, 'Sumerian', 'eme-ĝir'),
(394, 'sun', 'su', 'Sundanese', NULL),
(395, 'sus', NULL, 'Susu', NULL),
(396, 'swa', 'sw', 'Swahili', NULL),
(397, 'ssw', 'ss', 'Swati', NULL),
(398, 'swe', 'sv', 'Swedish', 'Svenska'),
(399, 'syr', NULL, 'Syriac', 'ܣܘܪܝܝܐ'),
(400, 'tgl', 'tl', 'Tagalog', 'Tagalog'),
(401, 'tah', 'ty', 'Tahitian', NULL),
(402, 'tai', NULL, 'Tai', NULL),
(403, 'tgk', 'tg', 'Tajik', 'тоҷикӣ, تاجیکی'),
(404, 'tmh', NULL, 'Tamashek', NULL),
(405, 'tam', 'ta', 'Tamil', 'தமிழ'),
(406, 'tat', 'tt', 'Tatar', 'татарча, tatarça, تاتارچا'),
(407, 'tel', 'te', 'Telugu', 'తెలుగు'),
(408, 'ter', NULL, 'Tereno', NULL),
(409, 'tet', NULL, 'Tetum', NULL),
(410, 'tha', 'th', 'Thai', 'ภาษาไทย'),
(411, 'tib', 'bo', 'Tibetan', 'བོད་ཡིག'),
(412, 'tig', NULL, 'Tigre', NULL),
(413, 'tir', 'ti', 'Tigrinya', NULL),
(414, 'tem', NULL, 'Timne', NULL),
(415, 'tiv', NULL, 'Tiv', NULL),
(416, 'tli', NULL, 'Tlingit', NULL),
(417, 'tpi', NULL, 'Tok Pisin', NULL),
(418, 'tkl', NULL, 'Tokelau', NULL),
(419, 'tog', NULL, 'Tonga (Malawi)', NULL),
(420, 'ton', 'to', 'Tongan', NULL),
(421, 'tsi', NULL, 'Tsimshian', NULL),
(422, 'tso', 'ts', 'Tsonga', NULL),
(423, 'tsn', 'tn', 'Tswana', NULL),
(424, 'tul', NULL, 'Tulu', 'ತುಳು'),
(425, 'tum', NULL, 'Tumbuka', NULL),
(426, 'tup', NULL, 'Tupi languages', 'Nheengatu'),
(427, 'tur', 'tr', 'Turkish', 'Türkçe'),
(428, 'tuk', 'tk', 'Turkmen', 'Түркмен'),
(429, 'tvl', NULL, 'Tuvalu', NULL),
(430, 'tyv', NULL, 'Tuvinian', 'Тыва дыл'),
(431, 'twi', 'tw', 'Twi', NULL),
(432, 'udm', NULL, 'Udmurt', 'удмурт кыл'),
(433, 'uga', NULL, 'Ugaritic', NULL),
(434, 'uig', 'ug', 'Uighur; Uyghur', 'ئۇيغۇرچ, Uyğurçe, 维吾尔语'),
(435, 'ukr', 'uk', 'Ukrainian', 'українська мова'),
(436, 'umb', NULL, 'Umbundu', NULL),
(437, 'hsb', NULL, 'Upper Sorbian', 'hornjoserbsce'),
(438, 'urd', 'ur', 'Urdu', 'اردو'),
(439, 'uzb', 'uz', 'Uzbek', 'O''zbek, Ўзбек, أۇزبېك'),
(440, 'vai', NULL, 'Vai', NULL),
(441, 'ven', 've', 'Venda', 'Tshivenda'),
(442, 'vie', 'vi', 'Vietnamese', 'Tiếng Việt'),
(443, 'vol', 'vo', 'Volapük', NULL),
(444, 'vot', NULL, 'Votic', NULL),
(445, 'wak', NULL, 'Wakashan languages', NULL),
(446, 'wal', NULL, 'Walamo', NULL),
(447, 'wln', 'wa', 'Walloon', NULL),
(448, 'war', NULL, 'Waray', NULL),
(449, 'was', NULL, 'Washo', NULL),
(450, 'wel', 'cy', 'Welsh', 'Cymraeg'),
(451, 'wol', 'wo', 'Wolof', NULL),
(452, 'wuu', NULL, 'Wu Chinese (Shanghainese)', NULL),
(453, 'xho', 'xh', 'Xhosa', NULL),
(454, 'yao', NULL, 'Yao', 'Chiyao'),
(455, 'yap', NULL, 'Yapese', NULL),
(456, 'yid', 'yi', 'Yiddish', 'ייִדיש'),
(457, 'yor', 'yo', 'Yoruba', 'Yorùbá'),
(458, 'ypk', NULL, 'Yupik languages', NULL),
(459, 'znd', NULL, 'Zande', NULL),
(460, 'zap', NULL, 'Zapotec', NULL),
(461, 'zen', NULL, 'Zenaga', NULL),
(462, 'zha', 'za', 'Zhuang; Chuang', 'Sawcuengh'),
(463, 'zul', 'zu', 'Zulu', 'isiZulu'),
(464, 'zun', NULL, 'Zuni', 'Shiwi');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) CHARACTER SET utf8 NOT NULL,
  `password` varchar(70) CHARACTER SET utf8 NOT NULL,
  `foto` varchar(200) CHARACTER SET utf32 NOT NULL,
  `nivel` varchar(3) CHARACTER SET utf8 NOT NULL,
  `activar` tinyint(4) DEFAULT NULL,
  `key` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `nombre` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `apellidos` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8,
  `direccion` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `poblacion` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `provincia` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `c_postal` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `mostrar_email` tinyint(4) DEFAULT '0',
  `mostrar_nombre_real` tinyint(4) DEFAULT '0',
  `permitir_contacto` tinyint(4) DEFAULT '0',
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_ultima_modificacion` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `login`, `password`, `foto`, `nivel`, `activar`, `key`, `nombre`, `apellidos`, `descripcion`, `direccion`, `poblacion`, `provincia`, `c_postal`, `telefono`, `email`, `mostrar_email`, `mostrar_nombre_real`, `permitir_contacto`, `fecha_alta`, `fecha_ultima_modificacion`) VALUES
(6, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', '', 'adm', 1, NULL, 'Manuel', 'Ruiz-Falcó Couto', NULL, 'Avda. Madrid', 'Lugo', 'Lugo', '27002', '653103722', 'mruizfalco@mundo-r.com', 1, 1, 1, '2014-10-05 19:20:00', '2015-01-19 18:43:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_niveles`
--

CREATE TABLE IF NOT EXISTS `usuarios_niveles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idnivel` int(11) NOT NULL DEFAULT '0',
  `nivel` varchar(40) NOT NULL DEFAULT '',
  `descripcion` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_permisos`
--

CREATE TABLE IF NOT EXISTS `usuarios_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` int(11) NOT NULL DEFAULT '0',
  `modulo` varchar(40) NOT NULL DEFAULT '',
  `acceso` tinyint(4) NOT NULL DEFAULT '0',
  `insertar` tinyint(4) DEFAULT NULL,
  `editar` tinyint(4) DEFAULT NULL,
  `editar_propio` tinyint(4) DEFAULT NULL,
  `eliminar` tinyint(4) NOT NULL DEFAULT '0',
  `eliminar_propio` tinyint(4) DEFAULT NULL,
  `subir_fichero` tinyint(4) DEFAULT NULL,
  `editar_fichero` tinyint(4) DEFAULT NULL,
  `editar_fichero_propio` tinyint(4) DEFAULT NULL,
  `eliminar_fichero` tinyint(4) DEFAULT NULL,
  `eliminar_fichero_propio` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
