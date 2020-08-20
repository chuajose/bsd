<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 16:06
 */

namespace App\Tests\Domain\Lead;

use App\Domain\Lead\Model\LeadId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class LeadIdTest extends TestCase{

	/**
	 * @test
	 *
	 * @group unit
	 *
	 * @throws \Exception
	 * @throws \Assert\AssertionFailedException
	 */
	public function given_a_blank_id_it_should_create(): void
	{
		$id = new LeadId(Uuid::uuid4());
		$this->assertInstanceOf('Ramsey\Uuid\Uuid',$id->uuid());
	}


	/**
	 * @test
	 *
	 * @group unit
	 *
	 * @throws \Exception
	 * @throws \Assert\AssertionFailedException
	 */
	public function given_a_uuid_id_it_should_create(): void
	{
		$id = new LeadId(Uuid::uuid4());
		$this->assertInstanceOf('Ramsey\Uuid\Uuid',$id->uuid());
	}


	/**
	 * @test
	 *
	 * @group unit
	 *
	 * @throws \Exception
	 * @throws \Assert\AssertionFailedException
	 */
	public function given_a_invalid_uuid_id_it_should_create(): void
	{
		$this->expectException( \TypeError::class);
		$id = new LeadId('invalid uuid');
	}

	/**
	 * @test
	 *
	 * @group unit
	 *
	 * @throws \Exception
	 * @throws \Assert\AssertionFailedException
	 */
	public function given_a_valid_uuid_in_static_method_id_it_should_create_leadid(): void
	{
		$id = new LeadId(Uuid::uuid4());
		$uuid = LeadId::fromString($id->toString());
		$this->assertInstanceOf('App\Domain\Lead\Model\LeadId',$uuid);
	}


	/**
	 * @test
	 *
	 * @group unit
	 *
	 * @throws \Exception
	 * @throws \Assert\AssertionFailedException
	 */
	public function given_if_two_LeadId_are_equals(): void
	{
		$id = new LeadId(Uuid::uuid4());
		$uuid = LeadId::fromString($id->toString());
		$uuid2 = LeadId::fromString($id->toString());
		$this->assertTrue($uuid->equals($uuid2));
	}


}
