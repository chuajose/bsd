<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 15:59
 */

namespace App\Domain\Lead\Model;

use Assert\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LeadId {
	/**
	 * @var UuidInterface
	 */
	private $uuid;

	public function __construct(UuidInterface $uuid)
	{
		Assertion::uuid($uuid);
		$this->uuid = $uuid;
	}
	public static function fromString(string $leadId): LeadId
	{
		return new self(Uuid::fromString($leadId));
	}
	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}
	public function toString(): string
	{
		return $this->uuid->toString();
	}
	public function equals($other): bool
	{
		return $other instanceof self && $this->uuid->equals($other->uuid);
	}
	public function __toString(): string
	{
		return $this->uuid->toString();
	}
}
