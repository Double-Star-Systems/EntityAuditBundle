<?php

namespace SimpleThings\EntityAudit\Exception;

class DeletedException extends AuditException
{
    public function __construct($className, $id, $revision)
    {
        parent::__construct($className, $id, $revision);
        $this->message = sprintf(
            'Class "%s" entity id "%s" has been removed at revision %s',
            $className,
            implode(', ', $id),
            $revision
        );
    }
}
