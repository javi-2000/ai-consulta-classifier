<?php

namespace App\Enums;

enum ConsultaStatus: string
{
    case Success = 'SUCCESS';
    case Error = 'ERROR';
    case Timeout = 'TIMEOUT';
    case NoCredits = 'NO_CREDITS';
}
