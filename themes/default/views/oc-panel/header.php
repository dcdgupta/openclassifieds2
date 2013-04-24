<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="navbar navbar-inverse navbar-fixed-top">

    <div class="navbar-inner">

        <div class="container">

            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="brand" href="<?=Route::url('oc-panel',array('controller'=>'home'))?>"><?=__('Dashboard')?></a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <?=nav_link(_('Visit Site'),'home','icon-home icon-white','index','default')?>
                	<?=sidebar_link(__('Stats'),'stats','index','oc-panel','icon-align-left icon-white')?>
                    <?=sidebar_link(__('Widgets'),'widget','index','oc-panel','icon-move icon-white')?>
            	    <li  class="dropdown "><a href="#" class="dropdown-toggle"
            		      data-toggle="dropdown"><i class="icon-plus icon-white"></i> <?=__('New')?> <b class="caret"></b></a>
                    	<ul class="dropdown-menu">
                            <?=sidebar_link(__('Category'),'category')?>
                            <?=sidebar_link(__('Location'),'location')?>
                            <?=sidebar_link(__('User'),'user')?>
                            <?=sidebar_link(__('Role'),'role')?>
                            <?=sidebar_link(__('Content'),'content')?>
                    		<li class="divider"></li>
                    		<li><a href="<?=Route::url('post_new')?>">
                    			<i class="icon-pencil"></i><?=__('Publish new ')?></a>	</li>
                    	</ul>
            	   </li>
                   <?=sidebar_link(__('Moderation'),'ad','moderate','oc-panel','icon-list-alt icon-white')?>
                </ul>
                
            </div> <!--/.nav-collapse -->

            <div class="btn-group pull-right visible-desktop">
                <?=View::factory('widget_login')?>
            </div>

        </div><!--/.container -->

    </div><!--/.navbar-inner -->

</div><!--/.navbar -->