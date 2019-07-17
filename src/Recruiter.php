<?php
/**
 * Main WP_Job_Manager_JobAdder class file
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Recruiter {

    /**
     * Applications handling class
     *
     * @var Applications
     */
    public $applications;


    /**
     * Webhooks handling class
     *
     * @var Webhooks
     */
    public $webhooks;


    /**
     * Logger class
     *
     * @var Log
     */
    public $log;


    /**
     * Cron jobs class
     *
     * @var Cron
     */
    public $cron;


    /**
     * Provider client
     *
     * @var Client
     */
    public $client;

    
    /**
     * Insures that only one instance of WP_Job_Manager_JobAdder exists in memory at any one time.
     * 
     * @return Recruiter The one true instance of Recruiter
     */
    public function __construct( Interface_Adapter $adapter ) {
        $this->client         = new Client( $adapter );
        $this->applications   = new Applications;
        $this->webhooks       = new Webhooks;
        $this->log            = new Log;
        $this->cron           = new Cron;

        $this->hooks();

        do_action_ref_array( 'wp_job_manager_recruiter_loaded', $this ); 
    
        return $this;
    }


    public function hooks() {
        add_action( 'job_manager_' . WP_JOB_MANAGER_RECRUITER_SLUG . '_sync_jobs', array( $this->client, 'sync_jobs' ), 10 );
        add_action( 'job_manager_' . WP_JOB_MANAGER_RECRUITER_SLUG . '_post_job_application', array( $this->client, 'post_job_application' ), 10, 3 );
    }
}