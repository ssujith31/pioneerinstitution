<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

include_once __DIR__ . '/stubs/JOAuth1ClientInspector.php';
include_once __DIR__ . '/../session/handler/array.php';

/**
 * Test class for JOAuth1Client.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       3.2.0
 */
class JOAuth1ClientTest extends TestCase
{
	/**
	 * @var    JInput  input for the OAuth object.
	 * @since  3.2.0
	 */
	protected $input;

	/**
	 * @var    Registry  Options for the OAuth object.
	 * @since  3.2.0
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock http object.
	 * @since  3.2.0
	 */
	protected $client;

	/**
	 * An instance of the object to test.
	 *
	 * @var    JOAuth1ClientInspector
	 * @since  3.2.0
	 */
	protected $object;

	/**
	 * @var    JApplicationWeb  The application object to send HTTP headers for redirects.
	 * @since  3.2.0
	 */
	protected $application;

	/**
	 * @var    string  Sample JSON string.
	 * @since  3.2.0
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  3.2.0
	 */
	protected $errorString = '{"errorCode":401, "message": "Generic error"}';

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var  array
	 * @since  3.6
	 */
	protected $backupServer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->saveFactoryState();
		$this->backupServer = $_SERVER;
		JFactory::$session = $this->getMockSession();

		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$key = "TEST_KEY";
		$secret = "TEST_SECRET";

		$this->options = new Registry;
		$this->client = $this->getMockBuilder('JHttp')->setMethods(array('get', 'post', 'delete', 'put'))->getMock();
		$this->input = new JInput(array());
		$this->application = $this->getMockWeb();

		$this->options->set('consumer_key', $key);
		$this->options->set('consumer_secret', $secret);
		$this->object = new JOAuth1ClientInspector($this->options, $this->client, $this->input, $this->application);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$_SERVER = $this->backupServer;
		unset($this->backupServer);
		$this->restoreFactoryState();
		unset($this->options, $this->client, $this->input, $this->application, $this->object);
	}

	/**
	* Provides test data.
	*
	* @return array
	*
	* @since 3.2.0
	*/
	public function seedAuthenticate()
	{
		// Token, fail and oauth version.
		return array(
			array(array('key' => 'valid', 'secret' => 'valid'), false, '1.0'),
			array(null, false, '1.0'),
			array(null, false, '1.0a'),
			array(null, true, '1.0a')
			);
	}

	/**
	 * Tests the authenticate method
	 *
	 * @param   array    $token    The passed token.
	 * @param   boolean  $fail     Mark if should fail or not.
	 * @param   string   $version  Specify oauth version 1.0 or 1.0a.
	 *
	 * @return  void
	 *
	 * @dataProvider seedAuthenticate
	 * @since   3.2.0
	 */
	public function testAuthenticate($token, $fail, $version)
	{
		// Already got some credentials stored?
		if (!is_null($token))
		{
			$this->object->setToken($token);
			$result = $this->object->authenticate();
			$this->assertEquals($result, $token);
		}
		else
		{
			$this->object->setOption('requestTokenURL', 'https://example.com/request_token');
			$this->object->setOption('authoriseURL', 'https://example.com/authorize');
			$this->object->setOption('accessTokenURL', 'https://example.com/access_token');

			// Request token.
			$returnData = new stdClass;
			$returnData->code = 200;
			$returnData->body = 'oauth_token=token&oauth_token_secret=secret&oauth_callback_confirmed=true';

			$this->client->expects($this->at(0))
				->method('post')
				->with($this->object->getOption('requestTokenURL'))
				->will($this->returnValue($returnData));

			$input = TestReflection::getValue($this->object, 'input');
			TestReflection::setValue($this->object, 'input', $input);

			if (strcmp($version, '1.0a') === 0)
			{
				$this->object->setOption('callback', 'TEST_URL');
			}
			$this->object->authenticate();

			$token = $this->object->getToken();
			$this->assertEquals($token['key'], 'token');
			$this->assertEquals($token['secret'], 'secret');

			// Access token.
			$input = TestReflection::getValue($this->object, 'input');

			if (strcmp($version, '1.0a') === 0)
			{
				TestReflection::setValue($this->object, 'version', $version);
				$data = array('oauth_verifier' => 'verifier', 'oauth_token' => 'token');
			}
			else
			{
				TestReflection::setValue($this->object, 'version', $version);
				$data = array('oauth_token' => 'token');
			}
			TestReflection::setValue($input, 'data', $data);

			$memoryHandler = new JSessionHandlerArray;

			// Get mock session
			$mockSession = $this->getMockBuilder('JSession')
				->setMethods(array( '_start', 'get'))
				->setConstructorArgs(array('none', array(), $memoryHandler))
				->getMock();

			if ($fail)
			{
				$mockSession->expects($this->at(0))
							->method('get')
							->with('key', null, 'oauth_token')
							->will($this->returnValue('bad'));

				$mockSession->expects($this->at(1))
							->method('get')
							->with('secret', null, 'oauth_token')
							->will($this->returnValue('session'));

				JFactory::$session = $mockSession;

				$this->setExpectedException('DomainException');
				$this->object->authenticate();
			}

			$mockSession->expects($this->at(0))
						->method('get')
						->with('key', null, 'oauth_token')
						->will($this->returnValue('token'));

			$mockSession->expects($this->at(1))
						->method('get')
						->with('secret', null, 'oauth_token')
						->will($this->returnValue('secret'));

			JFactory::$session = $mockSession;

			$returnData = new stdClass;
			$returnData->code = 200;
			$returnData->body = 'oauth_token=token_key&oauth_token_secret=token_secret';

			$this->client->expects($this->at(0))
				->method('post')
				->with($this->object->getOption('accessTokenURL'))
				->will($this->returnValue($returnData));

			$result = $this->object->authenticate();

			$this->assertEquals($result['key'], 'token_key');
			$this->assertEquals($result['secret'], 'token_secret');
		}
	}

	/**
	 * Tests the _generateRequestToken method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException DomainException
	 */
	public function testGenerateRequestTokenFailure()
	{
		$this->object->setOption('requestTokenURL', 'https://example.com/request_token');

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = 'oauth_token=token&oauth_token_secret=secret&oauth_callback_confirmed=false';

		$this->client->expects($this->at(0))
			->method('post')
			->with($this->object->getOption('requestTokenURL'))
			->will($this->returnValue($returnData));

		TestReflection::invoke($this->object, '_generateRequestToken');
	}

	/**
	* Provides test data.
	*
	* @return array
	*
	* @since 3.2.0
	*/
	public function seedOauthRequest()
	{
		// Method
		return array(
			array('GET'),
			array('PUT'),
			array('DELETE')
			);
	}

	/**
	 * Tests the oauthRequest method
	 *
	 * @param   string  $method  The request method.
	 *
	 * @dataProvider seedOauthRequest
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testOauthRequest($method)
	{
		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		if (strcmp($method, 'PUT') === 0)
		{
			$data = array('key1' => 'value1', 'key2' => 'value2');
			$this->client->expects($this->at(0))
				->method($method, $data)
				->with('www.example.com')
				->will($this->returnValue($returnData));

			$this->assertThat(
				$this->object->oauthRequest('www.example.com', $method, array('oauth_token' => '1235'), $data, array('Content-Type' => 'multipart/form-data')),
				$this->equalTo($returnData)
				);

		}
		else
		{
			$this->client->expects($this->at(0))
				->method($method)
				->with('www.example.com')
				->will($this->returnValue($returnData));

			$this->assertThat(
				$this->object->oauthRequest('www.example.com', $method, array('oauth_token' => '1235'), array(), array('Content-Type' => 'multipart/form-data')),
				$this->equalTo($returnData)
				);
		}
	}

	/**
	 * Tests the safeEncode
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testSafeEncodeEmpty()
	{
		$this->assertThat(
			$this->object->safeEncode(null),
			$this->equalTo('')
			);
	}
}
