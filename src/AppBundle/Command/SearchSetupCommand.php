<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Elasticsearch\Client;

class SearchSetupCommand extends ContainerAwareCommand
{
    
    /**
     * @var Client
     */
    private $client;
    
    protected function configure()
    {
        $this
            ->setName('app:search:setup')
            ->setDescription('Updates the index settings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client = $this->getContainer()->get('vendor.elasticsearch.client');
        $this->setupSearchIndex($output);
    }

    protected function setupSearchIndex(OutputInterface $output)
    {
        $indexName = $this->getContainer()->getParameter('search_index_name');
        
        $this->createIndexIfNotExist($indexName, $output);
        $this->closeIndex($indexName);

        $params = [
            'index' => $indexName,
            'body' => [
                'index' => [
                    'analysis' => [
                        'char_filter' => [
                            'iso_latin1_accent' => [
                                'type' => 'mapping',
                                'mappings' => $this->getCharMapping(),
                            ],
                        ],
                        'filter' => [],
                        'tokenizer' => [],
                        'analyzer' => [],
                    ],
                ],
            ],
        ];

        $this->client->indices()->putSettings($params);
        $this->openIndex($indexName);
        
        $output->writeln(sprintf('Index "%s" analysis updated', $indexName));
    }

    private function createIndexIfNotExist($index, OutputInterface $output) 
    {
        $params = [
            'index' => $index,
        ];
        $result = $this->client->indices()->exists($params);
        if ($result === false) {
            $response = $this->client->indices()->create($params);
            $output->writeln(sprintf('Index "%s" was created', $index));
        }
    }

    private function closeIndex($index)
    {
        $params = [
            'index' => $index,
        ];
        $this->client->indices()->close($params);
    }

    private function openIndex($index)
    {
        $params = [
            'index' => $index,
        ];
        $this->client->indices()->open($params);
    }
    
    private function getCharMapping() 
    {
        $mapping = [
            '\u00C0' => 'A',  // À => A
            '\u00C1' => 'A',  // Á => A
            '\u00C2' => 'A',  // Â => A
            '\u00C3' => 'A',  // Ã => A
            '\u00C4' => 'A',  // Ä => A
            '\u00C5' => 'A',  // Å => A            
            '\u00C6' => 'AE', // Æ => AE
            '\u00C7' => 'C',  // Ç => C
            '\u00C8' => 'E',  // È => E
            '\u00C9' => 'E',  // É => E
            '\u00CA' => 'E',  // Ê => E
            '\u00CB' => 'E',  // Ë => E
            '\u00CC' => 'I',  // Ì => I
            '\u00CD' => 'I',  // Í => I
            '\u00CE' => 'I',  // Î => I
            '\u00CF' => 'I',  // Ï => I
            '\u0132' => 'IJ', // Ĳ => IJ
            '\u00D0' => 'D',  // Ð => D
            '\u00D1' => 'N',  // Ñ => N
            '\u00D2' => 'O',  // Ò => O
            '\u00D3' => 'O',  // Ó => O
            '\u00D4' => 'O',  // Ô => O
            '\u00D5' => 'O',  // Õ => O
            '\u00D6' => 'O',  // Ö => O
            '\u00D8' => 'O',  // Ø => O
            '\u0152' => 'OE', // Œ => OE
            '\u00DE' => 'TH', // Þ
            '\u00D9' => 'U',  // Ù => U
            '\u00DA' => 'U',  // Ú => U
            '\u00DB' => 'U',  // Û => U
            '\u00DC' => 'U',  // Ü => U
            '\u00DD' => 'Y',  // Ý => Y
            '\u0178' => 'Y',  // Ÿ => Y
            '\u00E0' => 'a',  // à => a
            '\u00E1' => 'a',  // á => a
            '\u00E2' => 'a',  // â => a
            '\u00E3' => 'a',  // ã => a
            '\u00E4' => 'a',  // ä => a
            '\u00E5' => 'a',  // å => a
            '\u00E6' => 'ae', // æ => ae
            '\u00E7' => 'c',  // ç => c
            '\u00E8' => 'e',  // è => e
            '\u00E9' => 'e',  // é => e            
            '\u00EA' => 'e',  // ê => e
            '\u00EB' => 'e',  // ë => e
            '\u00EC' => 'i',  // ì => i
            '\u00ED' => 'i',  // í => i
            '\u00EE' => 'i',  // î => i
            '\u00EF' => 'i',  // ï => i
            '\u0133' => 'ij', // ĳ => ij
            '\u00F0' => 'd',  // ð => d
            '\u00F1' => 'n',  // ñ => n
            '\u00F2' => 'o',  // ò => o
            '\u00F3' => 'o',  // ó => o
            '\u00F4' => 'o',  // ô => o    
            '\u00F5' => 'o',  // õ => o
            '\u00F6' => 'o',  // ö => o
            '\u00F8' => 'o',  // ø => o
            '\u0153' => 'oe', // œ => oe
            '\u00DF' => 'ss', // ß => ss
            '\u00FE' => 'th', // þ => th
            '\u00F9' => 'u',  // ù => u
            '\u00FA' => 'u',  // ú => u
            '\u00FB' => 'u',  // û => u            
            '\u00FC' => 'u',  // ü => u
            '\u00FD' => 'y',  // ý => y
            '\u00FF' => 'y',  // ÿ => y
            '\uFB00' => 'ff', // ﬀ => ff
            '\uFB01' => 'fi', // ﬁ => fi
            '\uFB02' => 'fl', // ﬂ => fl
            '\uFB03' => 'ffi',// ﬃ => ffi
            '\uFB04' => 'ffl',// ﬄ => ffl
            '\uFB05' => 'ft', // ﬅ => ft
            '\uFB06' => 'st', // ﬆ => st

        ];
        
        $formated = [];
        
        foreach ($mapping as $k => $v) {
            $formated[] = sprintf('%s=>%s', $k, $v);
        }
        
        return $formated;
    }
}
