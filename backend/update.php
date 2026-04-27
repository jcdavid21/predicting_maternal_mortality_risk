<?php
/**
 * rename_patients.php
 * 
 * Renames all anonymous patient records in the maternal_dbase database
 * to realistic Filipino names.
 * 
 * USAGE: Run once via browser or CLI: php rename_patients.php
 */

// ──────────────────────────────────────────────
// DATABASE CONFIG — update these before running
// ──────────────────────────────────────────────
$host = 'localhost';
$db   = 'maternal_dbase';
$user = 'root';
$pass = '';
$port = 3306;

// ──────────────────────────────────────────────
// CONNECT
// ──────────────────────────────────────────────
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ──────────────────────────────────────────────
// NAME MAPPINGS  (patient_id => new_name)
// PAT-XXXX patients: update `name` column only
// ANON-X patients:   update both `name` and username-style name field
// ──────────────────────────────────────────────
$updates = [
    // PAT-0011 | was: anonymous1
    11 => ['name' => 'Gertrudes Gonzales', 'username' => null],
    // PAT-0012 | was: anonymous2
    12 => ['name' => 'Luz Tamayo', 'username' => null],
    // PAT-0013 | was: anonymous3
    13 => ['name' => 'Perla Magno', 'username' => null],
    // PAT-0014 | was: anonymous4
    14 => ['name' => 'Erlinda Morales', 'username' => null],
    // PAT-0015 | was: anonymous5
    15 => ['name' => 'Ursula Lopez', 'username' => null],
    // PAT-0016 | was: anonymous6
    16 => ['name' => 'Lolita Tamayo', 'username' => null],
    // PAT-0017 | was: anonymous7
    17 => ['name' => 'Raquel Recto', 'username' => null],
    // PAT-0018 | was: anonymous8
    18 => ['name' => 'Natividad Abad', 'username' => null],
    // PAT-0019 | was: anonymous9
    19 => ['name' => 'Filipina Mendoza', 'username' => null],
    // PAT-0020 | was: anonymous10
    20 => ['name' => 'Luz Villanueva', 'username' => null],
    // ANON-1.00 | was: anonymous1.00000000000000000000000000000000000000
    31 => ['name' => 'Gertrudes Gonzales', 'username' => 'gertrudes_gonzales'],
    // ANON-2.00 | was: anonymous2.00000000000000000000000000000000000000
    32 => ['name' => 'Luz Tamayo', 'username' => 'luz_tamayo'],
    // ANON-3.00 | was: anonymous3.00000000000000000000000000000000000000
    33 => ['name' => 'Perla Magno', 'username' => 'perla_magno'],
    // ANON-4.00 | was: anonymous4.00000000000000000000000000000000000000
    34 => ['name' => 'Erlinda Morales', 'username' => 'erlinda_morales'],
    // ANON-5.00 | was: anonymous5.00000000000000000000000000000000000000
    35 => ['name' => 'Ursula Lopez', 'username' => 'ursula_lopez'],
    // ANON-6.00 | was: anonymous6.00000000000000000000000000000000000000
    36 => ['name' => 'Lolita Tamayo', 'username' => 'lolita_tamayo'],
    // ANON-7.00 | was: anonymous7.00000000000000000000000000000000000000
    37 => ['name' => 'Raquel Recto', 'username' => 'raquel_recto'],
    // ANON-8.00 | was: anonymous8.00000000000000000000000000000000000000
    38 => ['name' => 'Natividad Abad', 'username' => 'natividad_abad'],
    // ANON-9.00 | was: anonymous9.00000000000000000000000000000000000000
    39 => ['name' => 'Filipina Mendoza', 'username' => 'filipina_mendoza'],
    // ANON-10.0 | was: anonymous10.00000000000000000000000000000000000000
    40 => ['name' => 'Luz Villanueva', 'username' => 'luz_villanueva'],
    // ANON-11.0 | was: anonymous11.00000000000000000000000000000000000000
    41 => ['name' => 'Caridad Pascual', 'username' => 'caridad_pascual'],
    // ANON-12.0 | was: anonymous12.00000000000000000000000000000000000000
    42 => ['name' => 'Pamela Cabanero', 'username' => 'pamela_cabanero'],
    // ANON-13.0 | was: anonymous13.00000000000000000000000000000000000000
    43 => ['name' => 'Luz Tinio', 'username' => 'luz_tinio'],
    // ANON-14.0 | was: anonymous14.00000000000000000000000000000000000000
    44 => ['name' => 'Margarita Quimpo', 'username' => 'margarita_quimpo'],
    // ANON-15.0 | was: anonymous15.00000000000000000000000000000000000000
    45 => ['name' => 'Isabelita Obsena', 'username' => 'isabelita_obsena'],
    // ANON-16.0 | was: anonymous16.00000000000000000000000000000000000000
    46 => ['name' => 'Ursula Baguio', 'username' => 'ursula_baguio'],
    // ANON-17.0 | was: anonymous17.00000000000000000000000000000000000000
    47 => ['name' => 'Erlinda Fajardo', 'username' => 'erlinda_fajardo'],
    // ANON-18.0 | was: anonymous18.00000000000000000000000000000000000000
    48 => ['name' => 'Aileen Ilagan', 'username' => 'aileen_ilagan'],
    // ANON-19.0 | was: anonymous19.00000000000000000000000000000000000000
    49 => ['name' => 'Genoveva Santos', 'username' => 'genoveva_santos'],
    // ANON-20.0 | was: anonymous20.00000000000000000000000000000000000000
    50 => ['name' => 'Arlene Soriano', 'username' => 'arlene_soriano'],
    // ANON-21.0 | was: anonymous21.00000000000000000000000000000000000000
    51 => ['name' => 'Ofelia Camacho', 'username' => 'ofelia_camacho'],
    // ANON-22.0 | was: anonymous22.00000000000000000000000000000000000000
    52 => ['name' => 'Resurreccion Ilagan', 'username' => 'resurreccion_ilagan'],
    // ANON-23.0 | was: anonymous23.00000000000000000000000000000000000000
    53 => ['name' => 'Anita Santiago', 'username' => 'anita_santiago'],
    // ANON-24.0 | was: anonymous24.00000000000000000000000000000000000000
    54 => ['name' => 'Arlene Madriaga', 'username' => 'arlene_madriaga'],
    // ANON-25.0 | was: anonymous25.00000000000000000000000000000000000000
    55 => ['name' => 'Jocelyn Villanueva', 'username' => 'jocelyn_villanueva'],
    // ANON-26.0 | was: anonymous26.00000000000000000000000000000000000000
    56 => ['name' => 'Zenaida Fernandez', 'username' => 'zenaida_fernandez'],
    // ANON-27.0 | was: anonymous27.00000000000000000000000000000000000000
    57 => ['name' => 'Trinidad Valdez', 'username' => 'trinidad_valdez'],
    // ANON-28.0 | was: anonymous28.00000000000000000000000000000000000000
    58 => ['name' => 'Celestina Espinosa', 'username' => 'celestina_espinosa'],
    // ANON-29.0 | was: anonymous29.00000000000000000000000000000000000000
    59 => ['name' => 'Genoveva Torres', 'username' => 'genoveva_torres'],
    // ANON-30.0 | was: anonymous30.00000000000000000000000000000000000000
    60 => ['name' => 'Tessie Galang', 'username' => 'tessie_galang'],
    // ANON-31.0 | was: anonymous31.00000000000000000000000000000000000000
    61 => ['name' => 'Tina Castillo', 'username' => 'tina_castillo'],
    // ANON-32.0 | was: anonymous32.00000000000000000000000000000000000000
    62 => ['name' => 'Vivian Villafuerte', 'username' => 'vivian_villafuerte'],
    // ANON-33.0 | was: anonymous33.00000000000000000000000000000000000000
    63 => ['name' => 'Rowena Saguisag', 'username' => 'rowena_saguisag'],
    // ANON-34.0 | was: anonymous34.00000000000000000000000000000000000000
    64 => ['name' => 'Dolores Feria', 'username' => 'dolores_feria'],
    // ANON-35.0 | was: anonymous35.00000000000000000000000000000000000000
    65 => ['name' => 'Emmeline Lagman', 'username' => 'emmeline_lagman'],
    // ANON-36.0 | was: anonymous36.00000000000000000000000000000000000000
    66 => ['name' => 'Yolanda Dela Cruz', 'username' => 'yolanda_dela_cruz'],
    // ANON-37.0 | was: anonymous37.00000000000000000000000000000000000000
    67 => ['name' => 'Paz Ramirez', 'username' => 'paz_ramirez'],
    // ANON-38.0 | was: anonymous38.00000000000000000000000000000000000000
    68 => ['name' => 'Lorna Jacinto', 'username' => 'lorna_jacinto'],
    // ANON-39.0 | was: anonymous39.00000000000000000000000000000000000000
    69 => ['name' => 'Leticia Zulueta', 'username' => 'leticia_zulueta'],
    // ANON-40.0 | was: anonymous40.00000000000000000000000000000000000000
    70 => ['name' => 'Dolores Bautista', 'username' => 'dolores_bautista'],
    // ANON-41.0 | was: anonymous41.00000000000000000000000000000000000000
    71 => ['name' => 'Melinda Pascual', 'username' => 'melinda_pascual'],
    // ANON-42.0 | was: anonymous42.00000000000000000000000000000000000000
    72 => ['name' => 'Narcisa Fernandez', 'username' => 'narcisa_fernandez'],
    // ANON-43.0 | was: anonymous43.00000000000000000000000000000000000000
    73 => ['name' => 'Zenaida Ilagan', 'username' => 'zenaida_ilagan'],
    // ANON-44.0 | was: anonymous44.00000000000000000000000000000000000000
    74 => ['name' => 'Jenny Gatmaitan', 'username' => 'jenny_gatmaitan'],
    // ANON-45.0 | was: anonymous45.00000000000000000000000000000000000000
    75 => ['name' => 'Josie Lagman', 'username' => 'josie_lagman'],
    // ANON-46.0 | was: anonymous46.00000000000000000000000000000000000000
    76 => ['name' => 'Fe Palma', 'username' => 'fe_palma'],
    // ANON-47.0 | was: anonymous47.00000000000000000000000000000000000000
    77 => ['name' => 'Trinidad Mercado', 'username' => 'trinidad_mercado'],
    // ANON-48.0 | was: anonymous48.00000000000000000000000000000000000000
    78 => ['name' => 'Kristina Rivera', 'username' => 'kristina_rivera'],
    // ANON-49.0 | was: anonymous49.00000000000000000000000000000000000000
    79 => ['name' => 'Ofelia Manio', 'username' => 'ofelia_manio'],
    // ANON-50.0 | was: anonymous50.00000000000000000000000000000000000000
    80 => ['name' => 'Herminia Aquino', 'username' => 'herminia_aquino'],
    // ANON-51.0 | was: anonymous51.00000000000000000000000000000000000000
    81 => ['name' => 'Celestina Gatmaitan', 'username' => 'celestina_gatmaitan'],
    // ANON-52.0 | was: anonymous52.00000000000000000000000000000000000000
    82 => ['name' => 'Estrella Quezon', 'username' => 'estrella_quezon'],
    // ANON-53.0 | was: anonymous53.00000000000000000000000000000000000000
    83 => ['name' => 'Tessie Magno', 'username' => 'tessie_magno'],
    // ANON-54.0 | was: anonymous54.00000000000000000000000000000000000000
    84 => ['name' => 'Fe Hilario', 'username' => 'fe_hilario'],
    // ANON-55.0 | was: anonymous55.00000000000000000000000000000000000000
    85 => ['name' => 'Zenaida Rivera', 'username' => 'zenaida_rivera'],
    // ANON-56.0 | was: anonymous56.00000000000000000000000000000000000000
    86 => ['name' => 'Vivian Gatmaitan', 'username' => 'vivian_gatmaitan'],
    // ANON-57.0 | was: anonymous57.00000000000000000000000000000000000000
    87 => ['name' => 'Nena Tinio', 'username' => 'nena_tinio'],
    // ANON-58.0 | was: anonymous58.00000000000000000000000000000000000000
    88 => ['name' => 'Erlinda Manio', 'username' => 'erlinda_manio'],
    // ANON-59.0 | was: anonymous59.00000000000000000000000000000000000000
    89 => ['name' => 'Nilda Zulueta', 'username' => 'nilda_zulueta'],
    // ANON-60.0 | was: anonymous60.00000000000000000000000000000000000000
    90 => ['name' => 'Carmela Ramos', 'username' => 'carmela_ramos'],
    // ANON-61.0 | was: anonymous61.00000000000000000000000000000000000000
    91 => ['name' => 'Leticia Mendoza', 'username' => 'leticia_mendoza'],
    // ANON-62.0 | was: anonymous62.00000000000000000000000000000000000000
    92 => ['name' => 'Genoveva Manalo', 'username' => 'genoveva_manalo'],
    // ANON-63.0 | was: anonymous63.00000000000000000000000000000000000000
    93 => ['name' => 'Charito Rivera', 'username' => 'charito_rivera'],
    // ANON-64.0 | was: anonymous64.00000000000000000000000000000000000000
    94 => ['name' => 'Cristina Santiago', 'username' => 'cristina_santiago'],
    // ANON-65.0 | was: anonymous65.00000000000000000000000000000000000000
    95 => ['name' => 'Teodora Umali', 'username' => 'teodora_umali'],
    // ANON-66.0 | was: anonymous66.00000000000000000000000000000000000000
    96 => ['name' => 'Patricia Quimpo', 'username' => 'patricia_quimpo'],
    // ANON-67.0 | was: anonymous67.00000000000000000000000000000000000000
    97 => ['name' => 'Mercedita Santiago', 'username' => 'mercedita_santiago'],
    // ANON-68.0 | was: anonymous68.00000000000000000000000000000000000000
    98 => ['name' => 'Isabelita Lacson', 'username' => 'isabelita_lacson'],
    // ANON-69.0 | was: anonymous69.00000000000000000000000000000000000000
    99 => ['name' => 'Bebelyn Hontiveros', 'username' => 'bebelyn_hontiveros'],
    // ANON-70.0 | was: anonymous70.00000000000000000000000000000000000000
    100 => ['name' => 'Jenny Aguilar', 'username' => 'jenny_aguilar'],
    // ANON-71.0 | was: anonymous71.00000000000000000000000000000000000000
    101 => ['name' => 'Conception Morales', 'username' => 'conception_morales'],
    // ANON-72.0 | was: anonymous72.00000000000000000000000000000000000000
    102 => ['name' => 'Milagros Ureta', 'username' => 'milagros_ureta'],
    // ANON-73.0 | was: anonymous73.00000000000000000000000000000000000000
    103 => ['name' => 'Wanda Quezon', 'username' => 'wanda_quezon'],
    // ANON-74.0 | was: anonymous74.00000000000000000000000000000000000000
    104 => ['name' => 'Conception Ureta', 'username' => 'conception_ureta'],
    // ANON-75.0 | was: anonymous75.00000000000000000000000000000000000000
    105 => ['name' => 'Zelda Camacho', 'username' => 'zelda_camacho'],
    // ANON-76.0 | was: anonymous76.00000000000000000000000000000000000000
    106 => ['name' => 'Raquel Wenceslao', 'username' => 'raquel_wenceslao'],
    // ANON-77.0 | was: anonymous77.00000000000000000000000000000000000000
    107 => ['name' => 'Charito Lagman', 'username' => 'charito_lagman'],
    // ANON-78.0 | was: anonymous78.00000000000000000000000000000000000000
    108 => ['name' => 'Queenie Lacson', 'username' => 'queenie_lacson'],
    // ANON-79.0 | was: anonymous79.00000000000000000000000000000000000000
    109 => ['name' => 'Natividad Verzosa', 'username' => 'natividad_verzosa'],
    // ANON-80.0 | was: anonymous80.00000000000000000000000000000000000000
    110 => ['name' => 'Grace Gonzales', 'username' => 'grace_gonzales'],
    // ANON-81.0 | was: anonymous81.00000000000000000000000000000000000000
    111 => ['name' => 'Anita Feria', 'username' => 'anita_feria'],
    // ANON-82.0 | was: anonymous82.00000000000000000000000000000000000000
    112 => ['name' => 'Fe Manio', 'username' => 'fe_manio'],
    // ANON-83.0 | was: anonymous83.00000000000000000000000000000000000000
    113 => ['name' => 'Filipina Bustamante', 'username' => 'filipina_bustamante'],
    // ANON-84.0 | was: anonymous84.00000000000000000000000000000000000000
    114 => ['name' => 'Cristina Chua', 'username' => 'cristina_chua'],
    // ANON-85.0 | was: anonymous85.00000000000000000000000000000000000000
    115 => ['name' => 'Zenaida Bustamante', 'username' => 'zenaida_bustamante'],
    // ANON-86.0 | was: anonymous86.00000000000000000000000000000000000000
    116 => ['name' => 'Katrina Perez', 'username' => 'katrina_perez'],
    // ANON-87.0 | was: anonymous87.00000000000000000000000000000000000000
    117 => ['name' => 'Adoracion Saguisag', 'username' => 'adoracion_saguisag'],
    // ANON-88.0 | was: anonymous88.00000000000000000000000000000000000000
    118 => ['name' => 'Narcisa Reyes', 'username' => 'narcisa_reyes'],
    // ANON-89.0 | was: anonymous89.00000000000000000000000000000000000000
    119 => ['name' => 'Minda Ricofort', 'username' => 'minda_ricofort'],
    // ANON-90.0 | was: anonymous90.00000000000000000000000000000000000000
    120 => ['name' => 'Remedios Manio', 'username' => 'remedios_manio'],
    // ANON-91.0 | was: anonymous91.00000000000000000000000000000000000000
    121 => ['name' => 'Quirina Quezon', 'username' => 'quirina_quezon'],
    // ANON-92.0 | was: anonymous92.00000000000000000000000000000000000000
    122 => ['name' => 'Wilhelmina Rivera', 'username' => 'wilhelmina_rivera'],
    // ANON-93.0 | was: anonymous93.00000000000000000000000000000000000000
    123 => ['name' => 'Bernadette Hontiveros', 'username' => 'bernadette_hontiveros'],
    // ANON-94.0 | was: anonymous94.00000000000000000000000000000000000000
    124 => ['name' => 'Resurreccion Gonzales', 'username' => 'resurreccion_gonzales'],
    // ANON-95.0 | was: anonymous95.00000000000000000000000000000000000000
    125 => ['name' => 'Dolores Dalisay', 'username' => 'dolores_dalisay'],
    // ANON-96.0 | was: anonymous96.00000000000000000000000000000000000000
    126 => ['name' => 'Fe Galang', 'username' => 'fe_galang'],
    // ANON-97.0 | was: anonymous97.00000000000000000000000000000000000000
    127 => ['name' => 'Maria Ricofort', 'username' => 'maria_ricofort'],
    // ANON-98.0 | was: anonymous98.00000000000000000000000000000000000000
    128 => ['name' => 'Patricia Ricofort', 'username' => 'patricia_ricofort'],
    // ANON-99.0 | was: anonymous99.00000000000000000000000000000000000000
    129 => ['name' => 'Conception Macapagal', 'username' => 'conception_macapagal'],
    // ANON-100. | was: anonymous100.00000000000000000000000000000000000000
    130 => ['name' => 'Arlene Lim', 'username' => 'arlene_lim'],
    // ANON-101. | was: anonymous101.00000000000000000000000000000000000000
    131 => ['name' => 'Pamela Lopez', 'username' => 'pamela_lopez'],
    // ANON-102. | was: anonymous102.00000000000000000000000000000000000000
    132 => ['name' => 'Olympia Feria', 'username' => 'olympia_feria'],
    // ANON-103. | was: anonymous103.00000000000000000000000000000000000000
    133 => ['name' => 'Felicidad Gatmaitan', 'username' => 'felicidad_gatmaitan'],
    // ANON-104. | was: anonymous104.00000000000000000000000000000000000000
    134 => ['name' => 'Margarita Castro', 'username' => 'margarita_castro'],
    // ANON-105. | was: anonymous105.00000000000000000000000000000000000000
    135 => ['name' => 'Wilma Yap', 'username' => 'wilma_yap'],
    // ANON-106. | was: anonymous106.00000000000000000000000000000000000000
    136 => ['name' => 'Fe Recto', 'username' => 'fe_recto'],
    // ANON-107. | was: anonymous107.00000000000000000000000000000000000000
    137 => ['name' => 'Carmela Perez', 'username' => 'carmela_perez'],
    // ANON-108. | was: anonymous108.00000000000000000000000000000000000000
    138 => ['name' => 'Ursulina Santos', 'username' => 'ursulina_santos'],
    // ANON-109. | was: anonymous109.00000000000000000000000000000000000000
    139 => ['name' => 'Belen Buenaventura', 'username' => 'belen_buenaventura'],
    // ANON-110. | was: anonymous110.00000000000000000000000000000000000000
    140 => ['name' => 'Nancy Cruz', 'username' => 'nancy_cruz'],
    // ANON-111. | was: anonymous111.00000000000000000000000000000000000000
    141 => ['name' => 'Remedios Lagman', 'username' => 'remedios_lagman'],
    // ANON-112. | was: anonymous112.00000000000000000000000000000000000000
    142 => ['name' => 'Patricia Dela Torre', 'username' => 'patricia_dela_torre'],
    // ANON-113. | was: anonymous113.00000000000000000000000000000000000000
    143 => ['name' => 'Florencia Ramos', 'username' => 'florencia_ramos'],
    // ANON-114. | was: anonymous114.00000000000000000000000000000000000000
    144 => ['name' => 'Florencia Umali', 'username' => 'florencia_umali'],
    // ANON-115. | was: anonymous115.00000000000000000000000000000000000000
    145 => ['name' => 'Yasmin Bautista', 'username' => 'yasmin_bautista'],
    // ANON-116. | was: anonymous116.00000000000000000000000000000000000000
    146 => ['name' => 'Rowena Salcedo', 'username' => 'rowena_salcedo'],
    // ANON-117. | was: anonymous117.00000000000000000000000000000000000000
    147 => ['name' => 'Nancy Ramirez', 'username' => 'nancy_ramirez'],
    // ANON-118. | was: anonymous118.00000000000000000000000000000000000000
    148 => ['name' => 'Arlene Quezon', 'username' => 'arlene_quezon'],
    // ANON-119. | was: anonymous119.00000000000000000000000000000000000000
    149 => ['name' => 'Bernadette Diaz', 'username' => 'bernadette_diaz'],
    // ANON-120. | was: anonymous120.00000000000000000000000000000000000000
    150 => ['name' => 'Leonora Jacinto', 'username' => 'leonora_jacinto'],
    // ANON-121. | was: anonymous121.00000000000000000000000000000000000000
    151 => ['name' => 'Lily Saguisag', 'username' => 'lily_saguisag'],
    // ANON-122. | was: anonymous122.00000000000000000000000000000000000000
    152 => ['name' => 'Estrella Espinosa', 'username' => 'estrella_espinosa'],
    // ANON-123. | was: anonymous123.00000000000000000000000000000000000000
    153 => ['name' => 'Sheila Cabanero', 'username' => 'sheila_cabanero'],
    // ANON-124. | was: anonymous124.00000000000000000000000000000000000000
    154 => ['name' => 'Filipina Santiago', 'username' => 'filipina_santiago'],
    // ANON-125. | was: anonymous125.00000000000000000000000000000000000000
    155 => ['name' => 'Vivian Recto', 'username' => 'vivian_recto'],
    // ANON-126. | was: anonymous126.00000000000000000000000000000000000000
    156 => ['name' => 'Wilhelmina Salcedo', 'username' => 'wilhelmina_salcedo'],
    // ANON-127. | was: anonymous127.00000000000000000000000000000000000000
    157 => ['name' => 'Nena De Leon', 'username' => 'nena_de_leon'],
    // ANON-128. | was: anonymous128.00000000000000000000000000000000000000
    158 => ['name' => 'Rebecca Dela Torre', 'username' => 'rebecca_dela_torre'],
    // ANON-129. | was: anonymous129.00000000000000000000000000000000000000
    159 => ['name' => 'Charito Katipunan', 'username' => 'charito_katipunan'],
    // ANON-130. | was: anonymous130.00000000000000000000000000000000000000
    160 => ['name' => 'Isabelita Palma', 'username' => 'isabelita_palma'],
    // ANON-131. | was: anonymous131.00000000000000000000000000000000000000
    161 => ['name' => 'Hazel Ongsiako', 'username' => 'hazel_ongsiako'],
    // ANON-132. | was: anonymous132.00000000000000000000000000000000000000
    162 => ['name' => 'Ina Castillo', 'username' => 'ina_castillo'],
    // ANON-133. | was: anonymous133.00000000000000000000000000000000000000
    163 => ['name' => 'Milagros Hernandez', 'username' => 'milagros_hernandez'],
    // ANON-134. | was: anonymous134.00000000000000000000000000000000000000
    164 => ['name' => 'Cristina Madriaga', 'username' => 'cristina_madriaga'],
    // ANON-135. | was: anonymous135.00000000000000000000000000000000000000
    165 => ['name' => 'Rosa Abad', 'username' => 'rosa_abad'],
    // ANON-136. | was: anonymous136.00000000000000000000000000000000000000
    166 => ['name' => 'Vera Pascual', 'username' => 'vera_pascual'],
    // ANON-137. | was: anonymous137.00000000000000000000000000000000000000
    167 => ['name' => 'Aileen Hernandez', 'username' => 'aileen_hernandez'],
    // ANON-138. | was: anonymous138.00000000000000000000000000000000000000
    168 => ['name' => 'Maria Aquino', 'username' => 'maria_aquino'],
    // ANON-139. | was: anonymous139.00000000000000000000000000000000000000
    169 => ['name' => 'Paz Feria', 'username' => 'paz_feria'],
    // ANON-140. | was: anonymous140.00000000000000000000000000000000000000
    170 => ['name' => 'Maricel Pascual', 'username' => 'maricel_pascual'],
    // ANON-141. | was: anonymous141.00000000000000000000000000000000000000
    171 => ['name' => 'Cristina Mendoza', 'username' => 'cristina_mendoza'],
    // ANON-142. | was: anonymous142.00000000000000000000000000000000000000
    172 => ['name' => 'Narcisa Pagcaliwagan', 'username' => 'narcisa_pagcaliwagan'],
    // ANON-143. | was: anonymous143.00000000000000000000000000000000000000
    173 => ['name' => 'Elisa Navarette', 'username' => 'elisa_navarette'],
    // ANON-144. | was: anonymous144.00000000000000000000000000000000000000
    174 => ['name' => 'Florencia Ilagan', 'username' => 'florencia_ilagan'],
    // ANON-145. | was: anonymous145.00000000000000000000000000000000000000
    175 => ['name' => 'Kristina Kalaw', 'username' => 'kristina_kalaw'],
    // ANON-146. | was: anonymous146.00000000000000000000000000000000000000
    176 => ['name' => 'Caridad Recto', 'username' => 'caridad_recto'],
    // ANON-147. | was: anonymous147.00000000000000000000000000000000000000
    177 => ['name' => 'Leonora Ricofort', 'username' => 'leonora_ricofort'],
    // ANON-148. | was: anonymous148.00000000000000000000000000000000000000
    178 => ['name' => 'Wenifreda Viray', 'username' => 'wenifreda_viray'],
    // ANON-149. | was: anonymous149.00000000000000000000000000000000000000
    179 => ['name' => 'Yolanda Ibarra', 'username' => 'yolanda_ibarra'],
    // ANON-150. | was: anonymous150.00000000000000000000000000000000000000
    180 => ['name' => 'Milagros Ibarra', 'username' => 'milagros_ibarra'],
    // ANON-151. | was: anonymous151.00000000000000000000000000000000000000
    181 => ['name' => 'Genoveva Ablaza', 'username' => 'genoveva_ablaza'],
    // ANON-152. | was: anonymous152.00000000000000000000000000000000000000
    182 => ['name' => 'Lorena Fernandez', 'username' => 'lorena_fernandez'],
    // ANON-153. | was: anonymous153.00000000000000000000000000000000000000
    183 => ['name' => 'Teresita Jacinto', 'username' => 'teresita_jacinto'],
    // ANON-154. | was: anonymous154.00000000000000000000000000000000000000
    184 => ['name' => 'Gloria Guerrero', 'username' => 'gloria_guerrero'],
    // ANON-155. | was: anonymous155.00000000000000000000000000000000000000
    185 => ['name' => 'Filipina Ablaza', 'username' => 'filipina_ablaza'],
    // ANON-156. | was: anonymous156.00000000000000000000000000000000000000
    186 => ['name' => 'Katrina Salcedo', 'username' => 'katrina_salcedo'],
    // ANON-157. | was: anonymous157.00000000000000000000000000000000000000
    187 => ['name' => 'Grace Larena', 'username' => 'grace_larena'],
    // ANON-158. | was: anonymous158.00000000000000000000000000000000000000
    188 => ['name' => 'Isabelita Hontiveros', 'username' => 'isabelita_hontiveros'],
    // ANON-159. | was: anonymous159.00000000000000000000000000000000000000
    189 => ['name' => 'Teresita Ramos', 'username' => 'teresita_ramos'],
    // ANON-160. | was: anonymous160.00000000000000000000000000000000000000
    190 => ['name' => 'Charito Salcedo', 'username' => 'charito_salcedo'],
    // ANON-161. | was: anonymous161.00000000000000000000000000000000000000
    191 => ['name' => 'Resurreccion Lopez', 'username' => 'resurreccion_lopez'],
    // ANON-162. | was: anonymous162.00000000000000000000000000000000000000
    192 => ['name' => 'Milagros Dela Cruz', 'username' => 'milagros_dela_cruz'],
    // ANON-163. | was: anonymous163.00000000000000000000000000000000000000
    193 => ['name' => 'Lorena Quezon', 'username' => 'lorena_quezon'],
    // ANON-164. | was: anonymous164.00000000000000000000000000000000000000
    194 => ['name' => 'Ina Morales', 'username' => 'ina_morales'],
    // ANON-165. | was: anonymous165.00000000000000000000000000000000000000
    195 => ['name' => 'Filipina Tan', 'username' => 'filipina_tan'],
    // ANON-166. | was: anonymous166.00000000000000000000000000000000000000
    196 => ['name' => 'Perla Hilario', 'username' => 'perla_hilario'],
    // ANON-167. | was: anonymous167.00000000000000000000000000000000000000
    197 => ['name' => 'Milagros Aquino', 'username' => 'milagros_aquino'],
    // ANON-168. | was: anonymous168.00000000000000000000000000000000000000
    198 => ['name' => 'Hazel Saguisag', 'username' => 'hazel_saguisag'],
    // ANON-169. | was: anonymous169.00000000000000000000000000000000000000
    199 => ['name' => 'Teresita Flores', 'username' => 'teresita_flores'],
    // ANON-170. | was: anonymous170.00000000000000000000000000000000000000
    200 => ['name' => 'Isabelita Recto', 'username' => 'isabelita_recto'],
    // ANON-171. | was: anonymous171.00000000000000000000000000000000000000
    201 => ['name' => 'Karla Reyes', 'username' => 'karla_reyes'],
    // ANON-172. | was: anonymous172.00000000000000000000000000000000000000
    202 => ['name' => 'Lilia Tolentino', 'username' => 'lilia_tolentino'],
    // ANON-173. | was: anonymous173.00000000000000000000000000000000000000
    203 => ['name' => 'Estrella Ablaza', 'username' => 'estrella_ablaza'],
    // ANON-174. | was: anonymous174.00000000000000000000000000000000000000
    204 => ['name' => 'Nancy Jimenez', 'username' => 'nancy_jimenez'],
    // ANON-175. | was: anonymous175.00000000000000000000000000000000000000
    205 => ['name' => 'Caridad Quizon', 'username' => 'caridad_quizon'],
    // ANON-176. | was: anonymous176.00000000000000000000000000000000000000
    206 => ['name' => 'Salvacion Ramos', 'username' => 'salvacion_ramos'],
    // ANON-177. | was: anonymous177.00000000000000000000000000000000000000
    207 => ['name' => 'Estrella Villafuerte', 'username' => 'estrella_villafuerte'],
    // ANON-178. | was: anonymous178.00000000000000000000000000000000000000
    208 => ['name' => 'Maria Chua', 'username' => 'maria_chua'],
    // ANON-179. | was: anonymous179.00000000000000000000000000000000000000
    209 => ['name' => 'Conception Galang', 'username' => 'conception_galang'],
    // ANON-180. | was: anonymous180.00000000000000000000000000000000000000
    210 => ['name' => 'Luisa Camacho', 'username' => 'luisa_camacho'],
    // ANON-181. | was: anonymous181.00000000000000000000000000000000000000
    211 => ['name' => 'Ofelia Salcedo', 'username' => 'ofelia_salcedo'],
    // ANON-182. | was: anonymous182.00000000000000000000000000000000000000
    212 => ['name' => 'Dalisay Tinio', 'username' => 'dalisay_tinio'],
    // ANON-183. | was: anonymous183.00000000000000000000000000000000000000
    213 => ['name' => 'Jovita Quimpo', 'username' => 'jovita_quimpo'],
    // ANON-184. | was: anonymous184.00000000000000000000000000000000000000
    214 => ['name' => 'Nancy Castro', 'username' => 'nancy_castro'],
    // ANON-185. | was: anonymous185.00000000000000000000000000000000000000
    215 => ['name' => 'Lorena Salazar', 'username' => 'lorena_salazar'],
    // ANON-186. | was: anonymous186.00000000000000000000000000000000000000
    216 => ['name' => 'Caridad Ramos', 'username' => 'caridad_ramos'],
    // ANON-187. | was: anonymous187.00000000000000000000000000000000000000
    217 => ['name' => 'Zelda Tamayo', 'username' => 'zelda_tamayo'],
    // ANON-188. | was: anonymous188.00000000000000000000000000000000000000
    218 => ['name' => 'Ursula Ramos', 'username' => 'ursula_ramos'],
    // ANON-189. | was: anonymous189.00000000000000000000000000000000000000
    219 => ['name' => 'Violeta Manalo', 'username' => 'violeta_manalo'],
    // ANON-190. | was: anonymous190.00000000000000000000000000000000000000
    220 => ['name' => 'Maricel Flores', 'username' => 'maricel_flores'],
    // ANON-191. | was: anonymous191.00000000000000000000000000000000000000
    221 => ['name' => 'Zelda Jimenez', 'username' => 'zelda_jimenez'],
    // ANON-192. | was: anonymous192.00000000000000000000000000000000000000
    222 => ['name' => 'Pamela Perez', 'username' => 'pamela_perez'],
    // ANON-193. | was: anonymous193.00000000000000000000000000000000000000
    223 => ['name' => 'Fe Ramos', 'username' => 'fe_ramos'],
    // ANON-194. | was: anonymous194.00000000000000000000000000000000000000
    224 => ['name' => 'Queenie Bautista', 'username' => 'queenie_bautista'],
    // ANON-195. | was: anonymous195.00000000000000000000000000000000000000
    225 => ['name' => 'Lilia Tan', 'username' => 'lilia_tan'],
    // ANON-196. | was: anonymous196.00000000000000000000000000000000000000
    226 => ['name' => 'Cristina Bustamante', 'username' => 'cristina_bustamante'],
    // ANON-197. | was: anonymous197.00000000000000000000000000000000000000
    227 => ['name' => 'Cristina Larena', 'username' => 'cristina_larena'],
    // ANON-198. | was: anonymous198.00000000000000000000000000000000000000
    228 => ['name' => 'Narcisa Tolentino', 'username' => 'narcisa_tolentino'],
    // ANON-199. | was: anonymous199.00000000000000000000000000000000000000
    229 => ['name' => 'Charito Castillo', 'username' => 'charito_castillo'],
    // ANON-200. | was: anonymous200.00000000000000000000000000000000000000
    230 => ['name' => 'Xiomara Umali', 'username' => 'xiomara_umali'],
    // ANON-201. | was: anonymous201.00000000000000000000000000000000000000
    231 => ['name' => 'Milagros Wenceslao', 'username' => 'milagros_wenceslao'],
    // ANON-202. | was: anonymous202.00000000000000000000000000000000000000
    232 => ['name' => 'Belen Torres', 'username' => 'belen_torres'],
    // ANON-203. | was: anonymous203.00000000000000000000000000000000000000
    233 => ['name' => 'Emmeline Bautista', 'username' => 'emmeline_bautista'],
    // ANON-204. | was: anonymous204.00000000000000000000000000000000000000
    234 => ['name' => 'Edna Jacinto', 'username' => 'edna_jacinto'],
    // ANON-205. | was: anonymous205.00000000000000000000000000000000000000
    235 => ['name' => 'Zelda Umali', 'username' => 'zelda_umali'],
    // ANON-206. | was: anonymous206.00000000000000000000000000000000000000
    236 => ['name' => 'Rhea Manalo', 'username' => 'rhea_manalo'],
    // ANON-207. | was: anonymous207.00000000000000000000000000000000000000
    237 => ['name' => 'Wenifreda Espinosa', 'username' => 'wenifreda_espinosa'],
    // ANON-208. | was: anonymous208.00000000000000000000000000000000000000
    238 => ['name' => 'Josefina Katipunan', 'username' => 'josefina_katipunan'],
    // ANON-209. | was: anonymous209.00000000000000000000000000000000000000
    239 => ['name' => 'Rebecca Manalo', 'username' => 'rebecca_manalo'],
    // ANON-210. | was: anonymous210.00000000000000000000000000000000000000
    240 => ['name' => 'Florencia Espinosa', 'username' => 'florencia_espinosa'],
    // ANON-211. | was: anonymous211.00000000000000000000000000000000000000
    241 => ['name' => 'Bebelyn Diaz', 'username' => 'bebelyn_diaz'],
    // ANON-212. | was: anonymous212.00000000000000000000000000000000000000
    242 => ['name' => 'Kristina Hontiveros', 'username' => 'kristina_hontiveros'],
    // ANON-213. | was: anonymous213.00000000000000000000000000000000000000
    243 => ['name' => 'Felicidad Galang', 'username' => 'felicidad_galang'],
    // ANON-214. | was: anonymous214.00000000000000000000000000000000000000
    244 => ['name' => 'Mercedita Verzosa', 'username' => 'mercedita_verzosa'],
    // ANON-215. | was: anonymous215.00000000000000000000000000000000000000
    245 => ['name' => 'Wenifreda Aquino', 'username' => 'wenifreda_aquino'],
    // ANON-216. | was: anonymous216.00000000000000000000000000000000000000
    246 => ['name' => 'Ana Galang', 'username' => 'ana_galang'],
    // ANON-217. | was: anonymous217.00000000000000000000000000000000000000
    247 => ['name' => 'Emmeline Umali', 'username' => 'emmeline_umali'],
    // ANON-218. | was: anonymous218.00000000000000000000000000000000000000
    248 => ['name' => 'Teresita Aquino', 'username' => 'teresita_aquino'],
    // ANON-219. | was: anonymous219.00000000000000000000000000000000000000
    249 => ['name' => 'Tina Santiago', 'username' => 'tina_santiago'],
    // ANON-220. | was: anonymous220.00000000000000000000000000000000000000
    250 => ['name' => 'Pamela Espinosa', 'username' => 'pamela_espinosa'],
    // ANON-221. | was: anonymous221.00000000000000000000000000000000000000
    251 => ['name' => 'Leonora Valdez', 'username' => 'leonora_valdez'],
    // ANON-222. | was: anonymous222.00000000000000000000000000000000000000
    252 => ['name' => 'Patricia Ramirez', 'username' => 'patricia_ramirez'],
    // ANON-223. | was: anonymous223.00000000000000000000000000000000000000
    253 => ['name' => 'Patricia Magno', 'username' => 'patricia_magno'],
    // ANON-224. | was: anonymous224.00000000000000000000000000000000000000
    254 => ['name' => 'Wilma Navarro', 'username' => 'wilma_navarro'],
    // ANON-225. | was: anonymous225.00000000000000000000000000000000000000
    255 => ['name' => 'Fe Evangelista', 'username' => 'fe_evangelista'],
    // ANON-226. | was: anonymous226.00000000000000000000000000000000000000
    256 => ['name' => 'Josie Recto', 'username' => 'josie_recto'],
    // ANON-227. | was: anonymous227.00000000000000000000000000000000000000
    257 => ['name' => 'Paz Domingo', 'username' => 'paz_domingo'],
    // ANON-228. | was: anonymous228.00000000000000000000000000000000000000
    258 => ['name' => 'Diana Imperio', 'username' => 'diana_imperio'],
    // ANON-229. | was: anonymous229.00000000000000000000000000000000000000
    259 => ['name' => 'Sheila Reyes', 'username' => 'sheila_reyes'],
    // ANON-230. | was: anonymous230.00000000000000000000000000000000000000
    260 => ['name' => 'Kristina Saguisag', 'username' => 'kristina_saguisag'],
    // ANON-231. | was: anonymous231.00000000000000000000000000000000000000
    261 => ['name' => 'Felicidad Jacinto', 'username' => 'felicidad_jacinto'],
    // ANON-232. | was: anonymous232.00000000000000000000000000000000000000
    262 => ['name' => 'Jocelyn Morales', 'username' => 'jocelyn_morales'],
    // ANON-233. | was: anonymous233.00000000000000000000000000000000000000
    263 => ['name' => 'Conception Gonzales', 'username' => 'conception_gonzales'],
    // ANON-234. | was: anonymous234.00000000000000000000000000000000000000
    264 => ['name' => 'Quirina Lopez', 'username' => 'quirina_lopez'],
    // ANON-235. | was: anonymous235.00000000000000000000000000000000000000
    265 => ['name' => 'Violeta Saguisag', 'username' => 'violeta_saguisag'],
    // ANON-236. | was: anonymous236.00000000000000000000000000000000000000
    266 => ['name' => 'Anita Rivera', 'username' => 'anita_rivera'],
    // ANON-237. | was: anonymous237.00000000000000000000000000000000000000
    267 => ['name' => 'Luisa Cabanero', 'username' => 'luisa_cabanero'],
    // ANON-238. | was: anonymous238.00000000000000000000000000000000000000
    268 => ['name' => 'Josefina Quimpo', 'username' => 'josefina_quimpo'],
    // ANON-239. | was: anonymous239.00000000000000000000000000000000000000
    269 => ['name' => 'Resurreccion Mercado', 'username' => 'resurreccion_mercado'],
    // ANON-240. | was: anonymous240.00000000000000000000000000000000000000
    270 => ['name' => 'Minda Gatmaitan', 'username' => 'minda_gatmaitan'],
    // ANON-241. | was: anonymous241.00000000000000000000000000000000000000
    271 => ['name' => 'Melinda Espinosa', 'username' => 'melinda_espinosa'],
    // ANON-242. | was: anonymous242.00000000000000000000000000000000000000
    272 => ['name' => 'Pamela Kalaw', 'username' => 'pamela_kalaw'],
    // ANON-243. | was: anonymous243.00000000000000000000000000000000000000
    273 => ['name' => 'Adoracion Flores', 'username' => 'adoracion_flores'],
    // ANON-244. | was: anonymous244.00000000000000000000000000000000000000
    274 => ['name' => 'Natividad Gatmaitan', 'username' => 'natividad_gatmaitan'],
    // ANON-245. | was: anonymous245.00000000000000000000000000000000000000
    275 => ['name' => 'Filipina Ilagan', 'username' => 'filipina_ilagan'],
    // ANON-246. | was: anonymous246.00000000000000000000000000000000000000
    276 => ['name' => 'Lorna Santos', 'username' => 'lorna_santos'],
    // ANON-247. | was: anonymous247.00000000000000000000000000000000000000
    277 => ['name' => 'Norma Zulueta', 'username' => 'norma_zulueta'],
    // ANON-248. | was: anonymous248.00000000000000000000000000000000000000
    278 => ['name' => 'Leonora Gatmaitan', 'username' => 'leonora_gatmaitan'],
    // ANON-249. | was: anonymous249.00000000000000000000000000000000000000
    279 => ['name' => 'Conception Soriano', 'username' => 'conception_soriano'],
    // ANON-250. | was: anonymous250.00000000000000000000000000000000000000
    280 => ['name' => 'Ursula Evangelista', 'username' => 'ursula_evangelista'],
    // ANON-251. | was: anonymous251.00000000000000000000000000000000000000
    281 => ['name' => 'Vera Platon', 'username' => 'vera_platon'],
    // ANON-252. | was: anonymous252.00000000000000000000000000000000000000
    282 => ['name' => 'Filipina Tinio', 'username' => 'filipina_tinio'],
    // ANON-253. | was: anonymous253.00000000000000000000000000000000000000
    283 => ['name' => 'Ana Gonzales', 'username' => 'ana_gonzales'],
    // ANON-254. | was: anonymous254.00000000000000000000000000000000000000
    284 => ['name' => 'Elisa Natividad', 'username' => 'elisa_natividad'],
    // ANON-255. | was: anonymous255.00000000000000000000000000000000000000
    285 => ['name' => 'Salvacion Castro', 'username' => 'salvacion_castro'],
    // ANON-256. | was: anonymous256.00000000000000000000000000000000000000
    286 => ['name' => 'Ursula Mendoza', 'username' => 'ursula_mendoza'],
    // ANON-257. | was: anonymous257.00000000000000000000000000000000000000
    287 => ['name' => 'Josie Palma', 'username' => 'josie_palma'],
    // ANON-258. | was: anonymous258.00000000000000000000000000000000000000
    288 => ['name' => 'Zelda Saguisag', 'username' => 'zelda_saguisag'],
    // ANON-259. | was: anonymous259.00000000000000000000000000000000000000
    289 => ['name' => 'Corazon Dalisay', 'username' => 'corazon_dalisay'],
    // ANON-260. | was: anonymous260.00000000000000000000000000000000000000
    290 => ['name' => 'Leonora Torres', 'username' => 'leonora_torres'],
    // ANON-261. | was: anonymous261.00000000000000000000000000000000000000
    291 => ['name' => 'Esmeralda Lagman', 'username' => 'esmeralda_lagman'],
    // ANON-262. | was: anonymous262.00000000000000000000000000000000000000
    292 => ['name' => 'Salvacion Torres', 'username' => 'salvacion_torres'],
    // ANON-263. | was: anonymous263.00000000000000000000000000000000000000
    293 => ['name' => 'Salvacion Guerrero', 'username' => 'salvacion_guerrero'],
    // ANON-264. | was: anonymous264.00000000000000000000000000000000000000
    294 => ['name' => 'Josefina Manio', 'username' => 'josefina_manio'],
    // ANON-265. | was: anonymous265.00000000000000000000000000000000000000
    295 => ['name' => 'Milagros Katipunan', 'username' => 'milagros_katipunan'],
    // ANON-266. | was: anonymous266.00000000000000000000000000000000000000
    296 => ['name' => 'Jocelyn Guerrero', 'username' => 'jocelyn_guerrero'],
    // ANON-267. | was: anonymous267.00000000000000000000000000000000000000
    297 => ['name' => 'Carmela Tinio', 'username' => 'carmela_tinio'],
    // ANON-268. | was: anonymous268.00000000000000000000000000000000000000
    298 => ['name' => 'Quirina Ablaza', 'username' => 'quirina_ablaza'],
    // ANON-269. | was: anonymous269.00000000000000000000000000000000000000
    299 => ['name' => 'Emmeline Ureta', 'username' => 'emmeline_ureta'],
    // ANON-270. | was: anonymous270.00000000000000000000000000000000000000
    300 => ['name' => 'Anita Tolentino', 'username' => 'anita_tolentino'],
    // ANON-271. | was: anonymous271.00000000000000000000000000000000000000
    301 => ['name' => 'Narcisa Soriano', 'username' => 'narcisa_soriano'],
    // ANON-272. | was: anonymous272.00000000000000000000000000000000000000
    302 => ['name' => 'Fedelina Lim', 'username' => 'fedelina_lim'],
    // ANON-273. | was: anonymous273.00000000000000000000000000000000000000
    303 => ['name' => 'Patricia Ablaza', 'username' => 'patricia_ablaza'],
    // ANON-274. | was: anonymous274.00000000000000000000000000000000000000
    304 => ['name' => 'Luz Lim', 'username' => 'luz_lim'],
    // ANON-275. | was: anonymous275.00000000000000000000000000000000000000
    305 => ['name' => 'Ursula Pagcaliwagan', 'username' => 'ursula_pagcaliwagan'],
    // ANON-276. | was: anonymous276.00000000000000000000000000000000000000
    306 => ['name' => 'Dalisay Ablaza', 'username' => 'dalisay_ablaza'],
    // ANON-277. | was: anonymous277.00000000000000000000000000000000000000
    307 => ['name' => 'Fedelina Katipunan', 'username' => 'fedelina_katipunan'],
    // ANON-278. | was: anonymous278.00000000000000000000000000000000000000
    308 => ['name' => 'Narcisa Tamayo', 'username' => 'narcisa_tamayo'],
    // ANON-279. | was: anonymous279.00000000000000000000000000000000000000
    309 => ['name' => 'Genoveva Magno', 'username' => 'genoveva_magno'],
    // ANON-280. | was: anonymous280.00000000000000000000000000000000000000
    310 => ['name' => 'Imelda Soriano', 'username' => 'imelda_soriano'],
    // ANON-281. | was: anonymous281.00000000000000000000000000000000000000
    311 => ['name' => 'Dalisay Obsena', 'username' => 'dalisay_obsena'],
    // ANON-282. | was: anonymous282.00000000000000000000000000000000000000
    312 => ['name' => 'Jocelyn Villafuerte', 'username' => 'jocelyn_villafuerte'],
    // ANON-283. | was: anonymous283.00000000000000000000000000000000000000
    313 => ['name' => 'Olympia Mendoza', 'username' => 'olympia_mendoza'],
    // ANON-284. | was: anonymous284.00000000000000000000000000000000000000
    314 => ['name' => 'Melinda Ibarra', 'username' => 'melinda_ibarra'],
    // ANON-285. | was: anonymous285.00000000000000000000000000000000000000
    315 => ['name' => 'Erlinda De Leon', 'username' => 'erlinda_de_leon'],
    // ANON-286. | was: anonymous286.00000000000000000000000000000000000000
    316 => ['name' => 'Hilda Galang', 'username' => 'hilda_galang'],
    // ANON-287. | was: anonymous287.00000000000000000000000000000000000000
    317 => ['name' => 'Soledad Dela Torre', 'username' => 'soledad_dela_torre'],
    // ANON-288. | was: anonymous288.00000000000000000000000000000000000000
    318 => ['name' => 'Irene Pascual', 'username' => 'irene_pascual'],
    // ANON-289. | was: anonymous289.00000000000000000000000000000000000000
    319 => ['name' => 'Erlinda Garcia', 'username' => 'erlinda_garcia'],
    // ANON-290. | was: anonymous290.00000000000000000000000000000000000000
    320 => ['name' => 'Jovita Dela Cruz', 'username' => 'jovita_dela_cruz'],
    // ANON-291. | was: anonymous291.00000000000000000000000000000000000000
    321 => ['name' => 'Charito Pagcaliwagan', 'username' => 'charito_pagcaliwagan'],
    // ANON-292. | was: anonymous292.00000000000000000000000000000000000000
    322 => ['name' => 'Perla Ramirez', 'username' => 'perla_ramirez'],
    // ANON-293. | was: anonymous293.00000000000000000000000000000000000000
    323 => ['name' => 'Bernadette Ilagan', 'username' => 'bernadette_ilagan'],
    // ANON-294. | was: anonymous294.00000000000000000000000000000000000000
    324 => ['name' => 'Soledad Hontiveros', 'username' => 'soledad_hontiveros'],
    // ANON-295. | was: anonymous295.00000000000000000000000000000000000000
    325 => ['name' => 'Queenie Quizon', 'username' => 'queenie_quizon'],
    // ANON-296. | was: anonymous296.00000000000000000000000000000000000000
    326 => ['name' => 'Lolita Quezon', 'username' => 'lolita_quezon'],
    // ANON-297. | was: anonymous297.00000000000000000000000000000000000000
    327 => ['name' => 'Norma Garcia', 'username' => 'norma_garcia'],
    // ANON-298. | was: anonymous298.00000000000000000000000000000000000000
    328 => ['name' => 'Remedios Espinosa', 'username' => 'remedios_espinosa'],
    // ANON-299. | was: anonymous299.00000000000000000000000000000000000000
    329 => ['name' => 'Nenita Wenceslao', 'username' => 'nenita_wenceslao'],
    // ANON-300. | was: anonymous300.00000000000000000000000000000000000000
    330 => ['name' => 'Conception Mendoza', 'username' => 'conception_mendoza'],
    // ANON-301. | was: anonymous301.00000000000000000000000000000000000000
    331 => ['name' => 'Jocelyn Bustamante', 'username' => 'jocelyn_bustamante'],
    // ANON-302. | was: anonymous302.00000000000000000000000000000000000000
    332 => ['name' => 'Gloria Valdez', 'username' => 'gloria_valdez'],
    // ANON-303. | was: anonymous303.00000000000000000000000000000000000000
    333 => ['name' => 'Tessie Manalo', 'username' => 'tessie_manalo'],
    // ANON-304. | was: anonymous304.00000000000000000000000000000000000000
    334 => ['name' => 'Gloria Cabanero', 'username' => 'gloria_cabanero'],
    // ANON-305. | was: anonymous305.00000000000000000000000000000000000000
    335 => ['name' => 'Queenie Gonzales', 'username' => 'queenie_gonzales'],
    // ANON-306. | was: anonymous306.00000000000000000000000000000000000000
    336 => ['name' => 'Amelia Viray', 'username' => 'amelia_viray'],
    // ANON-307. | was: anonymous307.00000000000000000000000000000000000000
    337 => ['name' => 'Lorena Macaraeg', 'username' => 'lorena_macaraeg'],
    // ANON-308. | was: anonymous308.00000000000000000000000000000000000000
    338 => ['name' => 'Lorna Platon', 'username' => 'lorna_platon'],
    // ANON-309. | was: anonymous309.00000000000000000000000000000000000000
    339 => ['name' => 'Gloria Santos', 'username' => 'gloria_santos'],
    // ANON-310. | was: anonymous310.00000000000000000000000000000000000000
    340 => ['name' => 'Rhea Quezon', 'username' => 'rhea_quezon'],
    // ANON-311. | was: anonymous311.00000000000000000000000000000000000000
    341 => ['name' => 'Xiomara Tamayo', 'username' => 'xiomara_tamayo'],
    // ANON-312. | was: anonymous312.00000000000000000000000000000000000000
    342 => ['name' => 'Ursula Katipunan', 'username' => 'ursula_katipunan'],
    // ANON-313. | was: anonymous313.00000000000000000000000000000000000000
    343 => ['name' => 'Margarita Lagman', 'username' => 'margarita_lagman'],
    // ANON-314. | was: anonymous314.00000000000000000000000000000000000000
    344 => ['name' => 'Gloria Ramirez', 'username' => 'gloria_ramirez'],
    // ANON-315. | was: anonymous315.00000000000000000000000000000000000000
    345 => ['name' => 'Yasmin Katipunan', 'username' => 'yasmin_katipunan'],
    // ANON-316. | was: anonymous316.00000000000000000000000000000000000000
    346 => ['name' => 'Ursulina Pagcaliwagan', 'username' => 'ursulina_pagcaliwagan'],
    // ANON-317. | was: anonymous317.00000000000000000000000000000000000000
    347 => ['name' => 'Emmeline Manalo', 'username' => 'emmeline_manalo'],
    // ANON-318. | was: anonymous318.00000000000000000000000000000000000000
    348 => ['name' => 'Jovita Castillo', 'username' => 'jovita_castillo'],
    // ANON-319. | was: anonymous319.00000000000000000000000000000000000000
    349 => ['name' => 'Salud Domingo', 'username' => 'salud_domingo'],
    // ANON-320. | was: anonymous320.00000000000000000000000000000000000000
    350 => ['name' => 'Pamela Dela Torre', 'username' => 'pamela_dela_torre'],
    // ANON-321. | was: anonymous321.00000000000000000000000000000000000000
    351 => ['name' => 'Kristina Ablaza', 'username' => 'kristina_ablaza'],
    // ANON-322. | was: anonymous322.00000000000000000000000000000000000000
    352 => ['name' => 'Nilda Quizon', 'username' => 'nilda_quizon'],
    // ANON-323. | was: anonymous323.00000000000000000000000000000000000000
    353 => ['name' => 'Ofelia Salazar', 'username' => 'ofelia_salazar'],
    // ANON-324. | was: anonymous324.00000000000000000000000000000000000000
    354 => ['name' => 'Vera Diaz', 'username' => 'vera_diaz'],
    // ANON-325. | was: anonymous325.00000000000000000000000000000000000000
    355 => ['name' => 'Lorena Baguio', 'username' => 'lorena_baguio'],
    // ANON-326. | was: anonymous326.00000000000000000000000000000000000000
    356 => ['name' => 'Kristina Villafuerte', 'username' => 'kristina_villafuerte'],
    // ANON-327. | was: anonymous327.00000000000000000000000000000000000000
    357 => ['name' => 'Lolita Ureta', 'username' => 'lolita_ureta'],
    // ANON-328. | was: anonymous328.00000000000000000000000000000000000000
    358 => ['name' => 'Salvacion Lim', 'username' => 'salvacion_lim'],
    // ANON-329. | was: anonymous329.00000000000000000000000000000000000000
    359 => ['name' => 'Diana Umali', 'username' => 'diana_umali'],
    // ANON-330. | was: anonymous330.00000000000000000000000000000000000000
    360 => ['name' => 'Felicidad Quizon', 'username' => 'felicidad_quizon'],
    // ANON-331. | was: anonymous331.00000000000000000000000000000000000000
    361 => ['name' => 'Vera Santos', 'username' => 'vera_santos'],
    // ANON-332. | was: anonymous332.00000000000000000000000000000000000000
    362 => ['name' => 'Felicidad Navarro', 'username' => 'felicidad_navarro'],
    // ANON-333. | was: anonymous333.00000000000000000000000000000000000000
    363 => ['name' => 'Josefina Dalisay', 'username' => 'josefina_dalisay'],
    // ANON-334. | was: anonymous334.00000000000000000000000000000000000000
    364 => ['name' => 'Dalisay Wenceslao', 'username' => 'dalisay_wenceslao'],
    // ANON-335. | was: anonymous335.00000000000000000000000000000000000000
    365 => ['name' => 'Celestina Imperio', 'username' => 'celestina_imperio'],
    // ANON-336. | was: anonymous336.00000000000000000000000000000000000000
    366 => ['name' => 'Nilda Hilario', 'username' => 'nilda_hilario'],
    // ANON-337. | was: anonymous337.00000000000000000000000000000000000000
    367 => ['name' => 'Hazel Evangelista', 'username' => 'hazel_evangelista'],
    // ANON-338. | was: anonymous338.00000000000000000000000000000000000000
    368 => ['name' => 'Lolita Santiago', 'username' => 'lolita_santiago'],
    // ANON-339. | was: anonymous339.00000000000000000000000000000000000000
    369 => ['name' => 'Queenie Ibarra', 'username' => 'queenie_ibarra'],
    // ANON-340. | was: anonymous340.00000000000000000000000000000000000000
    370 => ['name' => 'Elvira Tamayo', 'username' => 'elvira_tamayo'],
    // ANON-341. | was: anonymous341.00000000000000000000000000000000000000
    371 => ['name' => 'Estrella Jacinto', 'username' => 'estrella_jacinto'],
    // ANON-342. | was: anonymous342.00000000000000000000000000000000000000
    372 => ['name' => 'Rowena Navarro', 'username' => 'rowena_navarro'],
    // ANON-343. | was: anonymous343.00000000000000000000000000000000000000
    373 => ['name' => 'Queenie Jacinto', 'username' => 'queenie_jacinto'],
    // ANON-344. | was: anonymous344.00000000000000000000000000000000000000
    374 => ['name' => 'Gertrudes Estepa', 'username' => 'gertrudes_estepa'],
    // ANON-345. | was: anonymous345.00000000000000000000000000000000000000
    375 => ['name' => 'Norma Villanueva', 'username' => 'norma_villanueva'],
    // ANON-346. | was: anonymous346.00000000000000000000000000000000000000
    376 => ['name' => 'Hilda Verzosa', 'username' => 'hilda_verzosa'],
    // ANON-347. | was: anonymous347.00000000000000000000000000000000000000
    377 => ['name' => 'Florencia Larena', 'username' => 'florencia_larena'],
    // ANON-348. | was: anonymous348.00000000000000000000000000000000000000
    378 => ['name' => 'Esmeralda Hernandez', 'username' => 'esmeralda_hernandez'],
    // ANON-349. | was: anonymous349.00000000000000000000000000000000000000
    379 => ['name' => 'Genoveva De Leon', 'username' => 'genoveva_de_leon'],
    // ANON-350. | was: anonymous350.00000000000000000000000000000000000000
    380 => ['name' => 'Corazon Garcia', 'username' => 'corazon_garcia'],
    // ANON-351. | was: anonymous351.00000000000000000000000000000000000000
    381 => ['name' => 'Lorna Magno', 'username' => 'lorna_magno'],
    // ANON-352. | was: anonymous352.00000000000000000000000000000000000000
    382 => ['name' => 'Lily Dacanay', 'username' => 'lily_dacanay'],
    // ANON-353. | was: anonymous353.00000000000000000000000000000000000000
    383 => ['name' => 'Lilia Zulueta', 'username' => 'lilia_zulueta'],
    // ANON-354. | was: anonymous354.00000000000000000000000000000000000000
    384 => ['name' => 'Elisa Galang', 'username' => 'elisa_galang'],
    // ANON-355. | was: anonymous355.00000000000000000000000000000000000000
    385 => ['name' => 'Edna Feria', 'username' => 'edna_feria'],
    // ANON-356. | was: anonymous356.00000000000000000000000000000000000000
    386 => ['name' => 'Rebecca Obsena', 'username' => 'rebecca_obsena'],
    // ANON-357. | was: anonymous357.00000000000000000000000000000000000000
    387 => ['name' => 'Amelia Lacson', 'username' => 'amelia_lacson'],
    // ANON-358. | was: anonymous358.00000000000000000000000000000000000000
    388 => ['name' => 'Charito Magno', 'username' => 'charito_magno'],
    // ANON-359. | was: anonymous359.00000000000000000000000000000000000000
    389 => ['name' => 'Corazon Imperio', 'username' => 'corazon_imperio'],
    // ANON-360. | was: anonymous360.00000000000000000000000000000000000000
    390 => ['name' => 'Nena Santos', 'username' => 'nena_santos'],
    // ANON-361. | was: anonymous361.00000000000000000000000000000000000000
    391 => ['name' => 'Raquel Verzosa', 'username' => 'raquel_verzosa'],
    // ANON-362. | was: anonymous362.00000000000000000000000000000000000000
    392 => ['name' => 'Narcisa Zulueta', 'username' => 'narcisa_zulueta'],
    // ANON-363. | was: anonymous363.00000000000000000000000000000000000000
    393 => ['name' => 'Carmela Camacho', 'username' => 'carmela_camacho'],
    // ANON-364. | was: anonymous364.00000000000000000000000000000000000000
    394 => ['name' => 'Erlinda Lim', 'username' => 'erlinda_lim'],
    // ANON-365. | was: anonymous365.00000000000000000000000000000000000000
    395 => ['name' => 'Fedelina Obsena', 'username' => 'fedelina_obsena'],
    // ANON-366. | was: anonymous366.00000000000000000000000000000000000000
    396 => ['name' => 'Rhea Hilario', 'username' => 'rhea_hilario'],
    // ANON-367. | was: anonymous367.00000000000000000000000000000000000000
    397 => ['name' => 'Grace Tinio', 'username' => 'grace_tinio'],
    // ANON-368. | was: anonymous368.00000000000000000000000000000000000000
    398 => ['name' => 'Milagros Castillo', 'username' => 'milagros_castillo'],
    // ANON-369. | was: anonymous369.00000000000000000000000000000000000000
    399 => ['name' => 'Jenny Morales', 'username' => 'jenny_morales'],
    // ANON-370. | was: anonymous370.00000000000000000000000000000000000000
    400 => ['name' => 'Fedelina Hilario', 'username' => 'fedelina_hilario'],
    // ANON-371. | was: anonymous371.00000000000000000000000000000000000000
    401 => ['name' => 'Kristina Perez', 'username' => 'kristina_perez'],
    // ANON-372. | was: anonymous372.00000000000000000000000000000000000000
    402 => ['name' => 'Wanda Bustamante', 'username' => 'wanda_bustamante'],
    // ANON-373. | was: anonymous373.00000000000000000000000000000000000000
    403 => ['name' => 'Raquel Evangelista', 'username' => 'raquel_evangelista'],
    // ANON-374. | was: anonymous374.00000000000000000000000000000000000000
    404 => ['name' => 'Diana Ricofort', 'username' => 'diana_ricofort'],
    // ANON-375. | was: anonymous375.00000000000000000000000000000000000000
    405 => ['name' => 'Raquel Macapagal', 'username' => 'raquel_macapagal'],
    // ANON-376. | was: anonymous376.00000000000000000000000000000000000000
    406 => ['name' => 'Filipina Saguisag', 'username' => 'filipina_saguisag'],
    // ANON-377. | was: anonymous377.00000000000000000000000000000000000000
    407 => ['name' => 'Ina Soriano', 'username' => 'ina_soriano'],
    // ANON-378. | was: anonymous378.00000000000000000000000000000000000000
    408 => ['name' => 'Violeta Ibarra', 'username' => 'violeta_ibarra'],
    // ANON-379. | was: anonymous379.00000000000000000000000000000000000000
    409 => ['name' => 'Ina Espinosa', 'username' => 'ina_espinosa'],
    // ANON-380. | was: anonymous380.00000000000000000000000000000000000000
    410 => ['name' => 'Wilhelmina Magno', 'username' => 'wilhelmina_magno'],
    // ANON-381. | was: anonymous381.00000000000000000000000000000000000000
    411 => ['name' => 'Karla Gatmaitan', 'username' => 'karla_gatmaitan'],
    // ANON-382. | was: anonymous382.00000000000000000000000000000000000000
    412 => ['name' => 'Perla Zulueta', 'username' => 'perla_zulueta'],
    // ANON-383. | was: anonymous383.00000000000000000000000000000000000000
    413 => ['name' => 'Carmela Ongsiako', 'username' => 'carmela_ongsiako'],
    // ANON-384. | was: anonymous384.00000000000000000000000000000000000000
    414 => ['name' => 'Nancy Feria', 'username' => 'nancy_feria'],
    // ANON-385. | was: anonymous385.00000000000000000000000000000000000000
    415 => ['name' => 'Hazel Aquino', 'username' => 'hazel_aquino'],
    // ANON-386. | was: anonymous386.00000000000000000000000000000000000000
    416 => ['name' => 'Rebecca Navarro', 'username' => 'rebecca_navarro'],
    // ANON-387. | was: anonymous387.00000000000000000000000000000000000000
    417 => ['name' => 'Florencia Rivera', 'username' => 'florencia_rivera'],
    // ANON-388. | was: anonymous388.00000000000000000000000000000000000000
    418 => ['name' => 'Norma Manalo', 'username' => 'norma_manalo'],
    // ANON-389. | was: anonymous389.00000000000000000000000000000000000000
    419 => ['name' => 'Rowena Morales', 'username' => 'rowena_morales'],
    // ANON-390. | was: anonymous390.00000000000000000000000000000000000000
    420 => ['name' => 'Anita Pascual', 'username' => 'anita_pascual'],
    // ANON-391. | was: anonymous391.00000000000000000000000000000000000000
    421 => ['name' => 'Amelia Natividad', 'username' => 'amelia_natividad'],
    // ANON-392. | was: anonymous392.00000000000000000000000000000000000000
    422 => ['name' => 'Anita Platon', 'username' => 'anita_platon'],
    // ANON-393. | was: anonymous393.00000000000000000000000000000000000000
    423 => ['name' => 'Caridad Ramirez', 'username' => 'caridad_ramirez'],
    // ANON-394. | was: anonymous394.00000000000000000000000000000000000000
    424 => ['name' => 'Edna Ablaza', 'username' => 'edna_ablaza'],
    // ANON-395. | was: anonymous395.00000000000000000000000000000000000000
    425 => ['name' => 'Norma Recto', 'username' => 'norma_recto'],
    // ANON-396. | was: anonymous396.00000000000000000000000000000000000000
    426 => ['name' => 'Katrina Baguio', 'username' => 'katrina_baguio'],
    // ANON-397. | was: anonymous397.00000000000000000000000000000000000000
    427 => ['name' => 'Maricel Mercado', 'username' => 'maricel_mercado'],
    // ANON-398. | was: anonymous398.00000000000000000000000000000000000000
    428 => ['name' => 'Josie Baguio', 'username' => 'josie_baguio'],
    // ANON-399. | was: anonymous399.00000000000000000000000000000000000000
    429 => ['name' => 'Amelia Zulueta', 'username' => 'amelia_zulueta'],
    // ANON-400. | was: anonymous400.00000000000000000000000000000000000000
    430 => ['name' => 'Zelda Obsena', 'username' => 'zelda_obsena'],
    // ANON-401. | was: anonymous401.00000000000000000000000000000000000000
    431 => ['name' => 'Rosa Yap', 'username' => 'rosa_yap'],
    // ANON-402. | was: anonymous402.00000000000000000000000000000000000000
    432 => ['name' => 'Yolanda Villafuerte', 'username' => 'yolanda_villafuerte'],
    // ANON-403. | was: anonymous403.00000000000000000000000000000000000000
    433 => ['name' => 'Mylene Santos', 'username' => 'mylene_santos'],
    // ANON-404. | was: anonymous404.00000000000000000000000000000000000000
    434 => ['name' => 'Xiomara Guerrero', 'username' => 'xiomara_guerrero'],
    // ANON-405. | was: anonymous405.00000000000000000000000000000000000000
    435 => ['name' => 'Felicidad Verzosa', 'username' => 'felicidad_verzosa'],
    // ANON-406. | was: anonymous406.00000000000000000000000000000000000000
    436 => ['name' => 'Amelia Baguio', 'username' => 'amelia_baguio'],
    // ANON-407. | was: anonymous407.00000000000000000000000000000000000000
    437 => ['name' => 'Tina Ureta', 'username' => 'tina_ureta'],
    // ANON-408. | was: anonymous408.00000000000000000000000000000000000000
    438 => ['name' => 'Ursula Recto', 'username' => 'ursula_recto'],
    // ANON-409. | was: anonymous409.00000000000000000000000000000000000000
    439 => ['name' => 'Fedelina Cabanero', 'username' => 'fedelina_cabanero'],
    // ANON-410. | was: anonymous410.00000000000000000000000000000000000000
    440 => ['name' => 'Raquel Hernandez', 'username' => 'raquel_hernandez'],
    // ANON-411. | was: anonymous411.00000000000000000000000000000000000000
    441 => ['name' => 'Nancy Hernandez', 'username' => 'nancy_hernandez'],
    // ANON-412. | was: anonymous412.00000000000000000000000000000000000000
    442 => ['name' => 'Imelda Dalisay', 'username' => 'imelda_dalisay'],
    // ANON-413. | was: anonymous413.00000000000000000000000000000000000000
    443 => ['name' => 'Nancy Garcia', 'username' => 'nancy_garcia'],
    // ANON-414. | was: anonymous414.00000000000000000000000000000000000000
    444 => ['name' => 'Amelia Madriaga', 'username' => 'amelia_madriaga'],
    // ANON-415. | was: anonymous415.00000000000000000000000000000000000000
    445 => ['name' => 'Kristina Larena', 'username' => 'kristina_larena'],
    // ANON-416. | was: anonymous416.00000000000000000000000000000000000000
    446 => ['name' => 'Fedelina Quizon', 'username' => 'fedelina_quizon'],
    // ANON-417. | was: anonymous417.00000000000000000000000000000000000000
    447 => ['name' => 'Salud Ocampo', 'username' => 'salud_ocampo'],
    // ANON-418. | was: anonymous418.00000000000000000000000000000000000000
    448 => ['name' => 'Karla Hilario', 'username' => 'karla_hilario'],
    // ANON-419. | was: anonymous419.00000000000000000000000000000000000000
    449 => ['name' => 'Ursulina Diaz', 'username' => 'ursulina_diaz'],
    // ANON-420. | was: anonymous420.00000000000000000000000000000000000000
    450 => ['name' => 'Emmeline Quezon', 'username' => 'emmeline_quezon'],
    // ANON-421. | was: anonymous421.00000000000000000000000000000000000000
    451 => ['name' => 'Luz Enriquez', 'username' => 'luz_enriquez'],
    // ANON-422. | was: anonymous422.00000000000000000000000000000000000000
    452 => ['name' => 'Aileen Umali', 'username' => 'aileen_umali'],
    // ANON-423. | was: anonymous423.00000000000000000000000000000000000000
    453 => ['name' => 'Jovita Garcia', 'username' => 'jovita_garcia'],
    // ANON-424. | was: anonymous424.00000000000000000000000000000000000000
    454 => ['name' => 'Rowena Hontiveros', 'username' => 'rowena_hontiveros'],
    // ANON-425. | was: anonymous425.00000000000000000000000000000000000000
    455 => ['name' => 'Filipina Morales', 'username' => 'filipina_morales'],
    // ANON-426. | was: anonymous426.00000000000000000000000000000000000000
    456 => ['name' => 'Narcisa Hilario', 'username' => 'narcisa_hilario'],
    // ANON-427. | was: anonymous427.00000000000000000000000000000000000000
    457 => ['name' => 'Evelyn Flores', 'username' => 'evelyn_flores'],
    // ANON-428. | was: anonymous428.00000000000000000000000000000000000000
    458 => ['name' => 'Conception Villafuerte', 'username' => 'conception_villafuerte'],
    // ANON-429. | was: anonymous429.00000000000000000000000000000000000000
    459 => ['name' => 'Nilda Santiago', 'username' => 'nilda_santiago'],
    // ANON-430. | was: anonymous430.00000000000000000000000000000000000000
    460 => ['name' => 'Jenny Buenaventura', 'username' => 'jenny_buenaventura'],
    // ANON-431. | was: anonymous431.00000000000000000000000000000000000000
    461 => ['name' => 'Resurreccion Yap', 'username' => 'resurreccion_yap'],
    // ANON-432. | was: anonymous432.00000000000000000000000000000000000000
    462 => ['name' => 'Patricia Villafuerte', 'username' => 'patricia_villafuerte'],
    // ANON-433. | was: anonymous433.00000000000000000000000000000000000000
    463 => ['name' => 'Perla Verzosa', 'username' => 'perla_verzosa'],
    // ANON-434. | was: anonymous434.00000000000000000000000000000000000000
    464 => ['name' => 'Yasmin Baguio', 'username' => 'yasmin_baguio'],
    // ANON-435. | was: anonymous435.00000000000000000000000000000000000000
    465 => ['name' => 'Adoracion Bautista', 'username' => 'adoracion_bautista'],
    // ANON-436. | was: anonymous436.00000000000000000000000000000000000000
    466 => ['name' => 'Lily Cruz', 'username' => 'lily_cruz'],
    // ANON-437. | was: anonymous437.00000000000000000000000000000000000000
    467 => ['name' => 'Violeta Recto', 'username' => 'violeta_recto'],
    // ANON-438. | was: anonymous438.00000000000000000000000000000000000000
    468 => ['name' => 'Grace Valdez', 'username' => 'grace_valdez'],
    // ANON-439. | was: anonymous439.00000000000000000000000000000000000000
    469 => ['name' => 'Erlinda Imperio', 'username' => 'erlinda_imperio'],
    // ANON-440. | was: anonymous440.00000000000000000000000000000000000000
    470 => ['name' => 'Cristina Abaya', 'username' => 'cristina_abaya'],
    // ANON-441. | was: anonymous441.00000000000000000000000000000000000000
    471 => ['name' => 'Isabelita Torres', 'username' => 'isabelita_torres'],
    // ANON-442. | was: anonymous442.00000000000000000000000000000000000000
    472 => ['name' => 'Wilhelmina Garcia', 'username' => 'wilhelmina_garcia'],
    // ANON-443. | was: anonymous443.00000000000000000000000000000000000000
    473 => ['name' => 'Yasmin Magno', 'username' => 'yasmin_magno'],
    // ANON-444. | was: anonymous444.00000000000000000000000000000000000000
    474 => ['name' => 'Margarita Cruz', 'username' => 'margarita_cruz'],
    // ANON-445. | was: anonymous445.00000000000000000000000000000000000000
    475 => ['name' => 'Emmeline Castro', 'username' => 'emmeline_castro'],
    // ANON-446. | was: anonymous446.00000000000000000000000000000000000000
    476 => ['name' => 'Florencia Diaz', 'username' => 'florencia_diaz'],
    // ANON-447. | was: anonymous447.00000000000000000000000000000000000000
    477 => ['name' => 'Lily Katipunan', 'username' => 'lily_katipunan'],
    // ANON-448. | was: anonymous448.00000000000000000000000000000000000000
    478 => ['name' => 'Remedios Umali', 'username' => 'remedios_umali'],
    // ANON-449. | was: anonymous449.00000000000000000000000000000000000000
    479 => ['name' => 'Yasmin Santiago', 'username' => 'yasmin_santiago'],
    // ANON-450. | was: anonymous450.00000000000000000000000000000000000000
    480 => ['name' => 'Katrina Obsena', 'username' => 'katrina_obsena'],
    // ANON-451. | was: anonymous451.00000000000000000000000000000000000000
    481 => ['name' => 'Adoracion Zulueta', 'username' => 'adoracion_zulueta'],
    // ANON-452. | was: anonymous452.00000000000000000000000000000000000000
    482 => ['name' => 'Wilma Ocampo', 'username' => 'wilma_ocampo'],
    // ANON-453. | was: anonymous453.00000000000000000000000000000000000000
    483 => ['name' => 'Celestina Cabanero', 'username' => 'celestina_cabanero'],
    // ANON-454. | was: anonymous454.00000000000000000000000000000000000000
    484 => ['name' => 'Violeta Quimpo', 'username' => 'violeta_quimpo'],
    // ANON-455. | was: anonymous455.00000000000000000000000000000000000000
    485 => ['name' => 'Remedios Abaya', 'username' => 'remedios_abaya'],
    // ANON-456. | was: anonymous456.00000000000000000000000000000000000000
    486 => ['name' => 'Hilda Soriano', 'username' => 'hilda_soriano'],
    // ANON-457. | was: anonymous457.00000000000000000000000000000000000000
    487 => ['name' => 'Esmeralda Lopez', 'username' => 'esmeralda_lopez'],
    // ANON-458. | was: anonymous458.00000000000000000000000000000000000000
    488 => ['name' => 'Zelda Garcia', 'username' => 'zelda_garcia'],
    // ANON-459. | was: anonymous459.00000000000000000000000000000000000000
    489 => ['name' => 'Vivian Dela Torre', 'username' => 'vivian_dela_torre'],
    // ANON-460. | was: anonymous460.00000000000000000000000000000000000000
    490 => ['name' => 'Yolanda Larena', 'username' => 'yolanda_larena'],
    // ANON-461. | was: anonymous461.00000000000000000000000000000000000000
    491 => ['name' => 'Teodora Villafuerte', 'username' => 'teodora_villafuerte'],
    // ANON-462. | was: anonymous462.00000000000000000000000000000000000000
    492 => ['name' => 'Bebelyn Quimpo', 'username' => 'bebelyn_quimpo'],
    // ANON-463. | was: anonymous463.00000000000000000000000000000000000000
    493 => ['name' => 'Margarita Aquino', 'username' => 'margarita_aquino'],
    // ANON-464. | was: anonymous464.00000000000000000000000000000000000000
    494 => ['name' => 'Aileen Natividad', 'username' => 'aileen_natividad'],
    // ANON-465. | was: anonymous465.00000000000000000000000000000000000000
    495 => ['name' => 'Josie Feria', 'username' => 'josie_feria'],
    // ANON-466. | was: anonymous466.00000000000000000000000000000000000000
    496 => ['name' => 'Milagros Lopez', 'username' => 'milagros_lopez'],
    // ANON-467. | was: anonymous467.00000000000000000000000000000000000000
    497 => ['name' => 'Ofelia Zulueta', 'username' => 'ofelia_zulueta'],
    // ANON-468. | was: anonymous468.00000000000000000000000000000000000000
    498 => ['name' => 'Felicidad Manio', 'username' => 'felicidad_manio'],
    // ANON-469. | was: anonymous469.00000000000000000000000000000000000000
    499 => ['name' => 'Belen Castillo', 'username' => 'belen_castillo'],
    // ANON-470. | was: anonymous470.00000000000000000000000000000000000000
    500 => ['name' => 'Elvira Umali', 'username' => 'elvira_umali'],

];

// ──────────────────────────────────────────────
// RUN UPDATES
// ──────────────────────────────────────────────
$updated  = 0;
$skipped  = 0;
$errors   = [];

$stmt = $pdo->prepare("UPDATE `patients` SET `name` = :name WHERE `id` = :id");

foreach ($updates as $patient_id => $data) {
    try {
        $stmt->execute([
            ':name' => $data['name'],
            ':id'   => $patient_id,
        ]);
        if ($stmt->rowCount() > 0) {
            $updated++;
            echo "✅ Updated patient #$patient_id → {$data['name']}\n";
        } else {
            $skipped++;
            echo "⚠️  Skipped patient #$patient_id (not found or already updated)\n";
        }
    } catch (PDOException $e) {
        $errors[] = "❌ Error on patient #$patient_id: " . $e->getMessage();
        echo end($errors) . "\n";
    }
}

// ──────────────────────────────────────────────
// SUMMARY
// ──────────────────────────────────────────────
echo "\n==========================================\n";
echo "✅ Updated : $updated\n";
echo "⚠️  Skipped : $skipped\n";
echo "❌ Errors  : " . count($errors) . "\n";
echo "==========================================\n";
echo "Done!\n";