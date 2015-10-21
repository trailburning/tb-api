<?php

/*
 * Copyright (c) Yann Hamon
 * Inspired from code Copyright (c) Patrick Hayes under BSD License
 */

namespace AppBundle\Services;

class GPXParser
{
    /**
     * @var bool
     */
    private $namespace = false;
    
    /**
     * @var string
     */
    private $nss = ''; // Name-space string. eg 'georss:'

    /**
     * Parses GPX data - returns array of segments, each segment contains lat, long and any additional fields.
     *
     * @param string $text A GPX string
     *
     * @return array
     */
    public function parse($text)
    {
        // Change to lower-case and strip all CDATA
        $text = strtolower($text);
        $text = preg_replace('/<!\[cdata\[(.*?)\]\]>/s', '', $text);

        // Load into DOMDocument
        $xmlobj = new \DOMDocument();
        @$xmlobj->loadXML($text);
        if ($xmlobj === false) {
            throw new \Exception('Invalid GPX: '.$text);
        }

        $this->xmlobj = $xmlobj;
        try {
            $routes = $this->parseGPXFeatures();
        } catch (\Exception $e) {
            throw $e;
        }

        return $routes;
    }

    protected function parseGPXFeatures()
    {
        $segments = [];
        $segments = array_merge($segments, $this->parseTracks());
        $routes = array_merge($segments, $this->parseRoutes());

        if (empty($segments)) {
            throw new \Exception('Invalid / Empty GPX');
        }

        return $segments;
    }

    protected function childElements($xml, $nodename = '')
    {
        $children = array();
        foreach ($xml->childNodes as $child) {
            if ($child->nodeName == $nodename) {
                $children[] = $child;
            }
        }

        return $children;
    }

    protected function parsePointTags($node)
    {
        $tags = array();
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                switch ($child->nodeName) {
                    case 'ele':
                        $tags['altitude'] = $child->nodeValue;
                        break;

                    case 'time':
                        $tags['datetime'] = strtotime($child->nodeValue);
                        break;

                    default:
                        break;
                }
            }
        }

        // set altitude to null if it is empty, or doesn't exist at all
        if (!isset($tags['altitude']) || (isset($tags['altitude']) && $tags['altitude'] === '')) {
            $tags['altitude'] = null;
        }

        return $tags;
    }

    protected function parseTracks()
    {
        $segments = [];
        $trk_elements = $this->xmlobj->getElementsByTagName('trk');
        
        foreach ($trk_elements as $trk) {
            $routes = [];
            foreach ($this->childElements($trk, 'trkseg') as $trkseg) {
                foreach ($this->childElements($trkseg, 'trkpt') as $trkpt) {
                    $data = [
                        'long' => $trkpt->attributes->getNamedItem('lon')->nodeValue,
                        'lat' => $trkpt->attributes->getNamedItem('lat')->nodeValue,
                    ];
                    $data = array_merge($data, $this->parsePointTags($trkpt));
                    $routes[] = $data;
                }
            }
            $segments[] = $routes;
        }

        return $segments;
    }

    protected function parseRoutes()
    {
        $segments = [];
        $rte_elements = $this->xmlobj->getElementsByTagName('rte');
        foreach ($rte_elements as $rte) {
            $routes = [];

            foreach ($this->childElements($rte, 'rtept') as $rtept) {
                $data = [
                    'long' => $rtept->attributes->getNamedItem('lon')->nodeValue,
                    'lat' => $rtept->attributes->getNamedItem('lat')->nodeValue,
                ];
                $data = array_merge($data, $this->parsePointTags($trkpt));
                $routes[] = $data;
            }
            $segments[] = $route;
        }

        return $segments;
    }
}
