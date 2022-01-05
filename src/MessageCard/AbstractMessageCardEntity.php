<?php

namespace MessageCard;

use JsonSerializable;

abstract class AbstractMessageCardEntity implements JsonSerializable
{
    public function jsonSerialize()
    {
        $into_json = array();
        foreach ($this as $property => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($property === 'type' || $property === 'context') {
                $into_json["@$property"] = $value;
            } else {
                $into_json[$property] = $value;
            }
        }
        return $into_json;
    }

    /**
     * Recursively format nested messagecard arrays
     *
     * @param array $array
     * @return mixed
     */
    public function formatMonospaceArray(array $array)
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return '`' . $value . '`';
            } elseif (is_array($value)) {
                return $this->formatMonospaceArray($value);
            } elseif ($value instanceof self) {
                return $value->formatMonospace();
            } else {
                return $value;
            }
        }, $array);
    }

    /**
     * Recursively surround all string properties with backticks, to avoid
     * markdown formatting of the entity.
     *
     * @return self
     */
    public function formatMonospace()
    {
        foreach ($this as $property => $value) {
            if (is_string($value)) {
                // TODO escape existing `
                $this->$property = '`' . $value . '`';
            } elseif (is_array($value)) {
                $this->$property = $this->formatMonospaceArray($value);
            } elseif ($value instanceof self) {
                $this->$property = $value->formatMonospace();
            } else {
                $this->$property = $value;
            }
        }
        return $this;
    }
}
