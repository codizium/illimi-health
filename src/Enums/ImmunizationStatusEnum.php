<?php

namespace Illimi\Health\Enums;

enum ImmunizationStatusEnum: string
{
    case Due = 'due';
    case Given = 'given';
    case Overdue = 'overdue';
    case Waived = 'waived';
}
