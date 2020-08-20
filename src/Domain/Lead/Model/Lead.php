<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 15:56
 */

namespace App\Domain\Lead\Model;


use DateTimeImmutable;

final class Lead {

	/**
	 * @var LeadId
	 */
	private  $id;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $nif;
	/**
	 * @var string
	 */
	private $gender;
	/**
	 * @var string
	 */
	private $maritalStatus;
	/**
	 * @var DateTimeImmutable|null
	 */
	private $birthdate;
	/**
	 * @var DateTimeImmutable
	 */
	private $created_at;
	/**
	 * @var DateTimeImmutable
	 */
	private $update_at;

	/**
	 * Lead constructor.
	 *
	 * @param LeadId $id
	 * @param string $name
	 */
	public function __construct( LeadId $id, string $name, DateTimeImmutable $created_at, DateTimeImmutable $update_at  ) {
		$this->id   = $id;
		$this->name = $name;
		$this->created_at = $created_at;
		$this->update_at = $created_at;
	}
	public static function create( LeadId $id, string $name) {
		$lead = new self( $id, $name,new DateTimeImmutable('now'),new DateTimeImmutable('now') );

		/*$lead->record(
			new JeekWasCreated(
				$jeek
			)
		);*/
		return $lead;
	}
	/**
	 * @return LeadId
	 */
	public function getId(): LeadId {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getCreatedAt(): DateTimeImmutable {
		return $this->created_at;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getUpdateAt(): DateTimeImmutable {
		return $this->update_at;
	}


}
