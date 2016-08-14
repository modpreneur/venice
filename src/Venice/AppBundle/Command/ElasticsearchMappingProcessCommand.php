<?php

namespace Venice\AppBundle\Command;

use Elasticsearch\Client as ESClient;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

// todo: @GabrielBordovsky @JakubFajkus convert this to venice command;
// todo: @GabrielBordovsky @JakubFajkus rename the index from necktie to venice?
/**
 * Class ElasticsearchMappingProcessCommand.
 */
class ElasticsearchMappingProcessCommand extends ContainerAwareCommand
{
    const INDEXES = ['necktie', 'necktie_tests'];
    const PATH = '/var/app/app/ElasticsearchMigrations/';

    protected $elasticHost;

    /** @var  ESClient */
    protected $eSClient;

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('venice:elastic:migrations')
            ->setDescription('Check status and puts elastic mapping.')
            ->addOption(
                'clean-start',
                null,
                InputOption::VALUE_NONE,
                'Deletes database and starts clean.'
            )
            ->addOption(
                'test-only',
                null,
                InputOption::VALUE_NONE,
                'Changes are applied only on test index (necktie_tests)'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \LengthException
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('ElasticsearchMappingProcessCommand.php - NOT IMPLEMENTED YET!');

        return 0;

        $this->create();
        $new = $this->getConfig();
        $clean = false;
        $msg = 'No status found for ';

        if ($input->getOption('clean-start')) {
            $msg = 'Cleaning index ';
            $clean = true;
        }

        foreach (self::INDEXES as $index) {
            if ($index === 'necktie' && $input->getOption('test-only')) {
                continue;
            }
            $newData = false;
            $params = [
                'index' => $index,
                'type' => 'migration_status',
            ];
            try {
                $esResponse = $this->eSClient->search($params);
            } catch (Missing404Exception $e) {
                $esResponse = null;
            }

            if ($esResponse && $esResponse['hits']['total'] > 1) {
                throw new \LengthException(
                    "None or one migration status excepted for index $index,".
                    "{$esResponse['hits']['total']} obtained. \n".
                    'Run with --clean-start to restore default clean state of ElasticSearch.'
                );
            }

            if ($clean || !$esResponse || $esResponse['hits']['total'] === 0) {
                $status = [
                    'NEW' => 0,
                    'LAST' => 0,
                ];
                $newData = $clean || $esResponse;
                $this->init($index, $clean && $esResponse);
                $output->writeln($msg.$index.'.');
            } else {
                $status = $esResponse['hits']['hits'][0]['_source'];
            }

            if ($status['LAST'] <  $new) {
                $status['NEW'] = $new;
                $client = new Client();
                do {
                    $data = $this->getMigrationData(++$status['LAST']);
                    $data = each($data);
                    $response = $client->request(
                        'POST',
                        'http://'.$this->elasticHost."/$index/_mapping/{$data['key']}",
                        [
                            'headers' => ['Content-Type' => 'application/json'],
                            'body' => $data['value'],
                        ]
                    );
                    if ($response->getBody()->getContents() !== '{"acknowledged":true}') {
                        $this->updateStatus($newData, $status, $esResponse['hits']['hits'][0]['_id'] ?? null);

                        throw new \UnexpectedValueException(
                            'Unexpected response when putting migration number '.$status['LAST'].
                            ':'.PHP_EOL.$response->getBody()->getContents()
                        );
                    }
                } while ($status['LAST'] < $status['NEW']);
                $output->writeln('Index: '.$index.': migrated to '.$status['LAST']);
                $this->updateStatus($newData, $params, $status, $esResponse['hits']['hits'][0]['_id'] ?? null);
            } else {
                $output->writeln('Index: '.$index.': nothing to migrate');
            }
        }
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \LogicException
     */
    private function create()
    {
        $elasticHost = $this->getContainer()->getParameter('elasticsearch_host');
        if (count(explode(':', $elasticHost)) === 1) {
            $elasticHost .= ':9200';
        }
        $this->elasticHost = $elasticHost;

        $this->eSClient = ClientBuilder::create()->setHosts([$elasticHost])->build();
    }

    /**
     * @param $newData
     * @param $params
     * @param $status
     * @param null $id
     */
    private function updateStatus($newData, $params, $status, $id = null)
    {
        $params['ttl'] = 0;
        if ($newData) {
            $params['body'] = $status;
            $this->eSClient->index($params);
        } else {
            $params['body'] = ['doc' => $status];
            $params['id'] = $id;
            $this->eSClient->update($params);
        }
    }

    /**
     * @param string $index
     * @param bool   $delete
     *
     * @throws \UnexpectedValueException
     */
    private function init(string $index, bool $delete = true)
    {
        $client = new Client();
        if ($delete) {
            $client->request('DELETE', 'http://'.$this->elasticHost."/$index");
        }
        $mapping = $this->getMigrationData(0, false);
        $client->request('POST', 'http://'.$this->elasticHost."/$index", [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $mapping['MAPPING'],
        ]);
    }

    /**
     * @param int  $fileName
     * @param bool $withType
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    private function getMigrationData(int $fileName, bool $withType = true)
    {
        $fileContent = file_get_contents(self::PATH.'MigrationData/'.$fileName);
        if (!$fileContent) {
            throw new \UnexpectedValueException(
                'Migration file not found on '.self::PATH.'MigrationData/'.$fileName
            );
        }
        $eolPos = 0;
        if ($withType) {
            $equalPos = strpos($fileContent, '=') + 2;
            $eolPos = strpos($fileContent, PHP_EOL) - 1;
            $dataType = substr($fileContent, $equalPos, $eolPos - $equalPos);
        } else {
            $dataType = 'MAPPING';
        }
        $equalPos = strpos($fileContent, '=', $eolPos) + 2;

        $quotePos = strrpos($fileContent, "'");

        $data = substr($fileContent, $equalPos, $quotePos - $equalPos);

        return [$dataType => $data];
    }

    /**
     * @return int
     *
     * @throws \UnexpectedValueException
     */
    private function getConfig()
    {
        $fileContent = file_get_contents(self::PATH.'migration.conf.dist');
        if (!$fileContent) {
            throw new \UnexpectedValueException(
                'Config file for new migration setup not found on expected '.self::PATH.'migration.conf.dist'
            );
        }
        $lines = explode(PHP_EOL, $fileContent);
        $update = explode('=', $lines[0]);
        if ($update[0] === 'UPDATE' && (int) $update[1] > 0) {
            return (int) $update[1];
        } else {
            throw new \UnexpectedValueException(
                'Config file for new migration setup does not contain valid configuration'
            );
        }
    }
}
