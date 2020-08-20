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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Helper\ProgressBar;

final class CreateActivityCommand extends Command {

	protected static $defaultName = 'app:create-activity';
	private $leadRepository;

	public function __construct( LeadRepositoryInterface $leadRepository ) {
		$this->leadRepository = $leadRepository;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName( 'create:activity' )
			->setDescription( 'Crear actividades' )
			->addArgument(
				'ultima',
				InputArgument::OPTIONAL,
				'Es ultima?'
			);

	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$ultimas  = false;
		$filename = 'var/uploads/actividades.csv';
		if ( $input->getArgument( 'ultima' ) ) {
			$ultimas  = true;
			$filename = 'var/uploads/u_actividades.csv';

		}
		$verbosityLevelMap = [
			LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
		];


		ini_set( 'memory_limit', '528M' );
		$fila = 1;
		if ( ( $gestor = fopen( $filename, "r" ) ) !== false ) {
			$total       = count( file( $filename ) );
			$progressBar = new ProgressBar( $output, $total );
			$progressBar->start();
			while ( ( $datos = fgetcsv( $gestor, 1000, "," ) ) !== false ) {
				$fila ++;
				if ( $fila == 2 ) {
					continue;
				}
				$lead = [];
				if ( $ultimas ) {
					$lead['campaña']              = $datos[0];
					$lead['categoria']            = $datos[1];
					$lead['subcategoria']         = $datos[2];
					$lead['telefono']             = $datos[4];
					$lead['status']               = $datos[5];
					$lead['fecha_categorizacion'] = $datos[7];
					$lead['es_ultima']            = 2;
				} else {
					$lead['campaña']              = $datos[0];
					$lead['idLead']               = $datos[1];
					$lead['url']                  = $datos[2];
					$lead['telefono']             = $datos[3];
					$lead['fecha_insercion']      = $datos[4];
					$lead['tipo']                 = $datos[5];
					$lead['fecha_categorizacion'] = $datos[6];
					$lead['usuario']              = $datos[7];
					$lead['sub_id']               = $datos[8];
					$lead['categoria']            = $datos[9];
					$lead['subcategoria']         = $datos[10];
					$lead['venta']                = $datos[11];
					$lead['status']               = $datos[12];
				}
				$this->leadRepository->saveActivity( $lead );
				$progressBar->advance( 1 );


			}
			fclose( $gestor );
		}
		$progressBar->finish();

	}
}
