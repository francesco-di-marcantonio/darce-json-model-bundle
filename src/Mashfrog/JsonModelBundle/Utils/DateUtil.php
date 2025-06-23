<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Utils;

use DateTime;
use Exception;

final class DateUtil
{
    public static function recognizeString(string $stringDate): ?DateTime
    {
        if(strlen($stringDate) < 6){
            return null;
        }

        if (is_numeric($stringDate) === true) {
            return null;
        }

        $timestamp = strtotime($stringDate);
        if($timestamp === false){
            return null;
        }

        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }
}