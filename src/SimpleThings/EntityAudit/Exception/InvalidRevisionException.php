<?php

namespace SimpleThings\EntityAudit\Exception;

class InvalidRevisionException extends AuditException
{
    public function __construct($revision)
    {
        parent::__construct(null, null, $revision);
        $this->message = sprintf(
            "No revision '%s' exists.",
            $revision
        );
    }
}