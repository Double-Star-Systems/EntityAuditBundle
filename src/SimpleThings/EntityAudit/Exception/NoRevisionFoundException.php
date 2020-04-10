<?php

namespace SimpleThings\EntityAudit\Exception;

class NoRevisionFoundException extends AuditException
{
    public function __construct($className, $id, $revision)
    {
        parent::__construct($className, $id, $revision);
        $this->message = sprintf(
            "No revision of class '%s' (%s) was found at revision %s or before. The entity did not exist at the specified revision yet.",
            $className,
            implode(', ', $id),
            $revision
        );
    }
}
