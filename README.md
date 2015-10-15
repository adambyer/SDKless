#SDKless#

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
    
The way this works is that each API has a configuration file (or two) which the SDKless library uses to determine what needs to happen in order to get data from that API and return them in the desired format.

Now, granted, the code in the 1st example could just be put into a separate class and called like the 2nd example. But that is not what SDKless does. The SDKless class in the 2nd example (and included in this repository) has no API-specific code in it. No references to any APIs. It simply uses the configuration file for the specified API to know what to do.

And keep in mind that your project now only has one or two library files for SDKless, instead of one for each API you need to integrate with. And in many cases, an SDK can be made up of not just one, but several files. With SDKless, all APIs are consumed with the same code, which makes for quicker development, cleaner code, and easier troubleshooting.

This repository includes examples, in JSON configuration files, of this specification applied to several APIs. The hope is that these will be expanded upon, and more added, by the community, resulting in a mostly plug-n-play experience when consuming APIs. This repository also includes a PHP implementation of SDKless.

The specification supports:

 - Authentication
	 - OAuth1
	 - OAuth2
	 - non-standard flows
 - Endpoints
	 - GET, POST, PUT, DELETE
	 - input parameter format
	 - output data format/location
	 - Curl options

<b>See full documentation <a href="http://good-plus-geek.github.io/SDKless" target="_blank">here</a></b>
