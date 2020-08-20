<?php

declare( strict_types=1 );

/**
 * Created by bysidecar.
 * User: Jose Manuel Suárez Bravo
 * Date: 22/5/20
 * Time: 16:30
 */

namespace App\UI\Command;


use App\Domain\Lead\LeadRepositoryInterface;
use App\Domain\Lead\Model\Lead;
use App\Domain\Lead\Model\LeadId;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Helper\ProgressBar;

final class CreateLeadCommand extends Command{

	protected static $defaultName = 'app:create-lead';
	private $leadRepository;
	public function __construct(LeadRepositoryInterface $leadRepository ) {
		$this->leadRepository = $leadRepository;
		parent::__construct(  );
	}

	protected function configure() {
		$this
			// the short description shown while running "php bin/console list"
			->setDescription( 'Creates a new Lead.' );

	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$verbosityLevelMap = [
			LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
		];
		$faker     = \Faker\Factory::create( 'es_ES' );
		$logger            = new ConsoleLogger( $output, $verbosityLevelMap );
		$start       = microtime( true );

		ini_set('memory_limit', '528M');
		$fila = 1;
		if (($gestor = fopen("var/uploads/leads.csv", "r")) !== FALSE) {
			$total       = count(file('var/uploads/leads.csv'));
			$progressBar = new ProgressBar( $output, $total );
			$progressBar->start();
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				$numero = count($datos);
				$fila++;
				if($fila == 2)continue;

				$lead = [];
				$lead['telefono'] = $datos[0];
				$lead['email'] = $datos[1];
				$lead['ip'] = $datos[2];
				$lead['fecha_insercion'] = $datos[3];
				$lead['nombre'] = $datos[4];
				$lead['apellido1'] = $datos[5];
				$lead['apellido2'] = $datos[6];
				$lead['dni'] = $datos[7];
				$lead['tipo_cliente'] = $datos[8];
				$lead['direccion'] = $datos[9];
				$lead['numero'] = $datos[10];
				$lead['poblacion'] = $datos[11];
				$lead['cp'] = $datos[12];
				$lead['provincia'] = $datos[13];
				$lead['actividades'] = [];
				$this->leadRepository->saveJson($lead);
				$progressBar->advance(1);


			}
			fclose($gestor);
		}
		$progressBar->finish();

		/*dd();
		$file = file_get_contents('var/uploads/demo.json');
		$fileJson = json_decode($file, true);
		dd($fileJson);
		for($i=0; $i < $total; $i++){
			$lead = '{
    "lea_name": null,
    "observations": null,
    "lea_ts": "2020-07-19T09:09:03Z",
    "sou_id": 64,
    "id": 28856,
    "leatype_id": 9,
    "legacy_id": 0,
    "gclid": null,
    "lea_mail": null,
    "passport_id": "fa918ca7-d1c0-4580-9bc6-33f6d224b42c",
    "lea_phone": "634505472",
    "lea_smartcenter_id": "1562086",
    "updated_at": "2020-07-19T09:09:03Z",
    "domain": "www.movilr.es",
    "utm_source": null,
    "lea_dni": null,
    "sub_source": null,
    "lea_ip": "77.26.158.175",
    "passport_id_grp": "b1a80468-fbaa-4422-9378-254d29c531e0",
    "deleted_at": null,
    "created_at": "2020-07-19T09:09:03Z",
    "ga_client_id": null,
    "lea_url": "https://www.movilr.es/tarifa-r.html#",
    "is_smart_center": true
  }';
			$leadToSave = json_decode($lead, true);

			$this->leadRepository->saveJson($leadToSave);
			//$this->leadRepository->save(Lead::create(new LeadId(Uuid::uuid4()), $faker->name));
			$progressBar->advance(1);
		}*/
		return 1;
	}
}
