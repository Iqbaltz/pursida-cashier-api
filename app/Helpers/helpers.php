<?php

use Carbon\Carbon;

if (!function_exists('get_indo_date')) {
    function get_indo_date($tanggal)
    {
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tanggal);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
}
if (!function_exists('format_indonesia_datetime')) {
    function format_indonesia_datetime($date)
    {
        return Carbon::parse($date)->translatedFormat('d F Y H:i:s');
    }
}
if (!function_exists('format_rupiah')) {
    function format_rupiah($number)
    {
        return 'Rp' . number_format($number, 0, ',', '.') . ',-';
    }
}
