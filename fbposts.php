<?php

/**
 * @author Pradeep Veera <pradeep.veera@outlook.com>
 **/

// Importing required files
define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/sdk/src/Facebook/');
require __DIR__ . '/sdk/autoload.php';
require __DIR__ . '/config.php';

// importing a global classes FacebookSession and FacebookRequest
use Facebook\FacebookSession;
use Facebook\FacebookRequest;

// Configure the app ID and app secret 
FacebookSession::setDefaultApplication($FB_CONFIG['app_id'], $FB_CONFIG['app_secret']);

$importer = new ImportFacebook( $FB_CONFIG['access_token'] );
$feed = $importer->importPage('/Ryte/feed?limit=20');		
echo "<h1>Facebook Posts of Ryte GmbH</h1>";
echo $feed;

class ImportFacebook {
    /**
     * @param contains the accesstoken required to retrive the posts
     *
     * @return Facebook posts 
     */	    
	function __construct( $access_token ) {
		$this->session = new FacebookSession( $access_token );		
	}

    /**
     * @param contains the facebook pageid 
     *
     * @return facebook posts
     */    
    public function importPage( $page_id ) {
        $request = (new FacebookRequest( $this->session, 'GET', '/'. $page_id));
        $this->posts = $request->execute()->getGraphObject()->asArray();
        foreach($this->posts['data'] as $post){
            $output.="<h2>".$this->createPostTitle($post)."</h2>";
            $output.="<h3>".$post->story."</h3>";
            $output.="<p>".$post->message."</p>";
            $output.="</br>";
        }
        return $output;
    }

    /**
     * @param contains post object to create a title for each post  
     *
     * @return the title for the facebook post
     */     
    protected function createPostTitle($post) {
		$timestamp = strtotime($post->created_time);
        if(!empty($timestamp)){
            $date = date('d F Y', $timestamp);
            $time = date('H:i', $timestamp);
            $title = $this->page."Post on ".$date . ' at ' . $time;            
        }else {
            $title = "Unknown Page Title";
        }
		return $title;
	}	
}