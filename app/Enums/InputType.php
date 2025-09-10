<?php

namespace App\Enums;

enum InputType:string {
    case text = "text";
    case email = "email";
    case number = "number";
    case file = "file";
    case select = "select";
    case radio = "radio";
    case checkbox = "checkbox";
    case textarea = "textarea";
    case date = "date";
    case time = "time";
    case datetime = "datetime-local";
}
