# SDKless

### NOTE: This repository contains the documentation for the SDKless specification as well as demo implementations in various programming languages.

#### The following implementation libraries are also available:
 - <a href="https://github.com/adambyer/SDKless-python">Python</a>
 - <a href="https://github.com/adambyer/SDKless-php">PHP</a>

## Introduction

SDKless is a specification for describing the usage requirements of APIs. The goal of SDKless is to allow developers to use one code library to consume all APIs, rather than incorporating a separate SDK for each API. This will help speed up development time, allow for cleaner code, make troubleshooting easier, and reduce the number of code files required by your application.

SDKless is most helpful when your application consumes multiple similar APIs. For example, let's say your application allows users to connect to any social networks they use so they can view all their activity in one place. Normally you would incorporate into your fileset, the SDK for the API of each social network you want to support. Then your code would call the SDK-specific method for each connected social network, and then reformat the API-specific output into something your application can use. Something kinda like this...

    switch ($social_network) {
		case 'some-social-network':
			require_once('libraries/some-social-network.php');
			$api = new SomeSocialNetwork();
			$response = $api->getActivity();
			$posts = $response->data;
			$output = array();
			
			foreach ($posts as $post) {
				$output_item = array();
				$output_item['date'] = $contact['post_date'];
				$output_item['post'] = $contact['content']; 
				$output[] = $output_item;
			}
			
			break;
		case 'other-social-network':
			require_once('libraries/other-social-network.php');
			$api = new OtherSocialNetwork();
			$response = $api->pull_feed();
			$posts = $response['feed'];
			$output = array();
			
			foreach ($posts as $post) {
				$output_item = array();
				$output_item['date'] = $contact['date_added'];
				$output_item['content'] = $contact['feed_entry']; 
				$output[] = $output_item;
			}
			
			break;
	}

Etc... for however many social networks you support.

With SDKless that would look more like this...

    require_once('SDKless.php');
    $sdkless = new SDKless($social_network);
    $output = $sdkless->go('get_posts');
    
The way this works is that each API has a configuration file (or two) which the SDKless library uses to determine what needs to happen in order to get data from that API and return it in the desired format.

Now, granted, the code in the 1st example could just be put into a separate class and called like the 2nd example, without SDKless. But that does not eliminate the need for SDKs. The SDKless class in the 2nd example (and in the included demos) has no API-specific code in it. No references to any APIs. It simply uses the configuration file for the specified API to know what to do. No more need to include large SDK libraries; just one or two configuration files for each API.

This repository includes examples, in JSON configuration files, of this specification applied to several APIs. The hope is that API owners everywhere will adopt the SDKless specification and create specification files for their APIs instead of SDKs, resulting in a mostly plug-n-play experience when consuming APIs. This repository also includes PHP and Python/Django demo implementations of SDKless.

The specification supports:

 - Authentication
	 - OAuth1
	 - OAuth2
	 - non-standard flows
 - Endpoints
	 - GET, POST, PUT, DELETE
	 - input parameter format
	 - output data format/location
	 - any desired request headers, format, etc..

<b>See full documentation <a href="http://adambyer.github.io/SDKless/" target="_blank">here</a></b>
