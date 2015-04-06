<?php
/**
 * Bbs view for editor template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<ul class="nav nav-tabs" role="tablist">
	<li class="<?php echo ($active === 'block_index' ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/bbses/blocks/index/' . $frameId); ?>">
			<?php echo __d('bbses', 'BBS List'); ?>
		</a>
	</li>
	<li class="<?php echo ($active === 'bbs_frame_setting' ? 'active' : ''); ?>">
		<a href="<?php echo $this->Html->url('/bbses/bbs_frame_settings/edit/' . $frameId); ?>">
			<?php echo __d('bbses', 'Frame Setting'); ?>
		</a>
	</li>
</ul>

<br />