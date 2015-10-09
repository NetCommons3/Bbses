<?php
/**
 * Sort element of BbsArticles index
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */


$url = NetCommonsUrl::actionUrlAsArray(Hash::merge(array(
	'plugin' => 'bbses',
	'controller' => 'bbs_articles',
	'action' => 'index',
	'block_id' => Current::read('Block.id'),
	'frame_id' => Current::read('Frame.id'),
), $this->Paginator->params['named']));

$curretSort = isset($this->Paginator->params['named']['sort']) ? $this->Paginator->params['named']['sort'] : 'BbsArticle.created';
$curretDirection = isset($this->Paginator->params['named']['direction']) ? $this->Paginator->params['named']['direction'] : 'desc';

$options = array(
	'BbsArticle.created.desc' => array(
		'label' => __d('bbses', 'Latest post order'),
		'sort' => 'BbsArticle.created',
		'direction' => 'desc'
	),
	'BbsArticle.created.asc' => array(
		'label' => __d('bbses', 'Older post order'),
		'sort' => 'BbsArticle.created',
		'direction' => 'asc'
	),
	'BbsArticleTree.bbs_article_child_count.desc' => array(
		'label' => __d('bbses', 'Descending order of comments'),
		'sort' => 'BbsArticleTree.bbs_article_child_count',
		'direction' => 'desc'
	),
);
?>

<span class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $options[$curretSort . '.' . $curretDirection]['label']; ?>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $sort) : ?>
			<li>
				<?php echo $this->Paginator->link($sort['label'], array('sort' => $sort['sort'], 'direction' => $sort['direction']), array('url' => $url)); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
