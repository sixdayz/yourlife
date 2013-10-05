<?php

namespace YourLife\DataBundle\Service;

class UserLevelService
{
    protected $points2level = [
        0   => 50,
        1   => 150,
        2   => 300,
        3   => 500,
        4   => 850,
        5   => 1200,
        6   => 1600
    ];

    public function getLevelByPoints($points)
    {
        $resultLevel = 0;

        foreach ($this->points2level as $level => $maxPoints) {
            if ($points < $maxPoints) {
                $resultLevel = $level;
                break;
            }
        }

        return $resultLevel;
    }

    public function getPercentForLevelPoints($points)
    {
        $result         = 0;
        $previousPoints = 0;

        foreach ($this->points2level as $level => $p) {
            if (0 == $level) {
                continue;
            }

            if ($points < $p) {
                $result = ceil( 100 * ($points - $previousPoints) / ($p - $previousPoints) );
            }

            $previousPoints = $p;
        }

        return $result;
    }
} 