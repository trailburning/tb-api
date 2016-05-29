<?php


namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchMappingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:search:mapping')
            ->setDescription('Sets or updates the mapping for a specified type')
            ->addArgument('type', InputArgument::REQUIRED, 'The type to populate')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client = $this->getContainer()->get('vendor.elasticsearch.client');
        $type = $input->getArgument('type');

        switch ($type) {
            case 'race_event':
                $this->populateRaceEventMapping();
                $output->writeln(sprintf('Mapping for type "%s" was updated', $type));
                break;
            default:
                $output->writeln(sprintf('<error>Unknown type "%s"</error>', $type));
                break;
        }
    }

    protected function populateRaceEventMapping()
    {
        $params = [
            'index' => 'search',
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
                            'analyzer' => 'simple',
                        ],
                        'category' => [
                            'type' => 'string',
                            'analyzer' => 'simple',
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
                                'type' => [
                                    'type' => 'string',
                                    'index' => 'not_analyzed',
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
                    ],
                ],
            ],
        ];

        $this->client->indices()->putMapping($params);
    }
}
