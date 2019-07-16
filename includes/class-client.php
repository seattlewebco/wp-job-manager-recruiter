<?php
/**
 * JobAdder API client wrapper
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Client {


    private $adapter;


    public function __construct( Interface_Adapter $adapter ) {
        $this->adapter = $adapter;
    }


    public function connected() {
        return $this->adapter->connected();
    }


    public function adapter() {
        return $this->adapter;
    }


    public function get_job() {
        return $this->adapter->get_job();
    }


    public function get_jobs() {
        return $this->adapter->get_jobs();
    }

    
    public function post_job_application( $job_id, $application_id, $data ) {
        return $this->adapter->post_job_application( $job_id, $application_id, $data );
    }


    public function sync_jobs() {
        $jobs = $this->adapter->sync_jobs();

        WP_Job_Manager_JobAdder()->log->info( __( 'Synching jobs...', 'wp-job-manager-jobadder' ) );

        foreach ( $jobs as $job_postdata ) {
            $existing = $this->adapter->job_exists( $job_postdata['meta_input']['_jid'] );

            if ( $existing ) {
                $job_postdata['ID'] = $existing;

                $job = wp_update_post( $job_postdata );

                WP_Job_Manager_JobAdder()->log->info( sprintf( __( 'Updating job ID %s', 'wp-job-manager-jobadder' ), $job ) );
            } else {
                $job = wp_insert_post( $job_postdata );

                WP_Job_Manager_JobAdder()->log->info( sprintf( __( 'Inserting job ID %s', 'wp-job-manager-jobadder' ), $job ) );
            }
        }
    }
}