<?php

namespace App\Service;

use App\Repository\CheckupRepository;

class CheckupDateService
{
    private $checkupRepository;
    private $startHour = 10;
    private $endHour = 17;
    private $step = 30;

    public function __construct(CheckupRepository $checkupRepository)
    {
        $this->checkupRepository = $checkupRepository;
    }

    public function getNextDate($date)
    {
        $checkups = $this->checkupRepository->getAllCheckupsByDate($date);
        $bannedTimes = [];
        foreach ($checkups as $checkup) {
            $hour = (int) $checkup->getDate()->format('H');
            $minute = (int) $checkup->getDate()->format('i');
            if (0 === $minute) {
                $bannedTimes[] = $hour . ':00';
            } elseif (30 === $minute) {
                $bannedTimes[] = $hour . ':30';
            } elseif ($minute > 30) {
                $bannedTimes[] = $hour . ':30';
                $bannedTimes[] = $hour + 1 . ':00';
            } else {
                $bannedTimes[] = $hour . ':00';
                $bannedTimes[] = $hour . ':30';
            }
        }
        $currentDate = $date->format('Y-m-d');
        $hour = $this->startHour - 1;
        $minute = $this->step;
        $times = [];
        while (!($this->endHour - 1 === $hour && $minute === $this->step)) {
            $minute += $this->step;
            if ($minute >= 60) {
                $hour += intdiv($minute, 60);
                $minute %= 60;
            }
            $newTime = new \DateTime($currentDate . ' ' . $hour . ':' . $minute);
            if (!in_array($newTime->format('H:i'), $bannedTimes)) {
                $times[] = $newTime->format('H:i');
            }
        }
        if (count($times) > 0) {
            return new \DateTime($currentDate . $times[0]);
        }

        return new \DateTime('1970-01-01 11:11:11');
    }
}
