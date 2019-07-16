<?php
/**
 * Logging class
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Log {

	/**
	 * Logs an info message
	 *
	 * @param string $message
	 * @param array  $details
	 * @return void
	 */
	public static function info( $message, $details = array() ) {
		$log = new Logger( 'wp-job-manager-' . WP_JOB_MANAGER_RECRUITER_SLUG );
		$log->pushHandler( new StreamHandler( WP_JOB_MANAGER_JOBADDER_LOG, Logger::DEBUG ) );
		$log->info( esc_html__( $message, 'wp-job-manager-' . WP_JOB_MANAGER_RECRUITER_SLUG ), (array) $details );
	}


	/**
	 * Logs an error message
	 *
	 * @param string $message
	 * @param array  $details
	 * @return void
	 */
	public static function error( $message, $details = array() ) {
		$log = new Logger( 'wp-job-manager-' . WP_JOB_MANAGER_RECRUITER_SLUG );
		$log->pushHandler( new StreamHandler( WP_JOB_MANAGER_JOBADDER_LOG, Logger::DEBUG ) );
		$log->error( esc_html__( $message, 'wp-job-manager-' . WP_JOB_MANAGER_RECRUITER_SLUG ), (array) $details );
	}
	
}