<?php

class endifMediaGitHubPluginUpdater {
 
    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $username; // GitHub username
    private $repo; // GitHub repo name
    private $pluginFile; // __FILE__ of our plugin
    private $githubAPIResult; // holds data from GitHub
    private $accessToken; // GitHub private repo token
 
    function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' ) {
        add_filter( "pre_set_site_transient_update_themes", array( $this, "setTransitent" ) );
        add_filter( "plugins_api", array( $this, "setPluginInfo" ), 10, 3 );
        add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );
 
        $this->pluginFile = $pluginFile;
        $this->username = $gitHubUsername;
        $this->repo = $gitHubProjectName;
        $this->accessToken = $accessToken;
    }
 
    // Get information regarding our plugin from WordPress
    private function initPluginData() {
        // code here
        //$this->slug = plugin_basename( $this->pluginFile );

        $this->pluginData = wp_get_theme();
        $this->slug = 'stringcan-child-theme';
    }
 
    // Get information regarding our plugin from GitHub
    private function getRepoReleaseInfo() {
      
        // Only do this once
        if ( ! empty( $this->githubAPIResult ) ) {
            return;
        }

        // Query the GitHub API
        $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
         
        // We need the access token for private repos
        if ( ! empty( $this->accessToken ) ) {
            $url = add_query_arg( array( "access_token" => $this->accessToken ), $url );
        }
         
        // Get the results
        $this->githubAPIResult = wp_remote_retrieve_body( wp_remote_get( $url ) );
        if ( ! empty( $this->githubAPIResult ) ) {
            $this->githubAPIResult = @json_decode( $this->githubAPIResult, true );
        }
    }
 
    // Push in plugin version information to get the update notification
    public function setTransitent( $transient ) {
        // If we have checked the plugin data before, don't re-check
        if ( empty( $transient->checked ) ) {
            //return $transient;
        }

        // Get plugin & GitHub release information
        $this->initPluginData();
        $this->getRepoReleaseInfo();

        // Check the versions if we need to do an update
        $doUpdate = '1';//version_compare( $this->githubAPIResult->tag_name, $transient->checked[$this->slug] );

       

        // Update the transient to include our updated plugin data
        if ( $doUpdate == 1 ) {

            /*echo '<pre>';
            //var_dump($this->githubAPIResult);
            
            print_r($this->githubAPIResult[0]['tag_name']);
            print_r($this->githubAPIResult[0]['zipball_url']);
            print_r($this->githubAPIResult[0]['url']);            
            echo '</pre>';*/

            $package = $this->githubAPIResult[0]['zipball_url'];
         
            // Include the access token for private GitHub repos
            if ( !empty( $this->accessToken ) ) {
                $package = add_query_arg( array( "access_token" => $this->accessToken ), $package );
            }
         
            /*$obj = new stdClass();
            $obj->slug = 'stringcan-child-theme';//$this->slug;
            $obj->new_version = $this->githubAPIResult[0]['tag_name'];
            $obj->url = $this->pluginData["ThemeURI"];
            $obj->package = $package;*/


            $transient->response[$this->slug] = array( 
                    'slug' => 'stringcan-child-theme',
                    'new_version' => $this->githubAPIResult[0]['tag_name'],
                    'url' => 'https://github.com/endifmedia/stringcan-child-theme/releases',
                    'package' => $package
                    );

        }

        /*echo '<pre>';
        var_dump($transient);
        echo '</pre>';*/

        return $transient;
    }
 
    // Push in plugin version information to display in the details lightbox
    public function setPluginInfo( $false, $action, $response ) {
        // Get plugin & GitHub release information
        $this->initPluginData();
        $this->getRepoReleaseInfo();

        // If nothing is found, do nothing
        if ( empty( $response->slug ) || $response->slug != $this->slug ) {
            return false;
        }

        // Add our plugin information
        $response->last_updated = $this->githubAPIResult[0]['published_at'];
        $response->slug = $this->slug;
        $response->plugin_name  = $this->pluginData["Name"];
        $response->version = $this->githubAPIResult[0]['tag_name'];
        $response->author = 'Ethan Allen';
        $response->homepage = $this->pluginData["ThemeURI"];
         
        // This is our release download zip file
        $downloadLink = $this->githubAPIResult[0]['zipball_url'];
         
        // Include the access token for private GitHub repos
        if ( !empty( $this->accessToken ) ) {
            $downloadLink = add_query_arg(
                array( "access_token" => $this->accessToken ),
                $downloadLink
            );
        }
        $response->download_link = $downloadLink;

        // We're going to parse the GitHub markdown release notes, include the parser
        require_once( 'Parsedown.php' );

        // Create tabs in the lightbox
        $response->sections = array(
            'description' => $this->pluginData["Description"],
            'changelog' => class_exists( "Parsedown" )
                ? Parsedown::instance()->parse( $this->githubAPIResult[0]['body'] )
                : $this->githubAPIResult[0]['body']
        );

        // Gets the required version of WP if available
        $matches = null;
        preg_match( "/requires:\s([\d\.]+)/i", $this->githubAPIResult[0]['body'], $matches );
        if ( ! empty( $matches ) ) {
            if ( is_array( $matches ) ) {
                if ( count( $matches ) > 1 ) {
                    $response->requires = $matches[1];
                }
            }
        }
         
        // Gets the tested version of WP if available
        $matches = null;
        preg_match( "/tested:\s([\d\.]+)/i", $this->githubAPIResult[0]['body'], $matches );
        if ( ! empty( $matches ) ) {
            if ( is_array( $matches ) ) {
                if ( count( $matches ) > 1 ) {
                    $response->tested = $matches[1];
                }
            }
        }
         
        //return $response;
        //var_dump($response);
    }
 
    // Perform additional actions to successfully install our plugin
    public function postInstall( $true, $hook_extra, $result ) {
        // Get plugin information
        $this->initPluginData();

        // Remember if our plugin was previously activated
        $wasActivated = is_plugin_active( $this->slug );

        // Since we are hosted in GitHub, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:
        global $wp_filesystem;
        $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
        $wp_filesystem->move( $result['destination'], $pluginFolder );
        $result['destination'] = $pluginFolder;

        // Re-activate plugin if needed
        if ( $wasActivated ) {
            $activate = activate_plugin( $this->slug );
        }
         
        return $result;
    }
}
