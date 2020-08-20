<?php
/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 15:56
 */

namespace App\Domain\Lead;

use App\Domain\Lead\Model\Lead;
use App\Domain\Lead\Model\LeadId;

interface LeadRepositoryInterface {
	public function save(Lead $lead):void;
	public function saveJson(array $lead):void;
	public function saveActivity(array $activity):void;

	public function list(array $criteria):array;
	public function findbyId(LeadId $id):array;
	public function findby(array $data):array;

}
