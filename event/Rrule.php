<?php
namespace ical\event;

class Rrule
{
    const FREQ_YEARLY = 'Recurses yearly';
    const FREQ_MONTHLY = 'Recurses monthly';
    const FREQ_WEEKLY = 'Recurses weekly';
    const FREQ_DAILY = 'Recurses daily';
    const FREQ_HOURLY = 'Recurses hourly';
    const FREQ_MINUTELY = 'Recurses minutely';
    const FREQ_SECONDLY = 'Recurses secondly';

    const WKD_MONDAY = 'monday';
    const WKD_TUESDAY = 'tuesday';
    const WKD_WEDNESDAY = 'wednesday';
    const WKD_THURSDAY = 'thursday';
    const WKD_FRIDAY = 'friday';
    const WKD_SATURDAY = 'saturday';
    const WKD_SUNDAY = 'sunday';

    protected $until;
    protected $freq;
    protected $interval;
    protected $count;

    protected $bysecond;
    protected $byminute;
    protected $byhour;
    protected $byday;
    protected $bymonthday;
    protected $byyearday;
    protected $byweekno;
    protected $bymonth;
    protected $bysetpos;

    protected $wkst;

    public function parseProperties(\ical\parser\Row $row) {
        $params = explode(';', $row->value);

        foreach ($params as $param) {
            list($name, $value) = explode('=', $param);

            switch ($name) {
                case 'UNTIL':
                    //$this->until = \ical\util\DateTimeFactory::createFromRow($row);
                    break;

                case 'FREQ':
                    $this->freq = self::translateFrequency($value);
                    break;

                case 'INTERVAL':
                    $this->interval = $value;
                    break;

                case 'COUNT':
                    $this->count = intval($value);
                    break;

                case 'WKST':
                    $this->wkst = $this->translateWeekday($value);
                    break;

                case 'BYSECOND':
                    $this->bysecond = explode(',', $value);
                    break;

                case 'BYMINUTE':
                    $this->byminute = explode(',', $value);
                    break;

                case 'BYHOUR':
                    $this->byhour = explode(',', $value);
                    break;

                case 'BYDAY':
                    $this->byday = explode(',', $value);
                    break;

                case 'BYMONTHDAY':
                    $this->bymonthday = explode(',', $value);
                    break;

                case 'BYYEARDAY':
                    $this->byyearday = explode(',', $value);
                    break;

                case 'BYWEEKNO':
                    $this->byweekno = explode(',', $value);
                    break;

                case 'BYMONTH':
                    $this->bymonth = explode(',', $value);
                    break;

                case 'BYSETPOS':
                    $this->bysetpos = explode(',', $value);
                    break;
            }
        }
    }

    /**
     * Translates a recurrence frequency value to an internal constant
     * @param string $value
     * @return string
     * @throws UnexpectedValueException
     */
    protected function translateFrequency($value) {
        switch ($value) {
            case 'YEARLY': return self::FREQ_YEARLY; break;
            case 'MONTHLY': return self::FREQ_MONTHLY; break;
            case 'WEEKLY': return self::FREQ_WEEKLY; break;
            case 'DAILY': return self::FREQ_DAILY; break;
            case 'HOURLY': return self::FREQ_HOURLY; break;
            case 'MINUTELY': return self::FREQ_MINUTELY; break;
            case 'SECONDLY': return self::FREQ_SECONDLY; break;
        }

        throw new \UnexpectedValueException("Unrecognized frequency:" . $value);
    }

    /**
     * Translates a weekday value to an internal constant
     * @param string $value
     * @return string
     * @throws UnexpectedValueException
     */
    protected function translateWeekday($value) {
        switch ($value) {
            case 'MO': return self::WKD_MONDAY; break;
            case 'TU': return self::WKD_TUESDAY; break;
            case 'WE': return self::WKD_WEDNESDAY; break;
            case 'TH': return self::WKD_THURSDAY; break;
            case 'FR': return self::WKD_FRIDAY; break;
            case 'SA': return self::WKD_SATURDAY; break;
            case 'SU': return self::WKD_SUNDAY; break;
        }

        throw new \UnexpectedValueException("Unrecognized weekday:" . $value);
    }
}