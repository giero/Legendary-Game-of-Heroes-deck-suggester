<?php
/**
 * @link http://gist.github.com/385876
 *
 * @param $filename
 * @param $delimiter
 *
 * @return array|bool
 */
function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename)) {
        die('Cannot read the file!');
    }

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                if (count($header) != count($row)) {
                    var_dump('Mismatched columns!', $header, $row);
                    die();
                }
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }

    $dbData = [];
    $legendMap = [
        4 => 2,
        5 => 3,
        6 => 4,
    ];

    foreach ($data as $row) {
        $rarity = strlen($row['stars']);
        $affinity = ucfirst($row['affinity']);
        $dbData[] = [
            'name' => $row['name'],
            'affinity' => $affinity,
            'type' => $row['class'],
            'species' => $row['race'],
            'attack' => '',
            'recovery' => '',
            'health' => '',
            'rarity' => $rarity,
            'eventSkills' => $row['race'] == 'Legend' && $rarity > 3
                ? [
                    $legendMap[$rarity].'x Slayer',
                    $legendMap[$rarity].'x Bounty Hunter',
                    $legendMap[$rarity].'x '.$affinity.' Commander',
                ]
                : [],
            'defenderSkill' => $row['defender skill'],
            'counterSkill' => $row['counter skill'],
            'leaderAbility' => $row['leader ability'],
            'combatAbility' => $row['combat ability'],
            'evolveFrom' => '',
            'evolveTo' => '',
        ];
    }

    return $dbData;
}

echo json_encode(csvToArray('heroes_all.tsv', "\t"), JSON_PRETTY_PRINT);