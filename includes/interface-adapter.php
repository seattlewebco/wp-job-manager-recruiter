<?php
/**
 * API interface
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


interface Interface_Adapter {


    public function connected();

    
    public function get_jobs();


    public function get_job( $job_id );


    public function post_job_application( $job_id, $application_id, $data );


    public function sync_jobs();


    public function job_exists( $job_id );

}