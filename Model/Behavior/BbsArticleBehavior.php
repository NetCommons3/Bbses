<?php
/**
 * BbsArticle Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * BbsArticle Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Categories\Model\Behavior
 */
class BbsArticleBehavior extends ModelBehavior {

/**
 * Update bbs_article_modified
 *
 * @param object $model instance of model
 * @param string $bbsKey bbses.key
 * @param int $languageId languages.id
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function updateBbsByBbsArticle(Model $model, $bbsKey, $languageId) {
		$model->loadModels([
			'Bbs' => 'Bbses.Bbs',
		]);

		$db = $model->getDataSource();

		$conditions = array(
			'bbs_key' => $bbsKey,
			'language_id' => $languageId,
			'is_latest' => true
		);
		$count = $model->find('count', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		if ($count === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		$article = $model->find('first', array(
			'recursive' => -1,
			'fields' => 'modified',
			'conditions' => $conditions,
			'order' => array('modified' => 'desc'),
		));
		if ($article === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		if ($article) {
			$update['bbs_article_modified'] = $db->value($article[$model->alias]['modified'], 'string');
			if (! $model->Bbs->updateAll($update, array('Bbs.key' => $bbsKey))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

/**
 * Update bbs_article_child_count
 *
 * @param object $model instance of model
 * @param int $rootId RootId for root BbsArticle
 * @param int $languageId languages.id
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function updateBbsArticleChildCount(Model $model, $rootId, $languageId) {
		$rootId = (int)$rootId;

		$conditions = array(
			'BbsArticleTree.root_id' => $rootId,
			'BbsArticle.language_id' => $languageId,
			'BbsArticle.is_active' => true
		);
		$count = $model->find('count', array(
			'recursive' => 0,
			'conditions' => $conditions,
		));
		if ($count === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		$update = array('BbsArticleTree.bbs_article_child_count' => $count);
		$conditions = array('BbsArticleTree.id' => $rootId);
		if (! $model->updateAll($update, $conditions)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
	}

/**
 * Title of reply
 *
 * @param object $model instance of model
 * @param string $title bbs_articles.title
 * @return string bbs_articles.title
 */
	public function getReplyTitle(Model $model, $title) {
		$matches = array();
		if (preg_match('/^Re(\d+)?:/', $title, $matches)) {
			if (isset($matches[1])) {
				$count = (int)$matches[1];
			} else {
				$count = 1;
			}
			$title = trim($title);
			$result = preg_replace('/^Re(\d+)?:[ ]*/', 'Re' . ($count + 1) . ': ', $title);
		} else {
			$result = 'Re: ' . $title;
		}

		return $result;
	}

/**
 * Content of reply
 *
 * @param object $model instance of model
 * @param string $content bbs_articles.content
 * @return string bbs_articles.content
 */
	public function getReplyContent(Model $model, $content) {
		$result = '<br><blockquote>' . $content . '</blockquote>';
		return $result;
	}

/**
 * 子記事数の取得
 *
 * @param object $model 呼び出し元のモデル
 * @param array $bbsKey 掲示板Key
 * @param array $bbsArticles 根記事データ
 * @param array $articleTreeIds TreeのIDリスト
 * @return string bbs_articles.content
 */
	public function getChildrenArticleCounts(Model $model, $bbsKey, $bbsArticles, $articleTreeIds) {
		$model->loadModels([
			'BbsArticleTree' => 'Bbses.BbsArticleTree',
		]);

		$query = array(
			'recursive' => -1,
			'fields' => ['BbsArticleTree.root_id', 'COUNT(*) AS bbs_article_child_count'],
			'conditions' => $model->getWorkflowConditions(array(
				'BbsArticleTree.root_id' => $articleTreeIds,
				'BbsArticleTree.bbs_key' => $bbsKey,
			)),
			'joins' => [
				[
					'type' => 'INNER',
					'table' => $model->BbsArticleTree->table,
					'alias' => $model->BbsArticleTree->alias,
					'conditions' => $model->belongsTo['BbsArticleTree']['conditions'],
				]
			],
			'group' => array('BbsArticleTree.root_id'),
		);
		$results = $model->find('all', $query);
		$counts = [];
		foreach ($results as $result) {
			$counts[$result['BbsArticleTree']['root_id']] = $result[0]['bbs_article_child_count'];
		}

		foreach ($bbsArticles as $i => $article) {
			if (isset($counts[$article['BbsArticleTree']['id']])) {
				$count = $counts[$article['BbsArticleTree']['id']];

				$beforeCount = $count - $bbsArticles[$i]['BbsArticleTree']['bbs_article_child_count'];
				$bbsArticles[$i]['BbsArticleTree']['approval_bbs_article_child_count'] = (string)$beforeCount;

				$bbsArticles[$i]['BbsArticleTree']['bbs_article_child_count'] = $count;
			}
		}

		return $bbsArticles;
	}

/**
 * 子記事の件名の取得(全権一覧で使用する)
 *
 * @param object $model 呼び出し元のモデル
 * @param array $articleTreeIds TreeのIDリスト
 * @return array
 */
	public function getChildrenArticleTitles(Model $model, $articleTreeIds) {
		if (isset($model->actsAs['NetCommons.Trackable']['foreignFields']['created_by'])) {
			$trackable = $model->actsAs['NetCommons.Trackable']['foreignFields']['created_by'];
		} else {
			$trackable = [
				'TrackableCreator.id',
				'TrackableCreator.handlename',
			];
		}

		$query = array(
			'recursive' => 0,
			'fields' => array_merge([
				'BbsArticle.id', 'BbsArticle.title', 'BbsArticle.title_icon', 'BbsArticle.created',
				'BbsArticle.key', 'BbsArticle.status',
				'BbsArticleTree.id',
			], $trackable),
			'conditions' => $model->getWorkflowConditions(array(
				'BbsArticleTree.root_id' => $articleTreeIds,
			)),
		);
		$results = $model->find('all', $query);

		$articles = [];
		foreach ($results as $result) {
			$articles[$result['BbsArticleTree']['id']] = $result;
		}

		return $articles;
	}

}
