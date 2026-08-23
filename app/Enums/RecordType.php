<?php

namespace App\Enums;

enum RecordType: string
{
    case DOCUMENT = 'document';
    case REPORT = 'report';
    case INVOICE = 'invoice';
    case NOTE = 'note';
    case LOG = 'log';
    case MAINTENANCE = 'maintenance';
}