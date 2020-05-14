<?php

namespace App\Service;


class ProductDurationService
{

    private static $duration = [
        0 => '0',
        1 => '1200',
        2 => '2700',
        3 => '2700',
        4 => '5400',
        5 => '7200',
        6 => '14400',
        7 => '28800',
        8 => '57600',
        9 => '14400',
        10 => '43200',
        11 => '86400',
        12 => '172800',
        13 => '0',
        14 => '0',
        15 => '0',
        16 => '0',
        17 => '900',
        18 => '5400',
        19 => '12960',
        20 => '28800',
        21 => '36000',
        22 => '30000',
        23 => '48000',
        24 => '43200',
        25 => '60000',
        26 => '46800',
        27 => '120000',
        28 => '180000',
        29 => '57000',
        30 => '240000',
        31 => '60000',
        32 => '43200',
        33 => '72000',
        34 => '48000',
        35 => '120000',
        36 => '52800',
        37 => '162000',
        38 => '57600',
        39 => '240000',
        40 => '288000',
        41 => '330000',
        42 => '372000',
        43 => '408000',
        44 => '432000',
        91 => '172800',
        92 => '10000',
        93 => '10000',
        97 => '43200',
        45 => '0',
        46 => '0',
        47 => '0',
        48 => '0',
        49 => '0',
        50 => '0',
        51 => '0',
        52 => '0',
        53 => '0',
        54 => '0',
        55 => '0',
        56 => '0',
        57 => '0',
        58 => '0',
        59 => '0',
        60 => '0',
        61 => '0',
        62 => '0',
        63 => '0',
        64 => '0',
        65 => '0',
        66 => '0',
        67 => '0',
        68 => '0',
        69 => '0',
        70 => '0',
        71 => '0',
        72 => '0',
        73 => '0',
        74 => '0',
        75 => '0',
        76 => '0',
        77 => '0',
        78 => '0',
        79 => '0',
        80 => '0',
        81 => '0',
        82 => '0',
        83 => '0',
        84 => '0',
        85 => '0',
        86 => '0',
        87 => '0',
        88 => '0',
        89 => '0',
        90 => '0',
        94 => '0',
        95 => '0',
        96 => '0',
        98 => '0',
        99 => '0',
        100 => '0',
        101 => '0',
        102 => '0',
        103 => '0',
        104 => '44400',
        105 => '0',
        106 => '0',
        107 => '42000',
        108 => '21600',
        109 => '8100',
        110 => '129600',
        111 => '59040',
        112 => '86400',
        113 => '100800',
        114 => '32400',
        115 => '306000',
        116 => '50400',
        117 => '64800',
        118 => '28800',
        119 => '79200',
        120 => '108000',
        121 => '151200',
        122 => '75600',
        123 => '97200',
        124 => '43200',
        125 => '226800',
        126 => '384000',
        127 => '444000',
        128 => '468000',
        129 => '38400',
        130 => '1200',
        131 => '1200',
        132 => '1500',
        133 => '1500',
        134 => '1200',
        135 => '1200',
        136 => '1800',
        137 => '1800',
        138 => '1800',
        139 => '1200',
        140 => '1200',
        141 => '1500',
        142 => '1500',
        143 => '1500',
        144 => '22500',
        145 => '1200',
        146 => '1200',
        147 => '1200',
        148 => '1800',
        149 => '1200',
        150 => '2100',
        151 => '187200',
        152 => '194400',
        153 => '86400',
        154 => '115200',
        155 => '86400',
        156 => '151200',
        157 => '50400',
        158 => '36000',
        159 => '28800',
        160 => '28800',
        161 => '1200',
        162 => '1000',
        163 => '2000',
        164 => '2200',
        165 => '600',
        166 => '1300',
        167 => '2900',
        168 => '900',
        169 => '3700',
        170 => '4600',
        171 => '25200',
        172 => '16200',
        173 => '32400',
        174 => '13200',
        175 => '10800',
        176 => '18900',
        177 => '23400',
        178 => '15000',
        179 => '13800',
        180 => '20700',
        181 => '36000',
        182 => '14700',
        183 => '52800',
        184 => '21600',
        185 => '30600',
        186 => '26400',
        187 => '14400',
        188 => '0',
        189 => '0',
        200 => '14400',
        201 => '14400',
        202 => '10800',
        203 => '12600',
        204 => '21000',
        205 => '3600',
        206 => '10800',
        207 => '15000',
        208 => '17400',
        209 => '22800',
        210 => '10800',
        211 => '19500',
        212 => '7200',
        213 => '12600',
        214 => '28800',
        215 => '28800',
        216 => '28800',
        217 => '28800',
        218 => '28800',
        219 => '28800',
        220 => '86400',
        221 => '86400',
        250 => '21600',
        251 => '28800',
        252 => '36000',
        253 => '43200',
        254 => '36000',
        255 => '28800',
        256 => '21600',
        257 => '36000',
        258 => '28800',
        259 => '43200',
        260 => '21600',
        261 => '28800',
        262 => '43200',
        263 => '36000',
        264 => '21600',
        265 => '28800',
        266 => '36000',
        267 => '28800',
        268 => '21600',
        269 => '43200',
        270 => '43200',
        271 => '36000',
        272 => '28800',
        273 => '21600',
        274 => '28800',
        275 => '21600',
        276 => '36000',
        300 => '40',
        301 => '40',
        302 => '40',
        303 => '40',
        304 => '40',
        305 => '40',
        306 => '40',
        307 => '40',
        308 => '40',
        309 => '40',
        310 => '40',
        311 => '40',
        312 => '40',
        313 => '40',
        314 => '40',
        315 => '40',
        316 => '40',
        317 => '40',
        318 => '40',
        319 => '40',
        320 => '40',
        321 => '40',
        322 => '40',
        323 => '40',
        324 => '40',
        325 => '40',
        326 => '40',
        327 => '40',
        328 => '40',
        329 => '40',
        330 => '40',
        331 => '40',
        332 => '40',
        333 => '40',
        334 => '40',
        335 => '40',
        336 => '40',
        337 => '40',
        338 => '40',
        339 => '40',
        340 => '40',
        341 => '40',
        342 => '40',
        343 => '40',
        344 => '40',
        345 => '40',
        346 => '40',
        347 => '40',
        348 => '40',
        349 => '40',
        350 => '21600',
        351 => '28800',
        352 => '21600',
        353 => '43200',
        354 => '43200',
        355 => '79200',
        356 => '172800',
        357 => '28800',
        358 => '86400',
        359 => '28800',
        360 => '86400',
        361 => '10800',
        400 => '40',
        401 => '40',
        402 => '40',
        403 => '40',
        450 => '1100',
        451 => '1300',
        452 => '2100',
        453 => '2200',
        454 => '2600',
        455 => '2100',
        456 => '2100',
        457 => '1100',
        458 => '1500',
        459 => '1300',
        460 => '1900',
        461 => '1000',
        462 => '1300',
        463 => '1100',
        464 => '1000',
        465 => '1200',
        466 => '1500',
        467 => '1400',
        468 => '900',
        469 => '1700',
        470 => '1000',
        471 => '1000',
        472 => '1100',
        473 => '800',
        474 => '1200',
        475 => '1300',
        476 => '1500',
        477 => '1200',
        478 => '1200',
        479 => '1600',
        480 => '2000',
        481 => '1600',
        482 => '1600',
        483 => '900',
        550 => '0',
        551 => '0',
        552 => '0',
        553 => '0',
        600 => '144',
        601 => '108',
        602 => '432',
        603 => '288',
        604 => '144',
        605 => '204',
        606 => '360',
        607 => '84',
        608 => '228',
        609 => '288',
        630 => '144',
        631 => '126',
        632 => '120',
        633 => '120',
        634 => '132',
        635 => '252',
        636 => '108',
        637 => '96',
        638 => '168',
        639 => '360',
        660 => '108',
        661 => '204',
        662 => '360',
        663 => '228',
        664 => '288',
        665 => '156',
        666 => '180',
        667 => '432',
        668 => '432',
        669 => '168',
        700 => '43200',
        701 => '21600',
        702 => '43200',
        703 => '155520',
        704 => '28800',
        705 => '18000',
        706 => '36000',
        707 => '21600',
        708 => '14400',
        709 => '86400',
        750 => '28800',
        751 => '43200',
        752 => '86400',
        753 => '36000',
        754 => '129600',
        755 => '86400',
        756 => '151200',
        757 => '72000',
        758 => '115200',
        759 => '64800',
    ];

    public static function getProductDurationByPid($pid)
    {
        if (isset(self::$duration[$pid])) {
            return self::$duration[$pid];
        }
        return 0;
    }
}