<?php
/**
 * BbsBlocksController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlocksControllerEditTest', 'Blocks.TestSuite');

/**
 * BbsBlocksController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Controller
 */
class BbsBlocksControllerEditTest extends BlocksControllerEditTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'bbses';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'bbs_blocks';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.likes.like',
		'plugin.likes.likes_user',
		'plugin.bbses.bbs',
		'plugin.bbses.block_setting_for_bbs',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_article',
		'plugin.bbses.bbs_article_tree',
		'plugin.workflow.workflow_comment',
	);

/**
 * Edit controller name
 *
 * @var string
 */
	protected $_editController = 'bbs_blocks';

/**
 * テストDataの取得
 *
 * @param bool $isEdit 編集かどうか
 * @return array
 */
	private function __getData($isEdit) {
		$frameId = '6';
		$frameKey = 'frame_3';
		if ($isEdit) {
			$blockId = '2';
			$blockKey = 'block_2';
			$bbsId = '3';
			$bbsKey = 'bbs_2';
			$bbsSettingId = '2';
			$bbsFrameSettingId = '2';
		} else {
			$blockId = null;
			$blockKey = null;
			$bbsId = null;
			$bbsKey = null;
			$bbsSettingId = null;
			$bbsFrameSettingId = null;
		}

		$data = array(
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '2',
				'plugin_key' => $this->plugin,
				'public_type' => '1',
				'from' => null,
				'to' => null,
			),
			'Bbs' => array(
				'id' => $bbsId,
				'key' => $bbsKey,
				'block_id' => $blockId,
				'name' => 'Bbs name',
				'bbs_article_modified' => null,
			),
			'BbsSetting' => array(
				'id' => $bbsSettingId,
				'bbs_key' => $bbsKey,
				'use_comment' => '1',
				'use_like' => '1',
				'use_unlike' => '1',
			),
			'BbsFrameSetting' => array(
				'id' => $bbsFrameSettingId,
				'frame_key' => $frameKey,
				'articles_per_page' => 10,
			),
		);

		return $data;
	}

/**
 * add()アクションDataProvider
 *
 * ### 戻り値
 *  - method: リクエストメソッド（get or post or put）
 *  - data: 登録データ
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderAdd() {
		$data = $this->__getData(false);

		$results = array();
		$results[0] = array('method' => 'get');
		$results[1] = array('method' => 'put');
		$results[2] = array('method' => 'post', 'data' => $data, 'validationError' => false);
		$results[3] = array('method' => 'post', 'data' => $data,
			'validationError' => array(
				'field' => 'Bbs.name',
				'value' => '',
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Bbs name'))
			)
		);

		return $results;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - method: リクエストメソッド（get or post or put）
 *  - data: 登録データ
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEdit() {
		$data = $this->__getData(true);

		$results = array();
		$results[0] = array('method' => 'get');
		$results[1] = array('method' => 'post');
		$results[2] = array('method' => 'put', 'data' => $data, 'validationError' => false);
		$results[3] = array('method' => 'put', 'data' => $data,
			'validationError' => array(
				'field' => 'Bbs.name',
				'value' => '',
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Bbs name'))
			)
		);

		return $results;
	}

/**
 * edit()のテスト(ExceptionError)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditExceptionError
 * @return void
 */
	public function testEditExceptionError($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this);

		if ($exception) {
			$this->controller->Bbs = $this->getMockForModel('Bbses.Bbs', array('getBbs'));
			$this->_mockForReturnFalse('Bbses.Bbs', 'getBbs');
		}

		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		//チェック
		if ($return === 'json') {
			$result = json_decode($this->contents, true);
			$this->assertArrayHasKey('code', $result);
			$this->assertEquals(400, $result['code']);
		} else {
			$this->asserts($assert, $this->contents);
		}

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * editError()アクションDataProvider
 *
 * ### 戻り値
 *  - method: リクエストメソッド（get or post or put）
 *  - data: 登録データ
 * -  urlOptions: URLオプション
 * -  assert: テストの期待値
 * -  exception: Exception
 * -  return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditExceptionError() {
		$data = $this->__getData(true);

		$results = array();
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
			'exception' => 'BadRequestException',
		);

		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
			'exception' => 'BadRequestException',
			'return' => 'json'
		);

		return $results;
	}

/**
 * delete()アクションDataProvider
 *
 * ### 戻り値
 *  - data 削除データ
 *
 * @return array
 */
	public function dataProviderDelete() {
		$data = array(
			'Block' => array(
				'id' => '4',
				'key' => 'block_2',
			),
			'Bbs' => array(
				'key' => 'bbs_2',
			),
		);
		return array(
			array('data' => $data)
		);
	}

}
