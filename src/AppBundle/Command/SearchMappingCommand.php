<?php

namespace AppBundle\Command;

use Elasticsearch\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchMappingCommand extends ContainerAwareCommand
{
    /**
     * @var Client
     */
    private $client;

    protected function configure()
    {
        $this
            ->setName('app:search:mapping')
            ->setDescription('Sets or updates the mapping for a specified type')
            ->addArgument('type', InputArgument::REQUIRED, 'The type to populate')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client = $this->getContainer()->get('vendor.elasticsearch.client');
        $type = $input->getArgument('type');

        switch ($type) {
            case 'race_event':
                $this->populateRaceEventMapping();
                $output->writeln(sprintf('Mapping for type "%s" was updated', $type));
                break;
            case 'autosuggest_location':
                $this->populateAutosuggestLocationMapping();
                $output->writeln(sprintf('Mapping for type "%s" was updated', $type));
                break;
            case 'autosuggest_race_event':
                $this->populateAutosuggestRaceEventMapping();
                $output->writeln(sprintf('Mapping for type "%s" was updated', $type));
                break;
            case 'all':
                $this->populateRaceEventMapping();
                $this->populateAutosuggestLocationMapping();
                $this->populateAutosuggestRaceEventMapping();
                $output->writeln(sprintf('All mappings were updated', $type));
                break;
            default:
                $output->writeln(sprintf('<error>Unknown type "%s"</error>', $type));
                break;
        }
    }

    protected function populateAutosuggestLocationMapping()
    {
        $indexName = $this->getContainer()->getParameter('autosuggest_index_name');

        $params = [
            'index' => $indexName,
            'type' => 'location',
            'body' => [
                'location' => [
                    'dynamic' => 'strict',
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'suggest' => [
                            'type' => 'completion',
                            'analyzer' => 'simple',
                            'search_analyzer' => 'simple',
                            'payloads' => true,
                            'preserve_separators' => false,
                        ],
                        'name' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];

        $this->client->indices()->putMapping($params);
    }

    protected function populateAutosuggestRaceEventMapping()
    {
        $indexName = $this->getContainer()->getParameter('autosuggest_index_name');

        $params = [
            'index' => $indexName,
            'type' => 'race_event',
            'body' => [
                'race_event' => [
                    'dynamic' => 'strict',
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'name' => [
                            'type' => 'string',
                        ],
                        'suggest' => [
                            'type' => 'completion',
                            'analyzer' => 'simple',
                            'search_analyzer' => 'simple',
                            'payloads' => true,
                            'preserve_separators' => false,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->indices()->putMapping($params);
    }

    protected function populateRaceEventMapping()
    {
        $indexName = $this->getContainer()->getParameter('search_index_name');

        $params = [
            'index' => $indexName,
            'type' => 'race_event',
            'body' => [
                'race_event' => [
                    // '_id' => [
                    //     'path' => 'id',
                    // ],
                    'dynamic' => 'strict',
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'name' => [
                            'type' => 'string',
                            'analyzer' => 'standard',
                        ],
                        'about' => [
                            'type' => 'string',
                            'analyzer' => 'english',
                        ],
                        'website' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'coords' => [
                            'type' => 'geo_point',
                        ],
                        'location' => [
                            'type' => 'string',
                            'analyzer' => 'standard',
                        ],
                        'type' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'category' => [
                            'type' => 'string',
                            'analyzer' => 'simple',
                        ],
                        'date' => [
                            'type' => 'date',
                        ],
                        'email' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'rating' => [
                            'type' => 'float',
                            'index' => 'not_analyzed',
                        ],
                        'races' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'name' => [
                                    'type' => 'string',
                                    'analyzer' => 'standard',
                                ],
                                'date' => [
                                    'type' => 'date',
                                ],
                                'category' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'distance' => [
                                    'type' => 'integer',
                                ],
                            ],
                        ],
                        'attributes' => [
                            'type' => 'string',
                            'analyzer' => 'simple',
                        ],
                        'attributes_slug' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ],
                        'media' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'path' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'mimeType' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'credit' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'creditUrl' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'sharePath' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'publish' => [
                                    'type' => 'boolean',
                                    'index' => 'not_analyzed',
                                ],
                                'metadata' => [
                                    'type' => 'object',
                                    'dynamic' => true,
                                ],
                            ],
                        ],
                        'completed' => [
                            'type' => 'nested',
                            'properties' => [
                                'rating' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                                'comment' => [
                                    'type' => 'string',
                                    'analyzer' => 'standard',
                                ],
                                'user' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'string',
                                            'index' => 'not_analyzed',
                                        ],
                                        'first_name' => [
                                            'type' => 'string',
                                            'analyzer' => 'standard',
                                        ],
                                        'last_name' => [
                                            'type' => 'string',
                                            'analyzer' => 'standard',
                                        ],
                                        'avatar' => [
                                            'type' => 'string',
                                            'index' => 'not_analyzed',
                                        ],
                                    ],
                                ],
                                'timestamp' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->client->indices()->putMapping($params);
    }
}
