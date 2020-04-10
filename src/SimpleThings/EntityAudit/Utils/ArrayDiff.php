<?php

namespace SimpleThings\EntityAudit\Utils;

/**
 * Creates a diff between 2 arrays.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ArrayDiff
{
    public function diff($oldData, $newData)
    {
        $diff = [];

        $keys = array_keys($oldData + $newData);
        foreach ($keys as $field) {
            $old = array_key_exists($field, $oldData) ? $oldData[$field] : null;
            $new = array_key_exists($field, $newData) ? $newData[$field] : null;

            if ($old == $new) {
                $row = ['old' => '', 'new' => '', 'same' => $old];
            } else {
                $row = ['old' => $old, 'new' => $new, 'same' => ''];
            }

            $diff[$field] = $row;
        }

        return $diff;
    }
}
