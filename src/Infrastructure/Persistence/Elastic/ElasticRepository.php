<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 11:59
 */

namespace App\Infrastructure\Persistence\Elastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

abstract class ElasticRepository {
	/** @var string */
	private $index;

	/** @var Client */
	private $client;

	public function __construct(array $config, string $index)
	{
		$this->client = ClientBuilder::fromConfig($config, true);
		$this->index = $index;
	}


	public function search(array $query): array
	{
		$finalQuery = [];
		$finalQuery['index'] = $this->index; // To be deleted in elastic 7
		$finalQuery['body'] = $query;
		return $this->client->search($finalQuery);
	}

	public  function deleteItem($id):void {
		$params          = [];
		$params['index'] = $this->index;
		$params['id']    = $id;
		$this->delete( $params );
	}

	public function edit($id, $data)
	{
		$params   = [];
		$params['index'] =  $this->index;
		$params['id']    = $id;
		$params['body'] = [
			'doc' => $data
		];
		$this->client->update( $params );
	}

	public function refresh(): void
	{
		if ($this->client->indices()->exists(['index' => $this->index])) {
			$this->client->indices()->refresh(['index' => $this->index]);
		}
	}

	public function delete(): void
	{
		if ($this->client->indices()->exists(['index' => $this->index])) {
			$this->client->indices()->delete(['index' => $this->index]);
		}
	}

	public function boot(): void
	{
		if (!$this->client->indices()->exists(['index' => $this->index])) {
			$this->client->indices()->create(['index' => $this->index]);
		}
	}

	protected function add(array $document): array
	{
		$query['index'] =  $this->index; // To be deleted in elastic 7

		$query['body'] = $document;
		//$this->client->bulk($query);
		return $this->client->index($query);
	}


	protected function bulk(array $documents): array
	{
		return $this->client->bulk($documents);
	}

	public function page(array $query, int $page = 1, int $limit = 50): array
	{
		Assertion::greaterThan($page, 0, 'Pagination need to be > 0');

		$query['index'] = $query['type'] = $this->index; // To be deleted in elastic 7
		$query['from'] = ($page - 1) * $limit;
		$query['size'] = $limit;

		$response = $this->client->search($query);

		return [
			'data'  => array_column($response['hits']['hits'], '_source'),
			'total' => $response['hits']['total']['value']?? 0,
		];
	}


}
