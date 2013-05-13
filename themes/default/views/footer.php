<?php defined('SYSPATH') or die('No direct script access.');?>
<hr>

<footer>

<?foreach ( widgets::get('footer') as $widget):?>
<div class="span3">
    <?=$widget->render()?>
</div>
<?endforeach?>


<!--This is the license for Open Classifieds, do not remove -->
<p>&copy;
<?if (Theme::get('premium')!=1):?>
    <a href="http://open-classifieds.com?utm_source=<?=$_SERVER['SERVER_NAME']?>&utm_medium=oc_footer&utm_campaign=<?=date('Y-m-d')?>" title="Classifieds Software PHP">Open Classifieds</a> 
<?endif?>    
2009 - <?=date('Y')?>

<?if(Core::config('appearance.theme_mobile')!=''):?>
- <a href="<?=Route::url('default')?>?mobile=active"><?=__('Mobile Version')?></a>
<?endif?>
</p>
</footer>