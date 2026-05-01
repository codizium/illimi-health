<?php

namespace Illimi\Health\Enums;

enum VisitOutcomeEnum: string
{
    case TreatedAndDismissed = 'treated_and_dismissed';
    case SentHome = 'sent_home';
    case Hospitalised = 'hospitalised';
    case Referred = 'referred';
}
