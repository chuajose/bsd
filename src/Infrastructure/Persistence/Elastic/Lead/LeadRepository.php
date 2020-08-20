<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 14:35
 */

namespace App\Infrastructure\Persistence\Elastic\Lead;

use App\Domain\Lead\Model\Lead;
use App\Domain\Lead\Model\LeadId;
use App\Domain\Lead\LeadRepositoryInterface;
use App\Infrastructure\Persistence\Elastic\ElasticRepository;
use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LeadRepository extends ElasticRepository implements LeadRepositoryInterface {
	/** @var string */
	private $index;
	/** @var Client */
	private $client;
	private $container;

	public function __construct( ContainerInterface $container, array $elasticConfig ) {
		parent::__construct($elasticConfig, 'lead');
		$this->container = $container;
	}

	public function save( Lead $lead ): void {
		// TODO: Implement save() method.
		$document = [
			'type' => 'lead',
			'lead_id' => $lead->getId()->toString(),
			'name' => $lead->getName(),
			'created_at' => $lead->getCreatedAt(),
			'updated_at' => $lead->getUpdateAt(),
			'occurred_on_in_atom' => (new \DateTimeImmutable('now'))->format('Y-m-d\TH:i:s.u'),
			'occurred_on_in_ms' => (new \DateTimeImmutable('now'))->getTimestamp(),
			//'context' => $message->getPayload(),
		];
		$this->store($document);
	}
	public function saveJson( array $lead ): void {
		// TODO: Implement save() method.


		$exist = $this->findPhone($lead['telefono']);
		if($exist === null) {
			$document = [
				'type'                => 'lead',
				'occurred_on_in_atom' => ( new \DateTimeImmutable( 'now' ) )->format( 'Y-m-d\TH:i:s.u' ),
				'occurred_on_in_ms'   => ( new \DateTimeImmutable( 'now' ) )->getTimestamp(),
				//'context' => $message->getPayload(),
			];
			$document = array_merge( $lead, $document );
			$this->store( $document );
		}
	}

	public function saveActivity( array $activity ): void {
		// TODO: Implement save() method.


		$exist = $this->findPhone($activity['telefono']);


		if($exist !== null && isset($exist['_source'])){
			$id = $exist['_id'];
			$lead = $exist['_source'];

			if(isset($lead['actividades'])){

				$lead['actividades'][]=$activity;
			}else{
				$lead['actividades'] = [$activity];


			}

			$this->update($id, $lead);
		}

	}
	public function list( array $criteria ): array {
		// TODO: Implement list() method.
	}

	public function findbyId( LeadId $id ): array {
		// TODO: Implement findbyId() method.
	}


	public function update( $id, $data ) {
		$this->edit( $id, $data );
	}

	public function find(  $id ) {
		try {
			$query = [
				'query' => [
					'bool' => [
						'filter' => [
							[ 'term' => [ 'id.keyword' => $id ] ],
							//[ 'term' => [ 'type.keyword' => 'User' ] ],
						],
					]
				]
			];

			$result = $this->search( $query );
			return $result['hits']['hits'][0]??null;

		} catch ( \Exception $exception ) {

			//TODO notificamos de que elastic search a producido un error? Que hacemos en este caso. Devolviendo null, el servico que carga esta busqueda empieza a bucar en la bbdd de mysql
			return null;
		}

	}

	public function findPhone(  $phone ) {

		try {
			$query = [
				'query' => [
					'bool' => [
						'filter' => [
							[ 'term' => [ 'telefono' => $phone ] ],
							//[ 'term' => [ 'type.keyword' => 'User' ] ],
						],
					]
				]
			];

			$result = $this->search( $query );

			return $result['hits']['hits'][0]??null;

		} catch ( \Exception $exception ) {

			//TODO notificamos de que elastic search a producido un error? Que hacemos en este caso. Devolviendo null, el servico que carga esta busqueda empieza a bucar en la bbdd de mysql
			return null;
		}

	}
	public function findBy(  array  $data):array {
		try {

			$query = [
				'bool' => [

					'must' => [
						[
							'bool' => [
								'should' => [
									[
										'match' => [
											'email' => $data['email']
										]
									],
									[
										'match' => [
											'telefono' => $data['telefono']
										]
									],
									/*[
										'match' => [
											'private' => false
										]
									]*/
								],
							],
						],
					],
				]
			];

			$result = $this->search( $query );
			return $result['hits']['hits'][0]??[];

		} catch ( \Exception $exception ) {

			//TODO notificamos de que elastic search a producido un error? Que hacemos en este caso. Devolviendo null, el servico que carga esta busqueda empieza a bucar en la bbdd de mysql
			return false;
		}

	}

	public function remove($id ) {

		try {
			$params          = [];
			$params['index'] = $this->index;
			$params['id']    = $id;

			$this->client->delete( $params );

			return null;
		} catch ( \Exception $exception ) {

			dd( $exception );

			//TODO notificamos de que elastic search a producido un error? Que hacemos en este caso. Devolviendo null, el servico que carga esta busqueda empieza a bucar en la bbdd de mysql
			return null;
		}

	}


	public function store($document){

		$this->add($document);
	}

	public function searchWithPermission(User $user, array $criteria, ?int $page = 1 ) {
		$limit = $this->container->getParameter( 'limit_per_page' );

		$finalQuery = [];

		//$finalQuery['type']  = 'actions';
		$query = [
			'query' => [

				'bool' => [
					'filter' => [
						'term' => [
							'active' => true
						],
					],
					'must' => [
						[
							'bool' => [
								'should' => [
									/*[
										'match' => [
											'allows.id' => $user->getId()->toString()
										]
									],*/
									[
										'match' => [
											'id' => $user->getId()
										]
									],
									[
										'match' => [
											'private' => false
										]
									]
								],
							],
						],
					]
				]
			],
			'sort'  => [
				'occurred_on_in_ms' => [
					'order' => 'DESC'
				]
			]
		];

		if ( isset( $criteria['must'] ) ) {
			$bool = [];

			foreach ( $criteria['must'] as $key => $value ) {


				$bool[] =
					[
						//'match' => [ $key => $value ]
						$value['type'] => [ $value['field'] => $value['value'] ]

					];

			}

			$query['query']['bool']['must'][]['bool']['should'] = $bool;
		}

		$finalQuery['body'] = $query;
		$response = $this->page( $finalQuery, $page, $limit );
		return [$response['data'], $response['total']];

	}
}
