<?php

namespace SimpleThings\EntityAudit\Exception;

class NotAuditedException extends AuditException
{
    public function __construct($className)
    {
        parent::__construct($className, null, null);
        $this->message = sprintf(
            "Class '$className' is not audited.",
            $className
        );
    }
}
