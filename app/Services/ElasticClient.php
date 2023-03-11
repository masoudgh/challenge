<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticClient
{
    protected Client $client;
    private string $index;

    /**
     * @return ElasticClient
     * @throws AuthenticationException
     */
    public static function client(): ElasticClient
    {
        $object = new static();
        $host = env('ELASTICSEARCH_HOST', 'localhost');
        $port = env('ELASTICSEARCH_PORT', '9200');
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASSWORD');

        $object->client = ClientBuilder::create()
            ->setHosts(["{$host}:{$port}"])
            ->setBasicAuthentication($username, $password)
            ->build();

        return $object;
    }

    /**
     * @param string $name
     * @return ElasticClient
     */
    public function index(string $name): static
    {
        $this->index = $name;

        return $this;
    }

    /**
     * @param array $data
     * @return Elasticsearch|Promise
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function create(array $data): Elasticsearch|Promise
    {
        return self::$client->index([
            'index' => $this->index,
            'body' => $data
        ]);
    }

    /**
     * @param mixed $id
     * @return Elasticsearch|Promise
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function delete(mixed $id): Elasticsearch|Promise
    {
        return self::$client->delete([
            'index' => $this->index,
            'id' => $id
        ]);
    }
}
