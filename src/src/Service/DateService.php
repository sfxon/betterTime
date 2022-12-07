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

        
    /**
     * Check, if the second date is bigger than the first one.
     *
     * @param  \Datetime $date1
     * @param  \Datetime $date2
     * @return bool
     */
    public static function secondDateIsBigger(\Datetime $date1, \Datetime $date2): bool
    {
        if($date2 <= $date1) {
            return false;
        }

        return true;
    }
}
