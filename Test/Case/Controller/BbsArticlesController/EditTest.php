<?php
/**
 * BbsArticlesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');

/**
 * BbsArticlesController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Controller\BbsArticlesController
 */
class BbsArticlesControllerEditTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'bbs_articles';

/**
 * テストDataの取得
 *
 * @param string $role ロール
 * @param string $bbsArticleKey キー
 * @return array
 */
	private function __getData($role = null, $bbsArticleKey = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';
		$bbsId = '2';
		$bbsKey = 'bbs_1';
		if ($role === Role::ROOM_ROLE_KEY_GENERAL_USER) {
			if ($bbsArticleKey === 'bbs_article_4') {
				$bbsArticleId = '4';
				$bbsArticleKey = 'bbs_article_4';
			} else {
				$bbsArticleId = '3';
				$bbsArticleKey = 'bbs_article_3';
			}
		} else {
			$bbsArticleId = '1';
			$bbsArticleKey = 'bbs_article_1';
		}

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '2',
				'plugin_key' => $this->plugin,
			),
			'Bbs' => array(
				'id' => $bbsId,
				'key' => $bbsKey,
			),
			'BbsArticle' => array(
				'id' => $bbsArticleId,
				'key' => $bbsArticleKey,
				'bbs_key' => $bbsKey,
				'language_id' => '2',
				'category_id' => '2',
				'status' => null,
				'title' => 'BBSTITLE',
				'content' => 'CONTENT',
			),
			'BbsArticleTree' => array(
				'id' => '1',
				'bbs_key' => $bbsKey,
				'bbs_article_key' => $bbsArticleKey,
				'root_id' => null,
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);

		return $data;
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		$data = $this->__getData();
		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * editアクションのGETテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限のみ
		//--他人の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			'assert' => null,
			'exception' => 'BadRequestException'
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			'assert' => null,
			'exception' => 'BadRequestException', 'return' => 'json'
		);
		//--自分の記事(一度も公開していない)
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = Hash::merge($results[2], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		));
		// 一般の記事に返信がつくと編集できない
		$results[4] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_14'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);

		return $results;
	}

/**
 * editアクションのGETテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByEditable() {
		$data = $this->__getData();
		$results = array();

		//編集権限あり
		//--コンテンツあり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_APPROVAL_WAITING, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][id]', 'value' => $data['BbsArticle']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][key]', 'value' => $data['BbsArticle']['key']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][title]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[BbsArticle][content]', 'value' => null),
		)));
		//--コンテンツなし
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		// 一般の記事に返信があっても、編集権限あれば可能
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_14'),
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[BbsArticle][content]', 'value' => null),
		);

		return $results;
	}

/**
 * editアクションのGETテスト(公開権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data = $this->__getData();
		$results = array();

		//フレームID指定なしテスト
		$results[0] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[0], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		)));

		return $results;
	}

/**
 * editアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditPost() {
		$data = $this->__getData();

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
				'exception' => 'ForbiddenException'
			),
			//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
				'exception' => 'BadRequestException'
			),
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
			//--自分の記事(一度も公開していない)
			array(
				'data' => $this->__getData(Role::ROOM_ROLE_KEY_GENERAL_USER, 'bbs_article_4'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_4'),
			),
			//編集権限あり
			//--コンテンツあり
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			),
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			),
		);
	}

/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__getData();
		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
		);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'BbsArticle.title',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title'))
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'BbsArticle.content',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content'))
				)
			)),
		);
	}

}
