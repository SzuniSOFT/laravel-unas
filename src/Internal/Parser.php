<?php


namespace SzuniSoft\Unas\Internal;


use function simplexml_load_string;

trait Parser
{

    /**
     * Parse Payload Data
     *
     * @param string $payload
     *
     * @return array
     *
     */
    public function parsePayload($payload)
    {
        if ($payload) {
            $xml = simplexml_load_string($payload, 'SimpleXMLElement', (LIBXML_VERSION >= 20700) ? (LIBXML_PARSEHUGE | LIBXML_NOCDATA) : LIBXML_NOCDATA);
            $ns = ['' => null] + $xml->getDocNamespaces(true);
            return $this->recursive_parse($xml, $ns);
        }
        return [];
    }

    protected function recursive_parse($xml, $ns)
    {
        $xml_string = (string)$xml;
        if ($xml->count() == 0 and $xml_string != '') {
            if (count($xml->attributes()) == 0) {
                if (trim($xml_string) == '') {
                    $result = null;
                } else {
                    $result = $xml_string;
                }
            } else {
                $result = array('#text' => $xml_string);
            }
        } else {
            $result = null;
        }
        foreach ($ns as $nsName => $nsUri) {
            foreach ($xml->attributes($nsUri) as $attName => $attValue) {
                if (!empty($nsName)) {
                    $attName = "{$nsName}:{$attName}";
                }
                $result["@{$attName}"] = (string)$attValue;
            }
            foreach ($xml->children($nsUri) as $childName => $child) {
                if (!empty($nsName)) {
                    $childName = "{$nsName}:{$childName}";
                }
                $child = $this->recursive_parse($child, $ns);
                if (is_array($result) and array_key_exists($childName, $result)) {
                    if (is_array($result[$childName]) and is_numeric(key($result[$childName]))) {
                        $result[$childName][] = $child;
                    } else {
                        $temp = $result[$childName];
                        $result[$childName] = [$temp, $child];
                    }
                } else {
                    $result[$childName] = $child;
                }
            }
        }
        return $result;
    }

}
