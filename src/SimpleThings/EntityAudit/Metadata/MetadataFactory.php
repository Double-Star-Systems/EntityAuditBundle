<?php

namespace SimpleThings\EntityAudit\Metadata;

class MetadataFactory
{
    private $auditedEntities = [];

    public function __construct($auditedEntities)
    {
        $this->auditedEntities = array_flip(array_filter($auditedEntities, function($record) {
            return is_string($record) || is_int($record);
        }));
    }

    public function isAudited($entity)
    {
        return isset($this->auditedEntities[$entity]);
    }
    
    public function getAllClassNames()
    {
        return array_flip($this->auditedEntities);
    }
}
