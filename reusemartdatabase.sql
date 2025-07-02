-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 08:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reusemartdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `alamats`
--

CREATE TABLE `alamats` (
  `ID_ALAMAT` bigint(20) UNSIGNED NOT NULL,
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `JUDUL` varchar(255) NOT NULL,
  `NAMA_JALAN` varchar(255) NOT NULL,
  `DESA_KELURAHAN` varchar(255) NOT NULL,
  `KECAMATAN` varchar(255) NOT NULL,
  `KABUPATEN` varchar(255) NOT NULL,
  `PROVINSI` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `ID_BADGE` bigint(20) UNSIGNED NOT NULL,
  `ID_PENITIP` bigint(20) UNSIGNED NOT NULL,
  `NAMA_BADGE` varchar(255) NOT NULL,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangs`
--

CREATE TABLE `barangs` (
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `ID_KATEGORI` bigint(20) UNSIGNED NOT NULL,
  `ID_PENITIP` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_PEGAWAI` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_ORGANISASI` bigint(20) UNSIGNED DEFAULT NULL,
  `NAMA_BARANG` varchar(255) NOT NULL,
  `KODE_BARANG` varchar(5) DEFAULT NULL,
  `HARGA_BARANG` double NOT NULL,
  `TGL_MASUK` date NOT NULL,
  `TGL_KELUAR` date DEFAULT NULL,
  `TGL_AMBIL` date DEFAULT NULL,
  `GARANSI` date DEFAULT NULL,
  `BERAT` decimal(8,2) DEFAULT NULL,
  `DESKRIPSI` varchar(1000) NOT NULL,
  `RATING` double NOT NULL DEFAULT 0,
  `STATUS_BARANG` varchar(255) NOT NULL,
  `FOTO_BARANG` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `ID_CART` bigint(20) UNSIGNED NOT NULL,
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `QUANTITY` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `desa_kelurahans`
--

CREATE TABLE `desa_kelurahans` (
  `id_desa_kelurahan` bigint(20) UNSIGNED NOT NULL,
  `id_kecamatan` bigint(20) UNSIGNED NOT NULL,
  `nama_desa_kelurahan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `desa_kelurahans`
--

INSERT INTO `desa_kelurahans` (`id_desa_kelurahan`, `id_kecamatan`, `nama_desa_kelurahan`, `created_at`, `updated_at`) VALUES
(43172, 2948, 'Sumberagung', NULL, NULL),
(43173, 2948, 'Moyudan', NULL, NULL),
(43174, 2948, 'Sumbermulyo', NULL, NULL),
(43175, 2948, 'Tegalsari', NULL, NULL),
(43176, 2948, 'Sumberrejo', NULL, NULL),
(43177, 2949, 'Sendangmulyo', NULL, NULL),
(43178, 2949, 'Minggir', NULL, NULL),
(43179, 2949, 'Karanglo', NULL, NULL),
(43180, 2949, 'Sendangrejo', NULL, NULL),
(43181, 2949, 'Demangan', NULL, NULL),
(43182, 2950, 'Margodadi', NULL, NULL),
(43183, 2950, 'Seyegan', NULL, NULL),
(43184, 2950, 'Margomulyo', NULL, NULL),
(43185, 2950, 'Margoagung', NULL, NULL),
(43186, 2950, 'Wedomartani', NULL, NULL),
(43187, 2951, 'Sidorejo', NULL, NULL),
(43188, 2951, 'Godean', NULL, NULL),
(43189, 2951, 'Sidoarum', NULL, NULL),
(43190, 2951, 'Sidoagung', NULL, NULL),
(43191, 2951, 'Sidomulyo', NULL, NULL),
(43192, 2952, 'Banyuraden', NULL, NULL),
(43193, 2952, 'Gamping', NULL, NULL),
(43194, 2952, 'Trihanggo', NULL, NULL),
(43195, 2952, 'Ambarukmo', NULL, NULL),
(43196, 2952, 'Nogotirto', NULL, NULL),
(43197, 2953, 'Sinduharjo', NULL, NULL),
(43198, 2953, 'Mlati', NULL, NULL),
(43199, 2953, 'Sendangadi', NULL, NULL),
(43200, 2953, 'Tirtoadi', NULL, NULL),
(43201, 2953, 'Tlogoadi', NULL, NULL),
(43202, 2954, 'Caturtunggal', NULL, NULL),
(43203, 2954, 'Depok', NULL, NULL),
(43204, 2954, 'Maguwoharjo', NULL, NULL),
(43205, 2954, 'Condongcatur', NULL, NULL),
(43206, 2955, 'Tegaltirto', NULL, NULL),
(43207, 2955, 'Berbah', NULL, NULL),
(43208, 2955, 'Kalitirto', NULL, NULL),
(43209, 2955, 'Jogotirto', NULL, NULL),
(43210, 2956, 'Bokoharjo', NULL, NULL),
(43211, 2956, 'Prambanan', NULL, NULL),
(43212, 2956, 'Sumberharjo', NULL, NULL),
(43213, 2956, 'Wukirharjo', NULL, NULL),
(43214, 2957, 'Tamanmartani', NULL, NULL),
(43215, 2957, 'Kalasan', NULL, NULL),
(43216, 2957, 'Purwomartani', NULL, NULL),
(43217, 2957, 'Selomartani', NULL, NULL),
(43218, 2958, 'Widodomartani', NULL, NULL),
(43219, 2958, 'Ngemplak', NULL, NULL),
(43220, 2958, 'Bimomartani', NULL, NULL),
(43221, 2958, 'Umbulmartani', NULL, NULL),
(43222, 2959, 'Sardonoharjo', NULL, NULL),
(43223, 2959, 'Ngaglik', NULL, NULL),
(43224, 2959, 'Sukoharjo', NULL, NULL),
(43225, 2959, 'Minomartani', NULL, NULL),
(43226, 2959, 'Donoharjo', NULL, NULL),
(43227, 2960, 'Triharjo', NULL, NULL),
(43228, 2960, 'Sleman', NULL, NULL),
(43229, 2960, 'Pandowoharjo', NULL, NULL),
(43230, 2960, 'Caturharjo', NULL, NULL),
(43231, 2960, 'Tridadi', NULL, NULL),
(43232, 2961, 'Tamanan', NULL, NULL),
(43233, 2961, 'Tempel', NULL, NULL),
(43234, 2961, 'Banyurejo', NULL, NULL),
(43235, 2961, 'Merdikorejo', NULL, NULL),
(43236, 2961, 'Margorejo', NULL, NULL),
(43237, 2962, 'Girikerto', NULL, NULL),
(43238, 2962, 'Turi', NULL, NULL),
(43239, 2962, 'Wonosari', NULL, NULL),
(43240, 2962, 'Bangunkerto', NULL, NULL),
(43241, 2962, 'Donokerto', NULL, NULL),
(43242, 2963, 'Harjobinangun', NULL, NULL),
(43243, 2963, 'Pakem', NULL, NULL),
(43244, 2963, 'Candibinangun', NULL, NULL),
(43245, 2963, 'Pakembinangun', NULL, NULL),
(43246, 2963, 'Hargobinangun', NULL, NULL),
(43247, 2964, 'Glagaharjo', NULL, NULL),
(43248, 2964, 'Cangkringan', NULL, NULL),
(43249, 2964, 'Wukirsari', NULL, NULL),
(43250, 2964, 'Umbulharjo', NULL, NULL),
(43251, 2964, 'Kepuharjo', NULL, NULL),
(43252, 2965, 'Triwidadi', NULL, NULL),
(43253, 2965, 'Srandakan', NULL, NULL),
(43254, 2965, 'Poncosari', NULL, NULL),
(43255, 2965, 'Trimulyo', NULL, NULL),
(43256, 2966, 'Murtigading', NULL, NULL),
(43257, 2966, 'Sanden', NULL, NULL),
(43258, 2966, 'Gadingsari', NULL, NULL),
(43259, 2966, 'Srigeude', NULL, NULL),
(43260, 2967, 'Tirtomulyo', NULL, NULL),
(43261, 2967, 'Kretek', NULL, NULL),
(43262, 2967, 'Parangtritis', NULL, NULL),
(43263, 2967, 'Donotirto', NULL, NULL),
(43264, 2968, 'Pananggungan', NULL, NULL),
(43265, 2968, 'Pundong', NULL, NULL),
(43266, 2968, 'Srihardono', NULL, NULL),
(43267, 2968, 'Seloharjo', NULL, NULL),
(43268, 2969, 'Sidomulyo', NULL, NULL),
(43269, 2969, 'Bambanglipuro', NULL, NULL),
(43270, 2969, 'Mulyodadi', NULL, NULL),
(43271, 2969, 'Sumbermulyo', NULL, NULL),
(43272, 2970, 'Pandak', NULL, NULL),
(43273, 2970, 'Caturharjo', NULL, NULL),
(43274, 2970, 'Wijirejo', NULL, NULL),
(43275, 2970, 'Gilangharjo', NULL, NULL),
(43276, 2971, 'Bantul', NULL, NULL),
(43277, 2971, 'Palbapang', NULL, NULL),
(43278, 2971, 'Ringinharjo', NULL, NULL),
(43279, 2971, 'Sabdodadi', NULL, NULL),
(43280, 2972, 'Bangunjiwo', NULL, NULL),
(43281, 2972, 'Jetis', NULL, NULL),
(43282, 2972, 'Canden', NULL, NULL),
(43283, 2972, 'Sumberagung', NULL, NULL),
(43284, 2973, 'Imogiri', NULL, NULL),
(43285, 2973, 'Karangtalun', NULL, NULL),
(43286, 2973, 'Wukirsari', NULL, NULL),
(43287, 2973, 'Girirejo', NULL, NULL),
(43288, 2974, 'Dlingo', NULL, NULL),
(43289, 2974, 'Temuwuh', NULL, NULL),
(43290, 2974, 'Jatimulyo', NULL, NULL),
(43291, 2974, 'Mangunan', NULL, NULL),
(43292, 2975, 'Pleret', NULL, NULL),
(43293, 2975, 'Wonokromo', NULL, NULL),
(43294, 2975, 'Bawuran', NULL, NULL),
(43295, 2975, 'Segoroyoso', NULL, NULL),
(43296, 2976, 'Piyungan', NULL, NULL),
(43297, 2976, 'Srimulyo', NULL, NULL),
(43298, 2976, 'Srimartani', NULL, NULL),
(43299, 2976, 'Bokoharjo', NULL, NULL),
(43300, 2977, 'Banguntapan', NULL, NULL),
(43301, 2977, 'Jagalan', NULL, NULL),
(43302, 2977, 'Singosaren', NULL, NULL),
(43303, 2977, 'Tamanan', NULL, NULL),
(43304, 2978, 'Sewon', NULL, NULL),
(43305, 2978, 'Panggungharjo', NULL, NULL),
(43306, 2978, 'Pendowoharjo', NULL, NULL),
(43307, 2978, 'Timbulharjo', NULL, NULL),
(43308, 2979, 'Kasihan', NULL, NULL),
(43309, 2979, 'Tamantirto', NULL, NULL),
(43310, 2979, 'Ngestiharjo', NULL, NULL),
(43311, 2979, 'Bangunjiwo', NULL, NULL),
(43312, 2980, 'Pajangan', NULL, NULL),
(43313, 2980, 'Guwosari', NULL, NULL),
(43314, 2980, 'Sendangsari', NULL, NULL),
(43315, 2980, 'Triharjo', NULL, NULL),
(43316, 2981, 'Sedayu', NULL, NULL),
(43317, 2981, 'Argomulyo', NULL, NULL),
(43318, 2981, 'Argosari', NULL, NULL),
(43319, 2981, 'Argodadi', NULL, NULL),
(43320, 2982, 'Suryodiningratan', NULL, NULL),
(43321, 2982, 'Purbayan', NULL, NULL),
(43322, 2982, 'Mantrijeron', NULL, NULL),
(43323, 2983, 'Kadipaten', NULL, NULL),
(43324, 2983, 'Patehan', NULL, NULL),
(43325, 2983, 'Panembahan', NULL, NULL),
(43326, 2984, 'Wirogunan', NULL, NULL),
(43327, 2984, 'Brontokusuman', NULL, NULL),
(43328, 2984, 'Keparakan', NULL, NULL),
(43329, 2985, 'Warungboto', NULL, NULL),
(43330, 2985, 'Mujamuju', NULL, NULL),
(43331, 2985, 'Pandeyan', NULL, NULL),
(43332, 2985, 'Sorowajan', NULL, NULL),
(43333, 2986, 'Purbayan', NULL, NULL),
(43334, 2986, 'Singosaren', NULL, NULL),
(43335, 2986, 'Prenggan', NULL, NULL),
(43336, 2987, 'Demangan', NULL, NULL),
(43337, 2987, 'Baciro', NULL, NULL),
(43338, 2987, 'Klumpit', NULL, NULL),
(43339, 2988, 'Tegalpanggung', NULL, NULL),
(43340, 2988, 'Bausasran', NULL, NULL),
(43341, 2988, 'Kricak', NULL, NULL),
(43342, 2989, 'Gunungketur', NULL, NULL),
(43343, 2989, 'Purwokinanti', NULL, NULL),
(43344, 2990, 'Ngupasan', NULL, NULL),
(43345, 2990, 'Gondomanan', NULL, NULL),
(43346, 2990, 'Prawirodirjan', NULL, NULL),
(43347, 2991, 'Ngampilan', NULL, NULL),
(43348, 2991, 'Notoprajan', NULL, NULL),
(43349, 2991, 'Patangpuluhan', NULL, NULL),
(43350, 2992, 'Wirobrajan', NULL, NULL),
(43351, 2992, 'Gedongkiwo', NULL, NULL),
(43352, 2992, 'Bener', NULL, NULL),
(43353, 2993, 'Sosromenduran', NULL, NULL),
(43354, 2993, 'Pringgokusuman', NULL, NULL),
(43355, 2994, 'Bumijo', NULL, NULL),
(43356, 2994, 'Cokrodiningratan', NULL, NULL),
(43357, 2994, 'Jetis', NULL, NULL),
(43358, 2995, 'Tegalrejo', NULL, NULL),
(43359, 2995, 'Kembang', NULL, NULL),
(43360, 2995, 'Karangwaru', NULL, NULL),
(43361, 2995, 'Suryatmajan', NULL, NULL),
(43362, 2996, 'Giricahyo', NULL, NULL),
(43363, 2996, 'Panggang', NULL, NULL),
(43364, 2996, 'Giriwungu', NULL, NULL),
(43365, 2996, 'Girisuko', NULL, NULL),
(43366, 2996, 'Girisekar', NULL, NULL),
(43367, 2996, 'Giritirto', NULL, NULL),
(43368, 2996, 'Girijati', NULL, NULL),
(43369, 2996, 'Giripurwo', NULL, NULL),
(43370, 2997, 'Girikarto', NULL, NULL),
(43371, 2997, 'Purwosari', NULL, NULL),
(43372, 2997, 'Girisari', NULL, NULL),
(43373, 2997, 'Giriharjo', NULL, NULL),
(43374, 2997, 'Giritunggal', NULL, NULL),
(43375, 2997, 'Giriwarno', NULL, NULL),
(43376, 2997, 'Girikaryo', NULL, NULL),
(43377, 2997, 'Giriloyo', NULL, NULL),
(43378, 2998, 'Giritontro', NULL, NULL),
(43379, 2998, 'Paliyan', NULL, NULL),
(43380, 2998, 'Girisato', NULL, NULL),
(43381, 2998, 'Giriwetan', NULL, NULL),
(43382, 2998, 'Girimulyo', NULL, NULL),
(43383, 2998, 'Girikaton', NULL, NULL),
(43384, 2998, 'Giriwinangun', NULL, NULL),
(43385, 2998, 'Girisiddo', NULL, NULL),
(43386, 2999, 'Saptosari', NULL, NULL),
(43387, 2999, 'Kanigoro', NULL, NULL),
(43388, 2999, 'Kedungpoh', NULL, NULL),
(43389, 2999, 'Nglindur', NULL, NULL),
(43390, 2999, 'Planjan', NULL, NULL),
(43391, 2999, 'Songbanyu', NULL, NULL),
(43392, 2999, 'Jetis', NULL, NULL),
(43393, 2999, 'Kepek', NULL, NULL),
(43394, 3000, 'Girisuci', NULL, NULL),
(43395, 3000, 'Pucung', NULL, NULL),
(43396, 3000, 'Girijaya', NULL, NULL),
(43397, 3000, 'Girimulya', NULL, NULL),
(43398, 3000, 'Giriwarno', NULL, NULL),
(43399, 3000, 'Giripeni', NULL, NULL),
(43400, 3000, 'Girikarya', NULL, NULL),
(43401, 3000, 'Girihardjo', NULL, NULL),
(43402, 3001, 'Tepus', NULL, NULL),
(43403, 3001, 'Purwodadi', NULL, NULL),
(43404, 3001, 'Sidoharjo', NULL, NULL),
(43405, 3001, 'Tepus', NULL, NULL),
(43406, 3001, 'Giripanggung', NULL, NULL),
(43407, 3001, 'Giritirta', NULL, NULL),
(43408, 3001, 'Giriwedoro', NULL, NULL),
(43409, 3001, 'Giripurwo', NULL, NULL),
(43410, 3002, 'Tanjungsari', NULL, NULL),
(43411, 3002, 'Banjaroyo', NULL, NULL),
(43412, 3002, 'Kemadang', NULL, NULL),
(43413, 3002, 'Ngestirejo', NULL, NULL),
(43414, 3002, 'Kemiri', NULL, NULL),
(43415, 3002, 'Tanjungsari', NULL, NULL),
(43416, 3002, 'Giripanggung', NULL, NULL),
(43417, 3002, 'Girikarto', NULL, NULL),
(43418, 3003, 'Rongkop', NULL, NULL),
(43419, 3003, 'Giriasih', NULL, NULL),
(43420, 3003, 'Giriwetan', NULL, NULL),
(43421, 3003, 'Girikarto', NULL, NULL),
(43422, 3003, 'Girijaya', NULL, NULL),
(43423, 3003, 'Giriloyo', NULL, NULL),
(43424, 3003, 'Giripurwo', NULL, NULL),
(43425, 3003, 'Giriwungu', NULL, NULL),
(43426, 3004, 'Semin', NULL, NULL),
(43427, 3004, 'Candirejo', NULL, NULL),
(43428, 3004, 'Kemejing', NULL, NULL),
(43429, 3004, 'Pundungsari', NULL, NULL),
(43430, 3004, 'Rejosari', NULL, NULL),
(43431, 3004, 'Tambak', NULL, NULL),
(43432, 3004, 'Sumberrejo', NULL, NULL),
(43433, 3004, 'Karangmojo', NULL, NULL),
(43434, 3005, 'Ngawen', NULL, NULL),
(43435, 3005, 'Beji', NULL, NULL),
(43436, 3005, 'Banyusoco', NULL, NULL),
(43437, 3005, 'Wates', NULL, NULL),
(43438, 3005, 'Sampang', NULL, NULL),
(43439, 3005, 'Katongan', NULL, NULL),
(43440, 3005, 'Tancep', NULL, NULL),
(43441, 3005, 'Ngloro', NULL, NULL),
(43442, 3006, 'Playen', NULL, NULL),
(43443, 3006, 'Banyusoco', NULL, NULL),
(43444, 3006, 'Banaran', NULL, NULL),
(43445, 3006, 'Ngleri', NULL, NULL),
(43446, 3006, 'Gading', NULL, NULL),
(43447, 3006, 'Plembono', NULL, NULL),
(43448, 3006, 'Ngawu', NULL, NULL),
(43449, 3006, 'Logandeng', NULL, NULL),
(43450, 3007, 'Patuk', NULL, NULL),
(43451, 3007, 'Bunder', NULL, NULL),
(43452, 3007, 'Ngoro-oro', NULL, NULL),
(43453, 3007, 'Nglanggeran', NULL, NULL),
(43454, 3007, 'Pengkok', NULL, NULL),
(43455, 3007, 'Salam', NULL, NULL),
(43456, 3007, 'Terbah', NULL, NULL),
(43457, 3007, 'Putat', NULL, NULL),
(43458, 3008, 'Nglipar', NULL, NULL),
(43459, 3008, 'Kedungkeris', NULL, NULL),
(43460, 3008, 'Pilangrejo', NULL, NULL),
(43461, 3008, 'Nglipar', NULL, NULL),
(43462, 3008, 'Pengkol', NULL, NULL),
(43463, 3008, 'Kedungpoh', NULL, NULL),
(43464, 3008, 'Katongan', NULL, NULL),
(43465, 3008, 'Natah', NULL, NULL),
(43466, 3009, 'Gedangsari', NULL, NULL),
(43467, 3009, 'Hargomulyo', NULL, NULL),
(43468, 3009, 'Sampang', NULL, NULL),
(43469, 3009, 'Mertelu', NULL, NULL),
(43470, 3009, 'Tegalrejo', NULL, NULL),
(43471, 3009, 'Watugajah', NULL, NULL),
(43472, 3009, 'Giripurwo', NULL, NULL),
(43473, 3009, 'Giriwetan', NULL, NULL),
(43474, 3010, 'Girisubo', NULL, NULL),
(43475, 3010, 'Jepitu', NULL, NULL),
(43476, 3010, 'Karangawen', NULL, NULL),
(43477, 3010, 'Nglindur', NULL, NULL),
(43478, 3010, 'Tileng', NULL, NULL),
(43479, 3010, 'Songbanyu', NULL, NULL),
(43480, 3010, 'Girikarto', NULL, NULL),
(43481, 3010, 'Giriwetan', NULL, NULL),
(43482, 3011, 'Saptosari', NULL, NULL),
(43483, 3011, 'Krambilsawit', NULL, NULL),
(43484, 3011, 'Panggul', NULL, NULL),
(43485, 3011, 'Pringombo', NULL, NULL),
(43486, 3011, 'Rejosari', NULL, NULL),
(43487, 3011, 'Jetis', NULL, NULL),
(43488, 3011, 'Kanigoro', NULL, NULL),
(43489, 3011, 'Planjan', NULL, NULL),
(43490, 3012, 'Karangmojo', NULL, NULL),
(43491, 3012, 'Bejiharjo', NULL, NULL),
(43492, 3012, 'Jatiayu', NULL, NULL),
(43493, 3012, 'Karangrejek', NULL, NULL),
(43494, 3012, 'Ngawis', NULL, NULL),
(43495, 3012, 'Wiladeg', NULL, NULL),
(43496, 3012, 'Kelor', NULL, NULL),
(43497, 3012, 'Bendungan', NULL, NULL),
(43498, 3013, 'Wonosari', NULL, NULL),
(43499, 3013, 'Siyono', NULL, NULL),
(43500, 3013, 'Karangtengah', NULL, NULL),
(43501, 3013, 'Kepek', NULL, NULL),
(43502, 3013, 'Siraman', NULL, NULL),
(43503, 3013, 'Wuni', NULL, NULL),
(43504, 3013, 'Pulutan', NULL, NULL),
(43505, 3013, 'Mulo', NULL, NULL),
(43506, 3014, 'Palihan', NULL, NULL),
(43507, 3014, 'Temon', NULL, NULL),
(43508, 3014, 'Sindutan', NULL, NULL),
(43509, 3014, 'Kaligondang', NULL, NULL),
(43510, 3014, 'Karangwuluh', NULL, NULL),
(43511, 3014, 'Kebonrejo', NULL, NULL),
(43512, 3014, 'Janten', NULL, NULL),
(43513, 3014, 'Plumbon', NULL, NULL),
(43514, 3015, 'Margosari', NULL, NULL),
(43515, 3015, 'Wates', NULL, NULL),
(43516, 3015, 'Triharjo', NULL, NULL),
(43517, 3015, 'Sogan', NULL, NULL),
(43518, 3015, 'Giripeni', NULL, NULL),
(43519, 3015, 'Karangwuni', NULL, NULL),
(43520, 3015, 'Nomporejo', NULL, NULL),
(43521, 3015, 'Bendungan', NULL, NULL),
(43522, 3016, 'Bugel', NULL, NULL),
(43523, 3016, 'Panjatan', NULL, NULL),
(43524, 3016, 'Cerme', NULL, NULL),
(43525, 3016, 'Krembangan', NULL, NULL),
(43526, 3016, 'Gotakan', NULL, NULL),
(43527, 3016, 'Depokan', NULL, NULL),
(43528, 3016, 'Kanoman', NULL, NULL),
(43529, 3016, 'Pleret', NULL, NULL),
(43530, 3017, 'Galur', NULL, NULL),
(43531, 3017, 'Brosot', NULL, NULL),
(43532, 3017, 'Kranggan', NULL, NULL),
(43533, 3017, 'Pandowan', NULL, NULL),
(43534, 3017, 'Karangsewu', NULL, NULL),
(43535, 3017, 'Ngentakrejo', NULL, NULL),
(43536, 3017, 'Tawangrejo', NULL, NULL),
(43537, 3017, 'Banjararum', NULL, NULL),
(43538, 3018, 'Lendah', NULL, NULL),
(43539, 3018, 'Wahyuharjo', NULL, NULL),
(43540, 3018, 'Bumirejo', NULL, NULL),
(43541, 3018, 'Jatirejo', NULL, NULL),
(43542, 3018, 'Sidorejo', NULL, NULL),
(43543, 3018, 'Gulurejo', NULL, NULL),
(43544, 3018, 'Banguncipto', NULL, NULL),
(43545, 3018, 'Ngabeyan', NULL, NULL),
(43546, 3019, 'Sentolo', NULL, NULL),
(43547, 3019, 'Sukoreno', NULL, NULL),
(43548, 3019, 'Kembang', NULL, NULL),
(43549, 3019, 'Banguncipto', NULL, NULL),
(43550, 3019, 'Salamrejo', NULL, NULL),
(43551, 3019, 'Kaliagung', NULL, NULL),
(43552, 3019, 'Demangrejo', NULL, NULL),
(43553, 3019, 'Srikandi', NULL, NULL),
(43554, 3020, 'Pengasih', NULL, NULL),
(43555, 3020, 'Sidomulyo', NULL, NULL),
(43556, 3020, 'Sendangmulyo', NULL, NULL),
(43557, 3020, 'Karangtengah', NULL, NULL),
(43558, 3020, 'Margosari', NULL, NULL),
(43559, 3020, 'Kedungsari', NULL, NULL),
(43560, 3020, 'Pengasihrejo', NULL, NULL),
(43561, 3020, 'Tawangsari', NULL, NULL),
(43562, 3021, 'Kokap', NULL, NULL),
(43563, 3021, 'Hargosari', NULL, NULL),
(43564, 3021, 'Kalirejo', NULL, NULL),
(43565, 3021, 'Banjarsari', NULL, NULL),
(43566, 3021, 'Triharjo', NULL, NULL),
(43567, 3021, 'Sendangrejo', NULL, NULL),
(43568, 3021, 'Giripurwo', NULL, NULL),
(43569, 3021, 'Madigondo', NULL, NULL),
(43570, 3022, 'Girimulyo', NULL, NULL),
(43571, 3022, 'Purwosari', NULL, NULL),
(43572, 3022, 'Gerbosari', NULL, NULL),
(43573, 3022, 'Jatisarono', NULL, NULL),
(43574, 3022, 'Ngargosari', NULL, NULL),
(43575, 3022, 'Pagerharjo', NULL, NULL),
(43576, 3022, 'Kembangrejo', NULL, NULL),
(43577, 3022, 'Sidoharjo', NULL, NULL),
(43578, 3023, 'Nanggulan', NULL, NULL),
(43579, 3023, 'Jatiwangsan', NULL, NULL),
(43580, 3023, 'Donorejo', NULL, NULL),
(43581, 3023, 'Kembangmulyo', NULL, NULL),
(43582, 3023, 'Sukoharjo', NULL, NULL),
(43583, 3023, 'Wonosidi', NULL, NULL),
(43584, 3023, 'Banyuroto', NULL, NULL),
(43585, 3023, 'Tanjungsari', NULL, NULL),
(43586, 3024, 'Kalibawang', NULL, NULL),
(43587, 3024, 'Banjaroyo', NULL, NULL),
(43588, 3024, 'Banjarasri', NULL, NULL),
(43589, 3024, 'Banjararum', NULL, NULL),
(43590, 3024, 'Banjarsari', NULL, NULL),
(43591, 3024, 'Tegalsari', NULL, NULL),
(43592, 3024, 'Sidomulyo', NULL, NULL),
(43593, 3025, 'Samigaluh', NULL, NULL),
(43594, 3025, 'Purwoharjo', NULL, NULL),
(43595, 3025, 'Sidoharjo', NULL, NULL),
(43596, 3025, 'Ngargosari', NULL, NULL),
(43597, 3025, 'Kebonharjo', NULL, NULL),
(43598, 3025, 'Tawangsari', NULL, NULL),
(43599, 3025, 'Sukosari', NULL, NULL),
(43600, 3025, 'Banjarharjo', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi_pembelian_barangs`
--

CREATE TABLE `detail_transaksi_pembelian_barangs` (
  `ID_DETAIL_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi_penitip_barangs`
--

CREATE TABLE `detail_transaksi_penitip_barangs` (
  `ID_DETAIL_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL,
  `ID_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `NAMA_BARANG` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diskusis`
--

CREATE TABLE `diskusis` (
  `ID_DISKUSI` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `PERTANYAAN` varchar(1000) NOT NULL,
  `CREATE_AT` date NOT NULL,
  `JAWABAN` text DEFAULT NULL,
  `ID_PEGAWAI` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diskusi_pegawais`
--

CREATE TABLE `diskusi_pegawais` (
  `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL,
  `ID_DISKUSI` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jabatans`
--

CREATE TABLE `jabatans` (
  `ID_JABATAN` bigint(20) UNSIGNED NOT NULL,
  `NAMA_JABATAN` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatans`
--

INSERT INTO `jabatans` (`ID_JABATAN`, `NAMA_JABATAN`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, NULL),
(2, 'Owner', NULL, NULL),
(3, 'Pegawai Gudang', NULL, NULL),
(4, 'CS', NULL, NULL),
(5, 'Hunter', NULL, NULL),
(6, 'Kurir', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kabupatens`
--

CREATE TABLE `kabupatens` (
  `id_kabupaten_kota` bigint(20) UNSIGNED NOT NULL,
  `id_provinsi` bigint(20) UNSIGNED NOT NULL,
  `nama_kabupaten_kota` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kabupatens`
--

INSERT INTO `kabupatens` (`id_kabupaten_kota`, `id_provinsi`, `nama_kabupaten_kota`, `created_at`, `updated_at`) VALUES
(210, 14, 'SLEMAN', NULL, NULL),
(211, 14, 'BANTUL', NULL, NULL),
(212, 14, 'YOGYAKARTA', NULL, NULL),
(213, 14, 'GUNUNG KIDUL', NULL, NULL),
(214, 14, 'KULON PROGO', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategoribarangs`
--

CREATE TABLE `kategoribarangs` (
  `ID_KATEGORI` bigint(20) UNSIGNED NOT NULL,
  `NAMA_KATEGORI` varchar(255) NOT NULL,
  `JML_BARANG` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategoribarangs`
--

INSERT INTO `kategoribarangs` (`ID_KATEGORI`, `NAMA_KATEGORI`, `JML_BARANG`) VALUES
(1, 'Elektronik & Gadget', 0),
(2, 'Pakaian & Aksesori', 0),
(3, 'Perabotan Rumah Tangga', 0),
(4, 'Buku, Alat Tulis, & Peralatan Sekolah', 0),
(5, 'Hobi, Mainan, & Koleksi', 0),
(6, 'Perlengkapan Bayi & Anak', 0),
(7, 'Otomotif & Aksesori', 0),
(8, 'Perlengkapan Taman & Outdoor', 0),
(9, 'Peralatan Kantor & Industri', 0),
(10, 'Kosmetik & Perawatan Diri', 0);

-- --------------------------------------------------------

--
-- Table structure for table `kecamatans`
--

CREATE TABLE `kecamatans` (
  `id_kecamatan` bigint(20) UNSIGNED NOT NULL,
  `id_kabupaten_kota` bigint(20) UNSIGNED NOT NULL,
  `nama_kecamatan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kecamatans`
--

INSERT INTO `kecamatans` (`id_kecamatan`, `id_kabupaten_kota`, `nama_kecamatan`, `created_at`, `updated_at`) VALUES
(2948, 210, 'Moyudan', NULL, NULL),
(2949, 210, 'Minggir', NULL, NULL),
(2950, 210, 'Seyegan', NULL, NULL),
(2951, 210, 'Godean', NULL, NULL),
(2952, 210, 'Gamping', NULL, NULL),
(2953, 210, 'Mlati', NULL, NULL),
(2954, 210, 'Depok', NULL, NULL),
(2955, 210, 'Berbah', NULL, NULL),
(2956, 210, 'Prambanan', NULL, NULL),
(2957, 210, 'Kalasan', NULL, NULL),
(2958, 210, 'Ngemplak', NULL, NULL),
(2959, 210, 'Ngaglik', NULL, NULL),
(2960, 210, 'Sleman', NULL, NULL),
(2961, 210, 'Tempel', NULL, NULL),
(2962, 210, 'Turi', NULL, NULL),
(2963, 210, 'Pakem', NULL, NULL),
(2964, 210, 'Cangkringan', NULL, NULL),
(2965, 211, 'Srandakan', NULL, NULL),
(2966, 211, 'Sanden', NULL, NULL),
(2967, 211, 'Kretek', NULL, NULL),
(2968, 211, 'Pundong', NULL, NULL),
(2969, 211, 'Bambang Lipuro', NULL, NULL),
(2970, 211, 'Pandak', NULL, NULL),
(2971, 211, 'Bantul', NULL, NULL),
(2972, 211, 'Jetis', NULL, NULL),
(2973, 211, 'Imogiri', NULL, NULL),
(2974, 211, 'Dlingo', NULL, NULL),
(2975, 211, 'Pleret', NULL, NULL),
(2976, 211, 'Piyungan', NULL, NULL),
(2977, 211, 'Banguntapan', NULL, NULL),
(2978, 211, 'Sewon', NULL, NULL),
(2979, 211, 'Kasihan', NULL, NULL),
(2980, 211, 'Pajangan', NULL, NULL),
(2981, 211, 'Sedayu', NULL, NULL),
(2982, 212, 'Mantrijeron', NULL, NULL),
(2983, 212, 'Kraton', NULL, NULL),
(2984, 212, 'Mergangsan', NULL, NULL),
(2985, 212, 'Umbulharjo', NULL, NULL),
(2986, 212, 'Kotagede', NULL, NULL),
(2987, 212, 'Gondokusuman', NULL, NULL),
(2988, 212, 'Danurejan', NULL, NULL),
(2989, 212, 'Pakualaman', NULL, NULL),
(2990, 212, 'Gondomanan', NULL, NULL),
(2991, 212, 'Ngampilan', NULL, NULL),
(2992, 212, 'Wirobrajan', NULL, NULL),
(2993, 212, 'Gedong Tengen', NULL, NULL),
(2994, 212, 'Jetis', NULL, NULL),
(2995, 212, 'Tegalrejo', NULL, NULL),
(2996, 213, 'Panggang', NULL, NULL),
(2997, 213, 'Purwosari', NULL, NULL),
(2998, 213, 'Paliyan', NULL, NULL),
(2999, 213, 'Sapto Sari', NULL, NULL),
(3000, 213, 'Girisuci', NULL, NULL),
(3001, 213, 'Tepus', NULL, NULL),
(3002, 213, 'Tanjungsari', NULL, NULL),
(3003, 213, 'Rongkop', NULL, NULL),
(3004, 213, 'Semin', NULL, NULL),
(3005, 213, 'Ngawen', NULL, NULL),
(3006, 213, 'Playen', NULL, NULL),
(3007, 213, 'Patuk', NULL, NULL),
(3008, 213, 'Nglipar', NULL, NULL),
(3009, 213, 'Gedang Sari', NULL, NULL),
(3010, 213, 'Girisubo', NULL, NULL),
(3011, 213, 'Saptosari', NULL, NULL),
(3012, 213, 'Karangmojo', NULL, NULL),
(3013, 213, 'Wonosari', NULL, NULL),
(3014, 214, 'Temon', NULL, NULL),
(3015, 214, 'Wates', NULL, NULL),
(3016, 214, 'Panjatan', NULL, NULL),
(3017, 214, 'Galur', NULL, NULL),
(3018, 214, 'Lendah', NULL, NULL),
(3019, 214, 'Sentolo', NULL, NULL),
(3020, 214, 'Pengasih', NULL, NULL),
(3021, 214, 'Kokap', NULL, NULL),
(3022, 214, 'Girimulyo', NULL, NULL),
(3023, 214, 'Nanggulan', NULL, NULL),
(3024, 214, 'Kalibawang', NULL, NULL),
(3025, 214, 'Samigaluh', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `klaim_merchandises`
--

CREATE TABLE `klaim_merchandises` (
  `ID_KLAIM` bigint(20) UNSIGNED NOT NULL,
  `ID_MERCHANDISE` bigint(20) UNSIGNED NOT NULL,
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `TGL_KLAIM` date DEFAULT NULL,
  `TGL_PENGAMBILAN` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komisis`
--

CREATE TABLE `komisis` (
  `ID_KOMISI` bigint(20) UNSIGNED NOT NULL,
  `JENIS_KOMISI` enum('Hunter','Penitip','Reusemart') NOT NULL,
  `ID_PENITIP` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_PEGAWAI` bigint(20) UNSIGNED DEFAULT NULL,
  `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `NOMINAL_KOMISI` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kurir_transaksi_pembelians`
--

CREATE TABLE `kurir_transaksi_pembelians` (
  `ID_KURIR_TRANSAKSI` bigint(20) UNSIGNED NOT NULL,
  `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL,
  `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `TGL_KONFIRMASI` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchandises`
--

CREATE TABLE `merchandises` (
  `ID_MERCHANDISE` bigint(20) UNSIGNED NOT NULL,
  `NAMA_MERCHANDISE` varchar(255) NOT NULL,
  `POIN_DIBUTUHKAN` int(11) NOT NULL DEFAULT 0,
  `JUMLAH` int(11) NOT NULL DEFAULT 0,
  `GAMBAR` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '0001_01_01_000003_create_notifications_table', 1),
(4, '2025_05_06_125604_create_penitips_table', 1),
(5, '2025_05_06_135023_create_jabatans_table', 1),
(6, '2025_05_07_045214_create_kategoribarangs_table', 1),
(7, '2025_05_07_045227_create_merchandises_table', 1),
(8, '2025_05_07_045239_create_organisasis_table', 1),
(9, '2025_05_07_045247_create_pegawais_table', 1),
(10, '2025_05_07_045304_create_pembelis_table', 1),
(11, '2025_05_07_045312_create_barangs_table', 1),
(12, '2025_05_07_045340_create_alamats_table', 1),
(13, '2025_05_07_045347_create_badges_table', 1),
(14, '2025_05_07_045353_create_transaksi_penitipan_barangs_table', 1),
(15, '2025_05_07_045403_create_transaksi_pembelian_barangs_table', 1),
(16, '2025_05_07_045413_create_detail_transaksi_penitip_barangs_table', 1),
(17, '2025_05_07_045415_create_detail_transaksi_pembelian_barangs_table', 1),
(18, '2025_05_07_045417_create_diskusis_table', 1),
(19, '2025_05_07_045419_create_requests_table', 1),
(20, '2025_05_07_045420_create_klaim_merchandises_table', 1),
(21, '2025_05_07_045422_create_komisis_table', 1),
(22, '2025_05_07_045432_create_diskusi_pegawais_table', 1),
(23, '2025_05_07_045433_create_pegawai_transaksi_pembelians_table', 1),
(24, '2025_05_07_045435_create_pegawai_transaksi_penitipans_table', 1),
(25, '2025_05_07_045439_create_transaksi_donasis_table', 1),
(26, '2025_05_07_204118_create_provinsis_table', 1),
(27, '2025_05_07_204124_create_kabupatens_table', 1),
(28, '2025_05_07_204128_create_kecamatans_table', 1),
(29, '2025_05_07_204132_create_desa_kelurahans_table', 1),
(30, '2025_05_11_012640_create_personal_access_tokens_table', 1),
(31, '2025_05_29_155559_create_sessions_table', 1),
(32, '2025_06_02_003105_create_cart_items_table', 1),
(33, '2025_06_08_014015_create_kurir_transaksi_pembelians_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organisasis`
--

CREATE TABLE `organisasis` (
  `ID_ORGANISASI` bigint(20) UNSIGNED NOT NULL,
  `NAMA_ORGANISASI` varchar(255) NOT NULL,
  `PROFILE_ORGANISASI` varchar(255) DEFAULT NULL,
  `ALAMAT_ORGANISASI` varchar(255) NOT NULL,
  `NO_TELP_ORGANISASI` varchar(25) NOT NULL,
  `EMAIL_ORGANISASI` varchar(25) NOT NULL,
  `PASSWORD_ORGANISASI` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pegawais`
--

CREATE TABLE `pegawais` (
  `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL,
  `ID_JABATAN` bigint(20) UNSIGNED NOT NULL,
  `NAMA_PEGAWAI` varchar(255) NOT NULL,
  `PROFILE_PEGAWAI` varchar(255) DEFAULT NULL,
  `NO_TELP_PEGAWAI` varchar(25) NOT NULL,
  `EMAIL_PEGAWAI` varchar(255) NOT NULL,
  `PASSWORD_PEGAWAI` varchar(255) NOT NULL,
  `KOMISI_PEGAWAI` double NOT NULL DEFAULT 0,
  `TGL_LAHIR_PEGAWAI` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pegawais`
--

INSERT INTO `pegawais` (`ID_PEGAWAI`, `ID_JABATAN`, `NAMA_PEGAWAI`, `PROFILE_PEGAWAI`, `NO_TELP_PEGAWAI`, `EMAIL_PEGAWAI`, `PASSWORD_PEGAWAI`, `KOMISI_PEGAWAI`, `TGL_LAHIR_PEGAWAI`, `created_at`, `updated_at`) VALUES
(1, 2, 'Owner ReuseMart', NULL, '081234567890', 'owner@reusermart.test', '$2y$12$JquSUpAEliMDhRXKcMOjIOyk.H1kkCgRF8Hhv9qlHutf3/jr1V4TS', 0, '1995-02-22', '2025-06-23 06:44:56', '2025-06-23 06:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai_transaksi_pembelians`
--

CREATE TABLE `pegawai_transaksi_pembelians` (
  `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL,
  `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pegawai_transaksi_penitipans`
--

CREATE TABLE `pegawai_transaksi_penitipans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ID_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL,
  `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelis`
--

CREATE TABLE `pembelis` (
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `NAMA_PEMBELI` varchar(255) NOT NULL,
  `PROFILE_PEMBELI` varchar(255) DEFAULT NULL,
  `TGL_LAHIR_PEMBELI` date NOT NULL,
  `NO_TELP_PEMBELI` varchar(25) NOT NULL,
  `EMAIL_PEMBELI` varchar(255) NOT NULL,
  `PASSWORD_PEMBELI` varchar(255) NOT NULL,
  `POINT_LOYALITAS_PEMBELI` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penitips`
--

CREATE TABLE `penitips` (
  `ID_PENITIP` bigint(20) UNSIGNED NOT NULL,
  `NAMA_PENITIP` varchar(255) NOT NULL,
  `PROFILE_PENITIP` varchar(255) DEFAULT NULL,
  `NO_KTP` varchar(16) NOT NULL,
  `ALAMAT_PENITIP` varchar(255) NOT NULL,
  `TGL_LAHIR_PENITIP` date NOT NULL,
  `NO_TELP_PENITIP` varchar(25) NOT NULL,
  `EMAIL_PENITIP` varchar(25) NOT NULL,
  `PASSWORD_PENITIP` varchar(255) NOT NULL,
  `FOTO_NIK` varchar(255) DEFAULT NULL,
  `SALDO_PENITIP` double NOT NULL DEFAULT 0,
  `POINT_LOYALITAS_PENITIP` int(11) NOT NULL DEFAULT 0,
  `RATING_PENITIP` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provinsis`
--

CREATE TABLE `provinsis` (
  `id_provinsi` bigint(20) UNSIGNED NOT NULL,
  `nama_provinsi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinsis`
--

INSERT INTO `provinsis` (`id_provinsi`, `nama_provinsi`) VALUES
(14, 'DAERAH ISTIMEWA YOGYAKARTA');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `ID_REQUEST` bigint(20) UNSIGNED NOT NULL,
  `ID_ORGANISASI` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED DEFAULT NULL,
  `NAMA_BARANG_REQUEST` varchar(255) NOT NULL,
  `CREATE_AT` date DEFAULT NULL,
  `DESKRIPSI_REQUEST` varchar(255) DEFAULT NULL,
  `STATUS_REQUEST` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_donasis`
--

CREATE TABLE `transaksi_donasis` (
  `ID_TRANSAKSI_DONASI` bigint(20) UNSIGNED NOT NULL,
  `ID_ORGANISASI` bigint(20) UNSIGNED NOT NULL,
  `ID_REQUEST` bigint(20) UNSIGNED NOT NULL,
  `TGL_DONASI` date DEFAULT NULL,
  `PENERIMA` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pembelian_barangs`
--

CREATE TABLE `transaksi_pembelian_barangs` (
  `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL,
  `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL,
  `ID_BARANG` bigint(20) UNSIGNED NOT NULL,
  `BUKTI_TRANSFER` varchar(255) DEFAULT NULL,
  `TGL_AMBIL_KIRIM` date DEFAULT NULL,
  `TGL_LUNAS_PEMBELIAN` date DEFAULT NULL,
  `TGL_PESAN_PEMBELIAN` date NOT NULL,
  `TOT_HARGA_PEMBELIAN` double NOT NULL,
  `STATUS_PEMBAYARAN` varchar(255) NOT NULL,
  `DELIVERY_METHOD` varchar(255) NOT NULL,
  `ONGKOS_KIRIM` double NOT NULL,
  `POIN_DIDAPAT` int(11) NOT NULL DEFAULT 0,
  `POIN_POTONGAN` int(11) DEFAULT NULL,
  `ID_ALAMAT_PENGIRIMAN` bigint(20) UNSIGNED DEFAULT NULL,
  `STATUS_BUKTI_TRANSFER` varchar(255) NOT NULL,
  `STATUS_TRANSAKSI` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_penitipan_barangs`
--

CREATE TABLE `transaksi_penitipan_barangs` (
  `ID_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL,
  `ID_PENITIP` bigint(20) UNSIGNED NOT NULL,
  `TGL_MASUK_TITIPAN` date NOT NULL,
  `TGL_KELUAR_TITIPAN` date NOT NULL,
  `NO_NOTA_TRANSAKSI_TITIPAN` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alamats`
--
ALTER TABLE `alamats`
  ADD PRIMARY KEY (`ID_ALAMAT`),
  ADD KEY `alamats_id_pembeli_foreign` (`ID_PEMBELI`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`ID_BADGE`),
  ADD KEY `badges_id_penitip_foreign` (`ID_PENITIP`);

--
-- Indexes for table `barangs`
--
ALTER TABLE `barangs`
  ADD PRIMARY KEY (`ID_BARANG`),
  ADD KEY `barangs_id_kategori_foreign` (`ID_KATEGORI`),
  ADD KEY `barangs_id_penitip_foreign` (`ID_PENITIP`),
  ADD KEY `barangs_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `barangs_id_organisasi_foreign` (`ID_ORGANISASI`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`ID_CART`),
  ADD UNIQUE KEY `cart_items_id_pembeli_id_barang_unique` (`ID_PEMBELI`,`ID_BARANG`),
  ADD KEY `cart_items_id_barang_foreign` (`ID_BARANG`);

--
-- Indexes for table `desa_kelurahans`
--
ALTER TABLE `desa_kelurahans`
  ADD PRIMARY KEY (`id_desa_kelurahan`),
  ADD KEY `desa_kelurahans_id_kecamatan_foreign` (`id_kecamatan`);

--
-- Indexes for table `detail_transaksi_pembelian_barangs`
--
ALTER TABLE `detail_transaksi_pembelian_barangs`
  ADD PRIMARY KEY (`ID_DETAIL_TRANSAKSI_PEMBELIAN`);

--
-- Indexes for table `detail_transaksi_penitip_barangs`
--
ALTER TABLE `detail_transaksi_penitip_barangs`
  ADD PRIMARY KEY (`ID_DETAIL_TRANSAKSI_PENITIPAN`),
  ADD KEY `detail_transaksi_penitip_barangs_id_transaksi_penitipan_foreign` (`ID_TRANSAKSI_PENITIPAN`),
  ADD KEY `detail_transaksi_penitip_barangs_id_barang_foreign` (`ID_BARANG`);

--
-- Indexes for table `diskusis`
--
ALTER TABLE `diskusis`
  ADD PRIMARY KEY (`ID_DISKUSI`),
  ADD KEY `diskusis_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `diskusis_id_barang_foreign` (`ID_BARANG`),
  ADD KEY `diskusis_id_pembeli_foreign` (`ID_PEMBELI`);

--
-- Indexes for table `diskusi_pegawais`
--
ALTER TABLE `diskusi_pegawais`
  ADD KEY `diskusi_pegawais_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `diskusi_pegawais_id_diskusi_foreign` (`ID_DISKUSI`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jabatans`
--
ALTER TABLE `jabatans`
  ADD PRIMARY KEY (`ID_JABATAN`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kabupatens`
--
ALTER TABLE `kabupatens`
  ADD PRIMARY KEY (`id_kabupaten_kota`),
  ADD KEY `kabupatens_id_provinsi_foreign` (`id_provinsi`);

--
-- Indexes for table `kategoribarangs`
--
ALTER TABLE `kategoribarangs`
  ADD PRIMARY KEY (`ID_KATEGORI`);

--
-- Indexes for table `kecamatans`
--
ALTER TABLE `kecamatans`
  ADD PRIMARY KEY (`id_kecamatan`),
  ADD KEY `kecamatans_id_kabupaten_kota_foreign` (`id_kabupaten_kota`);

--
-- Indexes for table `klaim_merchandises`
--
ALTER TABLE `klaim_merchandises`
  ADD PRIMARY KEY (`ID_KLAIM`),
  ADD KEY `klaim_merchandises_id_merchandise_foreign` (`ID_MERCHANDISE`),
  ADD KEY `klaim_merchandises_id_pembeli_foreign` (`ID_PEMBELI`);

--
-- Indexes for table `komisis`
--
ALTER TABLE `komisis`
  ADD PRIMARY KEY (`ID_KOMISI`),
  ADD KEY `komisis_id_penitip_foreign` (`ID_PENITIP`),
  ADD KEY `komisis_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `komisis_id_transaksi_pembelian_foreign` (`ID_TRANSAKSI_PEMBELIAN`);

--
-- Indexes for table `kurir_transaksi_pembelians`
--
ALTER TABLE `kurir_transaksi_pembelians`
  ADD PRIMARY KEY (`ID_KURIR_TRANSAKSI`),
  ADD KEY `kurir_transaksi_pembelians_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `kurir_transaksi_pembelians_id_transaksi_pembelian_foreign` (`ID_TRANSAKSI_PEMBELIAN`);

--
-- Indexes for table `merchandises`
--
ALTER TABLE `merchandises`
  ADD PRIMARY KEY (`ID_MERCHANDISE`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `organisasis`
--
ALTER TABLE `organisasis`
  ADD PRIMARY KEY (`ID_ORGANISASI`),
  ADD UNIQUE KEY `organisasis_password_organisasi_unique` (`PASSWORD_ORGANISASI`);

--
-- Indexes for table `pegawais`
--
ALTER TABLE `pegawais`
  ADD PRIMARY KEY (`ID_PEGAWAI`),
  ADD UNIQUE KEY `pegawais_email_pegawai_unique` (`EMAIL_PEGAWAI`),
  ADD KEY `pegawais_id_jabatan_foreign` (`ID_JABATAN`);

--
-- Indexes for table `pegawai_transaksi_pembelians`
--
ALTER TABLE `pegawai_transaksi_pembelians`
  ADD KEY `pegawai_transaksi_pembelians_id_pegawai_foreign` (`ID_PEGAWAI`),
  ADD KEY `pegawai_transaksi_pembelians_id_transaksi_pembelian_foreign` (`ID_TRANSAKSI_PEMBELIAN`);

--
-- Indexes for table `pegawai_transaksi_penitipans`
--
ALTER TABLE `pegawai_transaksi_penitipans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_transaksi_penitipans_id_transaksi_penitipan_foreign` (`ID_TRANSAKSI_PENITIPAN`),
  ADD KEY `pegawai_transaksi_penitipans_id_pegawai_foreign` (`ID_PEGAWAI`);

--
-- Indexes for table `pembelis`
--
ALTER TABLE `pembelis`
  ADD PRIMARY KEY (`ID_PEMBELI`);

--
-- Indexes for table `penitips`
--
ALTER TABLE `penitips`
  ADD PRIMARY KEY (`ID_PENITIP`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `provinsis`
--
ALTER TABLE `provinsis`
  ADD PRIMARY KEY (`id_provinsi`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`ID_REQUEST`),
  ADD KEY `requests_id_organisasi_foreign` (`ID_ORGANISASI`),
  ADD KEY `requests_id_barang_foreign` (`ID_BARANG`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transaksi_donasis`
--
ALTER TABLE `transaksi_donasis`
  ADD PRIMARY KEY (`ID_TRANSAKSI_DONASI`),
  ADD KEY `transaksi_donasis_id_organisasi_foreign` (`ID_ORGANISASI`),
  ADD KEY `transaksi_donasis_id_request_foreign` (`ID_REQUEST`);

--
-- Indexes for table `transaksi_pembelian_barangs`
--
ALTER TABLE `transaksi_pembelian_barangs`
  ADD PRIMARY KEY (`ID_TRANSAKSI_PEMBELIAN`),
  ADD KEY `transaksi_pembelian_barangs_id_pembeli_foreign` (`ID_PEMBELI`),
  ADD KEY `transaksi_pembelian_barangs_id_barang_foreign` (`ID_BARANG`),
  ADD KEY `transaksi_pembelian_barangs_id_alamat_pengiriman_foreign` (`ID_ALAMAT_PENGIRIMAN`);

--
-- Indexes for table `transaksi_penitipan_barangs`
--
ALTER TABLE `transaksi_penitipan_barangs`
  ADD PRIMARY KEY (`ID_TRANSAKSI_PENITIPAN`),
  ADD KEY `transaksi_penitipan_barangs_id_penitip_foreign` (`ID_PENITIP`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alamats`
--
ALTER TABLE `alamats`
  MODIFY `ID_ALAMAT` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `ID_BADGE` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangs`
--
ALTER TABLE `barangs`
  MODIFY `ID_BARANG` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `ID_CART` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `desa_kelurahans`
--
ALTER TABLE `desa_kelurahans`
  MODIFY `id_desa_kelurahan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43601;

--
-- AUTO_INCREMENT for table `detail_transaksi_pembelian_barangs`
--
ALTER TABLE `detail_transaksi_pembelian_barangs`
  MODIFY `ID_DETAIL_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_transaksi_penitip_barangs`
--
ALTER TABLE `detail_transaksi_penitip_barangs`
  MODIFY `ID_DETAIL_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diskusis`
--
ALTER TABLE `diskusis`
  MODIFY `ID_DISKUSI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jabatans`
--
ALTER TABLE `jabatans`
  MODIFY `ID_JABATAN` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kabupatens`
--
ALTER TABLE `kabupatens`
  MODIFY `id_kabupaten_kota` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `kategoribarangs`
--
ALTER TABLE `kategoribarangs`
  MODIFY `ID_KATEGORI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kecamatans`
--
ALTER TABLE `kecamatans`
  MODIFY `id_kecamatan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3026;

--
-- AUTO_INCREMENT for table `klaim_merchandises`
--
ALTER TABLE `klaim_merchandises`
  MODIFY `ID_KLAIM` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `komisis`
--
ALTER TABLE `komisis`
  MODIFY `ID_KOMISI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kurir_transaksi_pembelians`
--
ALTER TABLE `kurir_transaksi_pembelians`
  MODIFY `ID_KURIR_TRANSAKSI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merchandises`
--
ALTER TABLE `merchandises`
  MODIFY `ID_MERCHANDISE` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `organisasis`
--
ALTER TABLE `organisasis`
  MODIFY `ID_ORGANISASI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pegawais`
--
ALTER TABLE `pegawais`
  MODIFY `ID_PEGAWAI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pegawai_transaksi_penitipans`
--
ALTER TABLE `pegawai_transaksi_penitipans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelis`
--
ALTER TABLE `pembelis`
  MODIFY `ID_PEMBELI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penitips`
--
ALTER TABLE `penitips`
  MODIFY `ID_PENITIP` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provinsis`
--
ALTER TABLE `provinsis`
  MODIFY `id_provinsi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `ID_REQUEST` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_donasis`
--
ALTER TABLE `transaksi_donasis`
  MODIFY `ID_TRANSAKSI_DONASI` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_pembelian_barangs`
--
ALTER TABLE `transaksi_pembelian_barangs`
  MODIFY `ID_TRANSAKSI_PEMBELIAN` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_penitipan_barangs`
--
ALTER TABLE `transaksi_penitipan_barangs`
  MODIFY `ID_TRANSAKSI_PENITIPAN` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamats`
--
ALTER TABLE `alamats`
  ADD CONSTRAINT `alamats_id_pembeli_foreign` FOREIGN KEY (`ID_PEMBELI`) REFERENCES `pembelis` (`ID_PEMBELI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `badges`
--
ALTER TABLE `badges`
  ADD CONSTRAINT `badges_id_penitip_foreign` FOREIGN KEY (`ID_PENITIP`) REFERENCES `penitips` (`ID_PENITIP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `barangs`
--
ALTER TABLE `barangs`
  ADD CONSTRAINT `barangs_id_kategori_foreign` FOREIGN KEY (`ID_KATEGORI`) REFERENCES `kategoribarangs` (`ID_KATEGORI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `barangs_id_organisasi_foreign` FOREIGN KEY (`ID_ORGANISASI`) REFERENCES `organisasis` (`ID_ORGANISASI`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `barangs_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `barangs_id_penitip_foreign` FOREIGN KEY (`ID_PENITIP`) REFERENCES `penitips` (`ID_PENITIP`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_id_barang_foreign` FOREIGN KEY (`ID_BARANG`) REFERENCES `barangs` (`ID_BARANG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_items_id_pembeli_foreign` FOREIGN KEY (`ID_PEMBELI`) REFERENCES `pembelis` (`ID_PEMBELI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `desa_kelurahans`
--
ALTER TABLE `desa_kelurahans`
  ADD CONSTRAINT `desa_kelurahans_id_kecamatan_foreign` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatans` (`id_kecamatan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_transaksi_penitip_barangs`
--
ALTER TABLE `detail_transaksi_penitip_barangs`
  ADD CONSTRAINT `detail_transaksi_penitip_barangs_id_barang_foreign` FOREIGN KEY (`ID_BARANG`) REFERENCES `barangs` (`ID_BARANG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_transaksi_penitip_barangs_id_transaksi_penitipan_foreign` FOREIGN KEY (`ID_TRANSAKSI_PENITIPAN`) REFERENCES `transaksi_penitipan_barangs` (`ID_TRANSAKSI_PENITIPAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `diskusis`
--
ALTER TABLE `diskusis`
  ADD CONSTRAINT `diskusis_id_barang_foreign` FOREIGN KEY (`ID_BARANG`) REFERENCES `barangs` (`ID_BARANG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diskusis_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`),
  ADD CONSTRAINT `diskusis_id_pembeli_foreign` FOREIGN KEY (`ID_PEMBELI`) REFERENCES `pembelis` (`ID_PEMBELI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `diskusi_pegawais`
--
ALTER TABLE `diskusi_pegawais`
  ADD CONSTRAINT `diskusi_pegawais_id_diskusi_foreign` FOREIGN KEY (`ID_DISKUSI`) REFERENCES `diskusis` (`ID_DISKUSI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diskusi_pegawais_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kabupatens`
--
ALTER TABLE `kabupatens`
  ADD CONSTRAINT `kabupatens_id_provinsi_foreign` FOREIGN KEY (`id_provinsi`) REFERENCES `provinsis` (`id_provinsi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kecamatans`
--
ALTER TABLE `kecamatans`
  ADD CONSTRAINT `kecamatans_id_kabupaten_kota_foreign` FOREIGN KEY (`id_kabupaten_kota`) REFERENCES `kabupatens` (`id_kabupaten_kota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `klaim_merchandises`
--
ALTER TABLE `klaim_merchandises`
  ADD CONSTRAINT `klaim_merchandises_id_merchandise_foreign` FOREIGN KEY (`ID_MERCHANDISE`) REFERENCES `merchandises` (`ID_MERCHANDISE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `klaim_merchandises_id_pembeli_foreign` FOREIGN KEY (`ID_PEMBELI`) REFERENCES `pembelis` (`ID_PEMBELI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `komisis`
--
ALTER TABLE `komisis`
  ADD CONSTRAINT `komisis_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `komisis_id_penitip_foreign` FOREIGN KEY (`ID_PENITIP`) REFERENCES `penitips` (`ID_PENITIP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `komisis_id_transaksi_pembelian_foreign` FOREIGN KEY (`ID_TRANSAKSI_PEMBELIAN`) REFERENCES `transaksi_pembelian_barangs` (`ID_TRANSAKSI_PEMBELIAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kurir_transaksi_pembelians`
--
ALTER TABLE `kurir_transaksi_pembelians`
  ADD CONSTRAINT `kurir_transaksi_pembelians_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kurir_transaksi_pembelians_id_transaksi_pembelian_foreign` FOREIGN KEY (`ID_TRANSAKSI_PEMBELIAN`) REFERENCES `transaksi_pembelian_barangs` (`ID_TRANSAKSI_PEMBELIAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pegawais`
--
ALTER TABLE `pegawais`
  ADD CONSTRAINT `pegawais_id_jabatan_foreign` FOREIGN KEY (`ID_JABATAN`) REFERENCES `jabatans` (`ID_JABATAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pegawai_transaksi_pembelians`
--
ALTER TABLE `pegawai_transaksi_pembelians`
  ADD CONSTRAINT `pegawai_transaksi_pembelians_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pegawai_transaksi_pembelians_id_transaksi_pembelian_foreign` FOREIGN KEY (`ID_TRANSAKSI_PEMBELIAN`) REFERENCES `transaksi_pembelian_barangs` (`ID_TRANSAKSI_PEMBELIAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pegawai_transaksi_penitipans`
--
ALTER TABLE `pegawai_transaksi_penitipans`
  ADD CONSTRAINT `pegawai_transaksi_penitipans_id_pegawai_foreign` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawais` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pegawai_transaksi_penitipans_id_transaksi_penitipan_foreign` FOREIGN KEY (`ID_TRANSAKSI_PENITIPAN`) REFERENCES `transaksi_penitipan_barangs` (`ID_TRANSAKSI_PENITIPAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_id_barang_foreign` FOREIGN KEY (`ID_BARANG`) REFERENCES `barangs` (`ID_BARANG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requests_id_organisasi_foreign` FOREIGN KEY (`ID_ORGANISASI`) REFERENCES `organisasis` (`ID_ORGANISASI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_donasis`
--
ALTER TABLE `transaksi_donasis`
  ADD CONSTRAINT `transaksi_donasis_id_organisasi_foreign` FOREIGN KEY (`ID_ORGANISASI`) REFERENCES `organisasis` (`ID_ORGANISASI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_donasis_id_request_foreign` FOREIGN KEY (`ID_REQUEST`) REFERENCES `requests` (`ID_REQUEST`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_pembelian_barangs`
--
ALTER TABLE `transaksi_pembelian_barangs`
  ADD CONSTRAINT `transaksi_pembelian_barangs_id_alamat_pengiriman_foreign` FOREIGN KEY (`ID_ALAMAT_PENGIRIMAN`) REFERENCES `alamats` (`ID_ALAMAT`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_pembelian_barangs_id_barang_foreign` FOREIGN KEY (`ID_BARANG`) REFERENCES `barangs` (`ID_BARANG`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_pembelian_barangs_id_pembeli_foreign` FOREIGN KEY (`ID_PEMBELI`) REFERENCES `pembelis` (`ID_PEMBELI`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_penitipan_barangs`
--
ALTER TABLE `transaksi_penitipan_barangs`
  ADD CONSTRAINT `transaksi_penitipan_barangs_id_penitip_foreign` FOREIGN KEY (`ID_PENITIP`) REFERENCES `penitips` (`ID_PENITIP`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
