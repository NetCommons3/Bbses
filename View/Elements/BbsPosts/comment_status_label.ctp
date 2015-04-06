<?php
/**
 * comment status label element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$labels = [
	NetCommonsBlockComponent::STATUS_IN_DRAFT => [
		'class' => 'label-info',
		'message' => __d('net_commons', 'Temporary'),
	],
	NetCommonsBlockComponent::STATUS_APPROVED => [
		'class' => 'label-warning',
		'message' => __d('bbses', 'Comment approving'),
	],
];
$label = isset($labels[$status]) ? $labels[$status] : null;
?>

<?php if ($label): ?>
	<span class="label <?php echo $labels[$status]['class'] ?>">
		<?php echo $labels[$status]['message'] ?>
	</span>
<?php endif;