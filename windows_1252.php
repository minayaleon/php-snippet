<?php 

// https://ourcodeworld.com/articles/read/772/how-to-write-a-text-file-with-ansi-encoding-western-windows-1252-in-php
// https://www.fileformat.info/info/unicode/char/0192/charset_support.htm

function createWindow1252File() {
    // Windows-1252
    //$data = "‘’€†€";

    // ISO-8859-1
    $data = "‘’€†€Q_1";
    $string_encoded = iconv(mb_detect_encoding($data), 'Windows-1252//TRANSLIT', $data );

    $fileId = uniqid();
    $filename = 'temp/' . 'cp1252_' . $fileId . '.txt';

    $file = fopen( $filename, "w+");
    fwrite($file, $string_encoded);
    fclose($file);

    return $filename;
}

function readWindow1252File($win1252File){
    $fn = fopen($win1252File,'r');
    $result = fgets($fn);
    fclose($fn);
    return $result;
}

function sanitizeCP1252_v1($input) {
    $regexEncoding = \mb_regex_encoding();
    $detectEncoding = \mb_detect_encoding($input, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
    if (in_array($detectEncoding, ['Windows-1252', 'ISO-8859-1'])) {
        \mb_regex_encoding('ISO-8859-1');
        $pattern = \mb_convert_encoding("‘|’", "cp1252", 'UTF-8');
        $replacement = \mb_convert_encoding("'", "cp1252", 'UTF-8');
        $output = \mb_ereg_replace($pattern, $replacement, $input);
        \mb_regex_encoding($regexEncoding);
        if ($output) {
            return $output;
        } else {
            return $input;
        }
    }
}

function sanitizeWindow1252($input) {
    $window1252 = [
        "\x{80}" => "€",
        "\x{83}" => "ƒ",
        "\x{84}" => "„",
        "\x{86}" => "†",
        "\x{87}" => "‡",
        "\x{88}" => "ˆ",
        "\x{89}" => "‰",
        "\x{91}|\x{92}" => "'",
        "\x{95}" => "•",
        "\x{96}" => "–",
        "\x{97}" => "—",
        "\x{98}" => "˜",
        "\x{99}" => "™",
    ];

    $regexEncoding = \mb_regex_encoding();
    $detectEncoding = \mb_detect_encoding($input, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
    if (in_array($detectEncoding, ['Windows-1252', 'ISO-8859-1'])) {
        \mb_regex_encoding('ISO-8859-1');
        foreach ($window1252 as $pattern => $replacement) {
            $output = \mb_ereg_replace($pattern, $replacement, $input);
            if (!$output) {
                break;
            }
            $input = $output;
        }
        \mb_regex_encoding($regexEncoding);
        if ($output) {
            return $output;
        } else {
            return $input;
        }
    }
}

$win1252File = createWindow1252File();
echo $win1252File . PHP_EOL;

$win1252 = readWindow1252File($win1252File);
print $win1252 . PHP_EOL;

$win1252 = sanitizeWindow1252($win1252);
print $win1252  . PHP_EOL;
