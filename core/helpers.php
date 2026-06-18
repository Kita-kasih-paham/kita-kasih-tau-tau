<?php

/**
 * Format number with dot as thousand separator (Indonesian style)
 * e.g. 1000 → 1.000
 */
function fmt(int|float $n): string
{
    return number_format($n, 0, ',', '.');
}

/**
 * Format date to Indonesian long format with day name
 * e.g. 2026-04-17 → Jumat, 17 April 2026
 */
function fmtDate(string $date): string
{
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $hari = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu'
    ];
    $ts = strtotime($date);
    if (!$ts)
        return $date;
    return $hari[(int) date('w', $ts)] . ', '
        . (int) date('d', $ts) . ' '
        . $bulan[(int) date('n', $ts)] . ' '
        . date('Y', $ts);
}
