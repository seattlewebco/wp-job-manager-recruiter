<?php
/**
 * Cron jobs
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Cron {

    public function __construct() {
        add_action( 'job_manager_jobadder_sync_jobs', array( $this, 'sync_jobs' ) );

        add_action( 'update_option_jobadder_sync_interval', array( $this, 'schedule_sync' ) );
    }


    /**
     * Performs the synchronization of job ads from JobAdder
     *
     * @return void
     */
    public function sync_jobs() {
        do_action( 'job_manager_jobadder_before_job_sync' );

        WP_Job_Manager_JobAdder()->client->sync_jobs();

        do_action( 'job_manager_jobadder_after_job_sync' );
    }


    /**
     * Sets up schedule for JobAdder job ad synching
     *
     * @return void
     */
    public function schedule_sync() {
        wp_clear_scheduled_hook( 'job_manager_jobadder_sync_jobs' );

        wp_schedule_event( time(), 'jobadder_sync', 'job_manager_jobadder_sync_jobs' );
    }

}