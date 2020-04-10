<?php

namespace SimpleThings\EntityAudit;

/**
 * Revision is returned from {@link AuditReader::getRevisions()}
 */
class Revision
{
    private $rev;
    private $timestamp;
    private $username;

    function __construct($rev, $timestamp, $username)
    {
        $this->rev = $rev;
        $this->timestamp = $timestamp;
        $this->username = $username;
    }

    public function getRev()
    {
        return $this->rev;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getUsername()
    {
        return $this->username;
    }
}