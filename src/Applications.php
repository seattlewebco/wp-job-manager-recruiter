<?php
/**
 * Applications handling
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Applications {

    public function __construct() {
        add_action( 'new_job_application', array( $this, 'apply' ), 10, 2 );

        add_filter( 'pre_update_option', array( $this, 'save_application_form_fields' ), 10, 3 );
        add_action( 'delete_option_job_application_form_fields', array( $this, 'reload_application_form' ) );
    }


    public function apply( $application_id, $job_id ) {
        if ( ! get_option( 'jobadder_applications', 0 ) ) {
            return;
        }

        $apply  = new \WP_Job_Manager_Applications_Apply;
        $fields = array();

        /**
         * Lets try and get the real job ID because the second parameter here does not work consistently
         */
        $application = get_post( $application_id );
        
        if ( $application ) {
            $job = get_post( $application->post_parent );

            if ( $job->post_type == 'job_listing' ) {
                $job_id = $job->ID;
            }
        }

        if ( $job_id ) {
            foreach ( $apply->get_fields() as $field ) {
                if ( empty( $field[ WP_JOB_MANAGER_RECRUITER_SLUG ] ) ) {
                    continue;
                }

                $object = explode( ':', $field[ WP_JOB_MANAGER_RECRUITER_SLUG ], 2 );

                if ( sizeof( $object ) > 1 ) {
                    if ( substr( $field[ WP_JOB_MANAGER_RECRUITER_SLUG ], -2, 2 ) == '[]' ) {
                        $key = &$fields[ $object[0] ][ substr( $object[1], 0, -2 ) ];
                    } else {
                        $key = &$fields[ $object[0] ][ $object[1] ];
                    }
                } else {
                    if ( substr( $field[ WP_JOB_MANAGER_RECRUITER_SLUG ], -2, 2 ) == '[]' ) {
                        $key = &$fields[ substr( $field[ WP_JOB_MANAGER_RECRUITER_SLUG ], 0, -2 ) ];
                    } else {
                        $key = &$fields[ $field[ WP_JOB_MANAGER_RECRUITER_SLUG ] ];
                    }
                }

                $value = apply_filters( 'job_manager_' . WP_JOB_MANAGER_RECRUITER_SLUG . '_application_field_value', $field['value'], $field );

                if ( is_array( $key ) || substr( $field[ WP_JOB_MANAGER_RECRUITER_SLUG ], -2, 2 ) == '[]' ) {
                    $key[] = $value;
                } else {
                    $key = $value;
                }
            }

            if ( ! empty( $fields ) ) {
                do_action( 'job_manager_' . WP_JOB_MANAGER_RECRUITER_SLUG . '_post_job_application', $job_id, $application_id, $fields );
            }
        }
    }


    public function save_application_form_fields( $value, $option, $old_value ) {
        if ( $option == 'job_application_form_fields' && isset( $_POST ) && isset( $_POST['field_jobadder'] ) ) {
            $field_labels = ! empty( $_POST['field_label'] ) ? array_map( 'wp_kses_post', $_POST['field_label'] ) : array();

            $new_value = array();

            $i = 0;
            foreach ( $field_labels as $key => $field ) {
                if ( empty( $field_labels[ $key ] ) ) {
                    continue;
                }

                $field_name = sanitize_title( $field_labels[ $key ] );

                if ( isset( $new_value[ $field_name ] ) ) {
                    // Generate a unique field name by appending a number to the existing field name.
                    // Assumes no more than 100 fields with the same name would be needed? Otherwise it will override the field.
                    $counter = 1;
                    while ( $counter <= 100 ) {
                        $candidate = $field_name . '-' . $counter;
                        if ( ! isset( $new_value[ $candidate ] ) ) {
                            $field_name = $candidate;
                            break;
                        }
                        $counter++;
                    }
                }

                $new_value[ $field_name ] = $value[ $field_name ];

                $new_value[ $field_name ][ WP_JOB_MANAGER_RECRUITER_SLUG ] = isset( $_POST['field_jobadder'] ) && isset( $_POST['field_jobadder'][ $key ] ) ? $_POST['field_jobadder'][ $key ] : '';
            
                $i++;
            }

            wp_redirect( admin_url( 'edit.php?post_type=job_application&page=job-applications-form-editor&tab=fields' ) );

            return $new_value;
        }

        return $value;
    }


    /**
     * After resetting application form to defaults, reload the page again
     * due to the localized JS not being updated yet
     *
     * @return void
     */
    public function reload_application_form() {
        wp_redirect( admin_url( 'edit.php?post_type=job_application&page=job-applications-form-editor&tab=fields' ) );
        exit;
    }

}