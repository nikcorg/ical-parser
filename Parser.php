<?php
namespace ical;

use ical\Calendar;
use ical\Event;

class Parser
{
    const WHITESPACE = " ";

    protected $calendar;

    /**
     * Reads an .ics file and returns a ical\Calendar-object
     * @param string $filename
     * @return ical\Calender
     */
    public function readfile($filename) {
        mb_internal_encoding("UTF-8");

        $chunks = $this->parseChunks($this->unfolddata(file(realpath($filename))));
        $calendar = $this->processChunks($chunks);

        return $calendar;
    }

    protected function processChunks($chunks) {
        $calendar = new \ical\Calendar;

        // Find the CALENDAR chunk
        foreach ($chunks as $chunk) {
            if (\ical\parser\Chunk::TYPE_CALENDAR === $chunk->type) {
                $calendar = \ical\Calendar::createFromChunk($chunk);
                break;
            }
        }
        foreach ($chunks as $chunk) {
            switch ($chunk->type) {
                case \ical\parser\Chunk::TYPE_TIMEZONE:
                    break;

                case \ical\parser\Chunk::TYPE_TZ_DST:
                    break;

                case \ical\parser\Chunk::TYPE_TZ_STD:
                    break;

                case \ical\parser\Chunk::TYPE_EVENT:
                    $event = \ical\Event::createFromChunk($chunk);
                    $calendar->addEvent($event);
                    break;
            }
        }

        return $calendar;
    }

    /**
     * Parse a .ics-file into chunks
     * @param array $data
     * @return array
     */
    protected function parseChunks($data) {
        $chunks = array();
        $stack  = array();
        $cursor = -1;

        foreach ($data as $rownum => $raw) {
            $row = new \ical\parser\Row($raw, $rownum);

            switch ($row->name) {
                case "BEGIN":
                    array_unshift($stack, new \ical\parser\Chunk($row));
                    break;

                case "END";
                    array_unshift($chunks, array_shift($stack));
                    break;

                default:
                    $stack[0]->addRow($row);
                    break;
            }
        }

        if (count($stack) > 0) {
            throw new \Exception("Calendar data not well formed.");
        }

        return $chunks;
    }

    /**
     * Unfolds data that has been split onto several rows
     * @param array $folded
     * @return array
     */
    protected function unfolddata($folded) {
        $cursor = -1;
        $ret = array();

        foreach ($folded as $row) {
            if (mb_substr($row, 0, 1) === self::WHITESPACE) {
                $ret[$cursor] .= rtrim(mb_substr($row, 1), "\r\n");
            } else {
                $ret[++$cursor] = rtrim($row, "\r\n");
            }
        }

        return $ret;
    }
}