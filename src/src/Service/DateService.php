<?php

namespace App\Service;

class DateService
{
    /**
     * Check, if a datetime object has the date of the current day.
     *
     * @param  \DateTime $dateTime
     * @return void
     */
    public static function isDatetimeToday(\Datetime $dateTime): bool
    {
        if (null !== $dateTime) {
            $dateStart = $dateTime->format('Y-m-d');
            $dateNow = date('Y-m-d');

            if ($dateStart == $dateNow) {
                return true;
            }
        }

        return false;
    }
}
