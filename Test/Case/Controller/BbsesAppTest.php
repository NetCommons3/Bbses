<?php
/**
 * BbsesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsFrameComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsBlockComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsRoomRoleComponent', 'NetCommons.Controller/Component');
App::uses('YAControllerTestCase', 'NetCommons.TestSuite');

/**
 * BbsesController Test Case
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BbsesAppTest extends YAControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.net_commons.site_setting',
		'plugin.bbses.bbs',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_articles_user',
		'plugin.bbses.bbs_article',
		'plugin.blocks.block',
		'plugin.blocks.block_role_permission',
		'plugin.boxes.box',
		'plugin.boxes.boxes_page',
		'plugin.comments.comment',
		'plugin.containers.container',
		'plugin.containers.containers_page',
		'plugin.frames.frame',
		'plugin.m17n.language',
		'plugin.pages.page',
		'plugin.pages.languages_page',
		'plugin.plugin_manager.plugin',
		'plugin.roles.default_role_permission',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.roles_room',
		'plugin.rooms.room',
		'plugin.rooms.room_role_permission',
		'plugin.users.user',
		'plugin.users.user_attributes_user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'ja');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		Configure::write('Config.language', null);
		CakeSession::write('Auth.User', null);
		parent::tearDown();
	}

/**
 * _generateController method
 *
 * @param string $controllerName controller name
 * @param array $addMocks generate options
 * @return void
 */
	protected function _generateController($controllerName, $addMocks = array()) {
		$mocks = array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
			)
		);
		$params = array_merge_recursive($mocks, $addMocks);

		$this->generate($controllerName, $params);
	}

/**
 * _loginAdmin method
 *
 * @return void
 */
	protected function _loginAdmin() {
		//ログイン処理
		$this->controller->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(function () {
				$array = array(
					'id' => 1,
					'username' => 'admin',
					'role_key' => 'system_administrator',
				);
				CakeSession::write('Auth.User', $array);
				return $array;
			}));

		$this->controller->Auth->login(array(
				'username' => 'admin',
				'password' => 'admin',
			)
		);
		$this->assertTrue($this->controller->Auth->loggedIn(), '_loginAdmin()');
	}

/**
 * _loginAdmin method
 *
 * @return void
 */
	protected function _loginEditor() {
		//ログイン処理
		$this->controller->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(function () {
				$array = array(
					'id' => 3,
					'username' => 'editor',
					'role_key' => 'editor',
				);
				CakeSession::write('Auth.User', $array);
				return $array;
			}));

		$this->controller->Auth->login(array(
				'username' => 'editor',
				'password' => 'editor',
			)
		);
		$this->assertTrue($this->controller->Auth->loggedIn(), '_loginEditor()');
	}

/**
 * _loginAdmin method
 *
 * @return void
 */
	protected function _loginVisitor() {
		//ログイン処理
		$this->controller->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(function () {
				$array = array(
					'id' => 5,
					'username' => 'visitor',
					'role_key' => 'visitor',
				);
				CakeSession::write('Auth.User', $array);
				return $array;
			}));

		$this->controller->Auth->login(array(
				'username' => 'visitor',
				'password' => 'visitor',
			)
		);
		$this->assertTrue($this->controller->Auth->loggedIn(), '_loginVisitor()');
	}

/**
 * _logout method
 *
 * @return void
 */
	protected function _logout() {
		//ログアウト処理
		$this->testAction('/auth/logout', array(
			'data' => array(
			),
		));
		$this->assertNull(CakeSession::read('Auth.User'), '_logout()');
	}

/**
 * _setComponentError method
 *
 * @param string $componentName component name
 * @param string $methodName method name
 * @return void
 */
	protected function _setComponentError($componentName, $methodName) {
		$this->controller->$componentName
			->staticExpects($this->any())
			->method($methodName)
			->will($this->returnValue(false));

		$this->assertTrue(
				method_exists($this->controller->$componentName, $methodName),
				get_class($this->controller->$componentName) . '::' . $methodName
			);
	}

/**
 * _setModelError method
 *
 * @param string $modelName model name
 * @param string $methodName method name
 * @return void
 */
	protected function _setModelError($modelName, $methodName) {
		$this->controller->$modelName = $this->getMockForModel($modelName, array($methodName));
		$this->controller->$modelName->expects($this->any())
			->method($methodName)
			->will($this->returnValue(false));

		$this->assertTrue(
				method_exists($this->controller->$modelName, $methodName),
				get_class($this->controller->$modelName) . '::' . $methodName
			);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
		$this->assertTrue(true);
	}
}
