<?php

namespace App\Enums;

enum ProcessStatus:string {
    case pending = "PENDING";
    case rejected = "REJECTED";
    case cancel = "CANCEL";
    case process = "ON PROCESS";
    case finish = "FINISH";
    case sent = "SENT";
    case downloaded = "DOWNLOADED";
}
