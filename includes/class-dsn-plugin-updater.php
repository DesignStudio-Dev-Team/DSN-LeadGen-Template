<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DSN_Plugin_Updater {

    private $slug;
    private $plugin_data;
    private $username;
    private $repo;
    private $plugin_file;
    private $github_response;

    public function __construct( $plugin_file, $repo_slug ) {
        $this->plugin_file = $plugin_file;
        $this->slug = plugin_basename( $plugin_file );
        
        list( $this->username, $this->repo ) = explode( '/', $repo_slug );

        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
        add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 10, 3 );
    }

    public function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $this->get_repository_info();

        if ( $this->github_response ) {
            $remote_version = isset( $this->github_response['tag_name'] ) ? $this->github_response['tag_name'] : false;

            // Fix for v-prefixed tags
            $remote_version = ltrim( $remote_version, 'v' );
            $local_version = isset( $transient->checked[ $this->slug ] ) ? $transient->checked[ $this->slug ] : '0.0.0';

            if ( $remote_version && version_compare( $local_version, $remote_version, '<' ) ) {
                $obj = new stdClass();
                $obj->slug = $this->slug;
                $obj->plugin = $this->slug;
                $obj->new_version = $remote_version;
                $obj->url = $this->github_response['html_url'];
                
                // Find zip asset
                if ( isset( $this->github_response['assets'] ) && ! empty( $this->github_response['assets'] ) ) {
                    foreach ( $this->github_response['assets'] as $asset ) {
                        if ( 'application/zip' === $asset['content_type'] ) {
                            $obj->package = $asset['browser_download_url'];
                            break;
                        }
                    }
                }
                
                // Fallback to zipball if no asset found
                if ( empty( $obj->package ) ) {
                     $obj->package = $this->github_response['zipball_url'];
                }

                $transient->response[ $this->slug ] = $obj;
            }
        }

        return $transient;
    }

    public function get_plugin_info( $false, $action, $response ) {
        if ( empty( $response->slug ) || $response->slug !== $this->slug ) {
            return $false;
        }

        $this->get_repository_info();

        if ( $this->github_response ) {
            $response->last_updated = $this->github_response['published_at'];
            $response->slug = $this->slug;
            $response->plugin_name  = $this->plugin_data['Name'];
            $response->version = ltrim( $this->github_response['tag_name'], 'v' );
            $response->author = $this->plugin_data['AuthorName'];
            $response->homepage = $this->plugin_data['PluginURI'];
            
            // Description from body
            $response->sections = array(
                'description' => $this->plugin_data['Description'],
                'changelog'   => nl2br( $this->github_response['body'] )
            );
            
            // Download link
             if ( isset( $this->github_response['assets'] ) && ! empty( $this->github_response['assets'] ) ) {
                foreach ( $this->github_response['assets'] as $asset ) {
                    if ( 'application/zip' === $asset['content_type'] ) {
                        $response->download_link = $asset['browser_download_url'];
                        break;
                    }
                }
            }
             if ( empty( $response->download_link ) ) {
                 $response->download_link = $this->github_response['zipball_url'];
            }

            return $response;
        }

        return $false;
    }

    private function get_repository_info() {
        if ( ! empty( $this->github_response ) ) {
            return;
        }

        $request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repo );
        
        // Add token if available
        $args = array();
        if ( defined( 'DSN_GITHUB_TOKEN' ) ) {
            $args['headers'] = array(
                'Authorization' => 'token ' . DSN_GITHUB_TOKEN
            );
        }

        $response = wp_remote_get( $request_uri, $args );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return;
        }

        $this->github_response = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( ! isset( $this->plugin_data ) ) {
             $this->plugin_data = get_plugin_data( $this->plugin_file );
        }
    }
}
