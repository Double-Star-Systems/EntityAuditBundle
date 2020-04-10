<?php

namespace SimpleThings\EntityAudit\Exception;

class AuditedCollectionException extends AuditException
{
    public function __construct($message)
    {
        \Exception::__construct($message);
    }
}
