<?php

namespace YourLife\DataBundle\Enum;

class MissionResultStatus
{
    // Выполняется пользователем
    const IN_PROGRESS = 'in_progress';

    // Завершено пользователем
    const COMPLETE = 'complete';

    // Пользователь отказался
    const USER_CANCELED = 'user_canceled';
} 