<?php
    $today = strtotime('2024-08-19 11:08:23');

    // 上周/这周
    if ($dayType == 2) {
        // 周几 1（星期一）到 7（星期天）
        $wn = date('N', $today);
        if ($wn > 1) {
            $ws = ($wn-1);
            $monday = strtotime('-' .$ws.' day', $today);
        } else {
            $monday = $today;
        }

        // 这周
        $start = date('Y-m-d 00:00:00', $monday);
        $end = date('Y-m-d 23:59:59', $today);

        // 上周
        $startY = date('Y-m-d 00:00:00', strtotime('-7 day', $monday));
        $endY = date('Y-m-d 23:59:59', strtotime('-1 day', $monday));
    }

    // 上个月/这个月
    elseif ($dayType == 3) {
        $month = date('Y-m', $today);
        $start = date($month . '-01 00:00:00', $today);
        $end = date($month . '-d 23:59:59', $today);

        // 上个月
        $monthYt = strtotime('-1 month', $today);
        $monthY = date('Y-m', $monthYt);
        $startY = date($monthY . '-01 00:00:00');
        $monthDay = date('t', $monthYt);
        $endY = date($monthY . '-' .$monthDay.' 23:59:59');
    }
    // 昨天/今天
    else {
        $start = date('Y-m-d 00:00:00', $today);
        $end = date('Y-m-d 23:59:59', $today);

        $yes = strtotime('-1 day', $today);
        $startY = date('Y-m-d 00:00:00', $yes);
        $endY = date('Y-m-d 23:59:59', $yes);
    }

    dump($start);
    dump($end);
    dump($startY);
    dd($endY);
