<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\User;
use yii\helpers\ArrayHelper;

$assetName = AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo $assetName->baseUrl; ?>/theme/images/favicon.ico?v=1" type="image/x-icon" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
  </head>

  <body>
  <?php $this->beginBody() ?>
  <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?>
  <?php echo @Html::hiddenInput('controller', yii::$app->controller->id, array('id'=>'controller'));?>
    <!-- overlay -->
    <div class="overlay" style="display: none;">
       <div class="loader"><img width="150px" src="<?php echo $assetName->baseUrl; ?>/theme/images/loader.gif"/></div>
    </div>
    <!-- /.overlay -->
    <!-- Header -->
    <div class="header">
         <div class="container">
         <div class="row">
              <div class="col-md-12 col-sm-12 Mtop10">
                   <div class="col-md-4 col-sm-5"><a href="<?php echo Url::to(['/account/home']); ?>"><img width="60" src="<?php echo $assetName->baseUrl; ?>/theme/images/church-logo.png" title="Go home"/></a></div>
                   <div class="col-md-4 col-sm-5 pull-right text-right accountinfo">
                        <?php if (Yii::$app->user->isGuest) : ?>
                            <a href="<?php echo Url::to(['/account/login']); ?>">Login</a>
                        <?php else : ?>
                            <a href="javascript:void(0);">User :  <?php echo Html::encode(Yii::$app->user->identity->userprofile->firstname. ' ' . Yii::$app->user->identity->userprofile->middlename.' '. Yii::$app->user->identity->userprofile->lastname); ?></a>
                            &nbsp;&nbsp;
                            <a href="<?php echo Url::to(['/account/logout']); ?>"><img id="LogOut" src="<?php echo $assetName->baseUrl; ?>/theme/images/logout-icon.png" title="Logout"></a>
                       <?php endif; ?>
                       
                   </div>
              </div>
         </div>
         </div>
    </div>
    <!-- Header closed -->
    <!-- Menubar -->
<div class="menubar">
    <div class="container">
          <nav class="navbar navbar-inverse">
              
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>                  
                </div>
                  <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                    
                    <li class="<?php if(Yii::$app->controller->id == 'account' || Yii::$app->controller->id == 'institution' || Yii::$app->controller->id == 'affiliatedinstitution'
                    		|| Yii::$app->controller->id == 'conversations' ||Yii::$app->controller->id == 'committee' ||Yii::$app->controller->id == 'prayerrequest' || Yii::$app->controller->id == 'feedback'
                    		|| Yii::$app->controller->id == 'familyunit' || Yii::$app->controller->id == 'title' || Yii::$app->controller->id == 'staffdesignation' ) { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Main Menu<span class="caret"></span></a>
                       <ul class="dropdown-menu">
                                <li class ="<?= Yii::$app->controller->id == 'account' && Yii::$app->controller->action->id == 'home' ?"active":'' ?>" ><a href="<?=Url::to(['/account/home'])?>">Dashboard</a></li>
                              <!-- show only when manage institution permission enabled -->
                               <?php if (Yii::$app->user->can('05677a55-ed1a-11e6-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'institution'?"active":'' ?>" ><a href="<?=Url::to(['/institution/edit-institution/'])?>">Institution</a></li>
                                <?php } ?>
                                <!-- show only when manage bills permission enabled -->
                               <?php if (Yii::$app->user->can('a65b8d57-ec46-11e6-b48e-000c2990e707'))
                               { ?>
                                <li><a href="<?=Url::to(['/bill/index/'])?>">Bills</a></li>
                               <?php } ?>
                                <!-- show only when manage user profile permission enabled -->
                              <?php if (Yii::$app->user->can('297e0e10-ec46-11e6-b48e-000c2990e707'))
                              { ?>
                                <li class ="<?= Yii::$app->controller->id == 'account' && Yii::$app->controller->action->id == 'edit-admin-profile' ?"active":'' ?>"><a href="<?=Url::to(['/account/edit-admin-profile/'])?>">Admin Profile</a></li>
                                <?php } ?>
                               <!-- show only when manage affilated institution permission enabled -->
                               <?php if (Yii::$app->user->can('5a1562b9-ed1e-11e6-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'affiliatedinstitution'?"active":'' ?>" ><a href="<?=Url::to(['/affiliatedinstitution/index/'])?>">Affiliated Institutions</a></li> <?php } ?>
                                <!-- show only when list conversations permission enabled -->
                               <?php if (Yii::$app->user->can('22643223-ec48-11e6-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'conversations'?"active":'' ?>" ><a href="<?=Url::to(['/conversations/index/'])?>">Conversations</a></li>
                                <?php } ?>
                                  <!-- show only when manage commitee permission enabled -->
                              <?php if (Yii::$app->user->can('b46fb1de-ec46-11e6-b48e-000c2990e707'))
                               { ?>
                                 <li class ="<?= Yii::$app->controller->id == 'committee'?"active":'' ?>" ><a href="<?=Url::to(['/committee/index/'])?>">Committee</a></li>
                                 <?php } ?>
                                <!-- show only when manage prayer request permission enabled -->
                               <?php if (Yii::$app->user->can('ca4ac940-ec4a-11e6-b48e-000c2990e707') &&  Yii::$app->user->identity->institution->institutiontype == 2)
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'prayerrequest'?"active":'' ?>" ><a href="<?=Url::to(['/prayerrequest/index/'])?>">Prayer Request</a></li>
                               <?php } ?>
                               <!-- show only when manage feedback permission enabled -->
                              <?php if (Yii::$app->user->can('0f74458a-ec49-11e6-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'feedback'?"active":'' ?>" ><a href="<?=Url::to(['/feedback/index/'])?>">Feedback</a></li> <?php } ?>
                                <!-- show only when manage family units permission enabled -->
                              <?php if (Yii::$app->user->can('04cd913a-ec49-11e6-b48e-000c2990e707') &&  Yii::$app->user->identity->institution->institutiontype == 2)
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'familyunit'?"active":'' ?>"><a href="<?=Url::to(['/familyunit/index/'])?>">Family Unit</a>
                              </li> 
                              <li class ="<?= Yii::$app->controller->id == 'zone'?"active":'' ?>"><a href="<?=Url::to(['/zone/index/'])?>">Zone</a>
                              </li> 
                              <?php } ?>
                                <!-- show only when Manage Titles permission enabled -->
                               <?php if (Yii::$app->user->can('4904f428-ec4b-11e6-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'title'?"active":'' ?>" ><a href="<?=Url::to(['/title/index/'])?>">Title</a></li>
                                <?php } ?>
                                 <!-- show only when Manage Staff designations permission enabled -->
                            <?php if (Yii::$app->user->can('1041a93a-153b-11e7-b48e-000c2990e707'))
                               { ?>
                                <li class ="<?= Yii::$app->controller->id == 'staffdesignation'?"active":'' ?>"><a href="<?=Url::to(['/staffdesignation/index/'])?>">Staff Designation</a>
                                </li>
                                <?php } ?>
                                
                            </ul>
                    </li>
                    <!-- show only when view member list or add/edit member permission enabled -->
                  <?php if (Yii::$app->user->can('fe083df2-ec49-11e6-b48e-000c2990e707') || Yii::$app->user->can('1092473e-ec4a-11e6-b48e-000c2990e707'))
                               { ?>
                    <li class="<?php if(Yii::$app->controller->id == 'member' && (Yii::$app->controller->action->id == 'index' || Yii::$app->controller->action->id == 'create' || Yii::$app->controller->action->id == 'update' ) ) { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Member<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                              <?php if (Yii::$app->user->can('fe083df2-ec49-11e6-b48e-000c2990e707')){?>
                                <li class ="<?= Yii::$app->controller->id == 'member' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>" ><a href="<?=Url::to(['/member/index/'])?> ">Member Listing</a></li>
                              <?php } ?>
                              <?php if (Yii::$app->user->can('1092473e-ec4a-11e6-b48e-000c2990e707')){?>
                                <li class ="<?= Yii::$app->controller->id == 'member' && Yii::$app->controller->action->id == 'create' ?"active":'' ?>" ><a href="<?=Url::to(['/member/create/'])?>">Member Registration</a></li>
                                <?php } ?>

                                <?php if(Yii::$app->user->can('fe083df2-ec49-11e6-b48e-000c2990e707') && in_array(Yii::$app->user->identity->institution->id,[66,77])){ ?>
                                  <li class ="<?= Yii::$app->controller->id == 'member' && Yii::$app->controller->action->id == 'new-registered-members' ?"active":'' ?>" ><a href="<?=Url::to(['/member/new-registered-members'])?> ">New member request</a></li>
                                <?php } ?>
                            </ul>
                    </li>
                    <?php } ?>
                    <!-- show only when view member list or add/edit member permission enabled -->
                    <?php if (Yii::$app->user->can('27122647-ec4a-11e6-b48e-000c2990e707') || Yii::$app->user->can('316c0865-ec4a-11e6-b48e-000c2990e707'))
                               { ?>
                     <li class="<?php if(Yii::$app->controller->id == 'member' && (Yii::$app->controller->action->id == 'staff-list' || Yii::$app->controller->action->id == 'staff-register' ) ) { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Staff<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                        <?php if (Yii::$app->user->can('27122647-ec4a-11e6-b48e-000c2990e707')){?>
                        <li class ="<?= Yii::$app->controller->id == 'member' && Yii::$app->controller->action->id == 'staff-list' ?"active":'' ?>" ><a href="<?=Url::to(['/member/staff-list'])?>">Staff Listing</a></li> <?php } ?>
                          <?php if (Yii::$app->user->can('316c0865-ec4a-11e6-b48e-000c2990e707')){?>
                        <li class ="<?= Yii::$app->controller->id == 'member' && Yii::$app->controller->action->id == 'staff-register' ?"active":'' ?>" ><a href="<?=Url::to(['/member/staff-register'])?>">Staff Registration</a></li>
                          <?php } ?>
                            </ul>
                    </li>
                    <?php } ?>
                  <!-- show only when list event permission enabled -->
                    <?php if (Yii::$app->user->can('b0d171e3-ec48-11e6-b48e-000c2990e707') || Yii::$app->user->can('bdb35068-ec48-11e6-b48e-000c2990e707')) { ?>

                      <li class="<?php if(Yii::$app->controller->id == 'event') { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Events<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                                <li class ="<?= Yii::$app->controller->id == 'event' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>" ><a href="<?=Url::to(['/event/index/'])?>">Event Listing</a></li>
                                 <li class ="<?= Yii::$app->controller->id == 'event' && Yii::$app->controller->action->id == 'create' ?"active":'' ?>"><a href="<?=Url::to(['/event/create/'])?>">Event Registration</a></li> 
                                  <!-- show only when View Album permission enabled -->
                              <?php if (Yii::$app->user->can('437c49cf-ec46-11e6-b48e-000c2990e707'))
                               { ?>
                                 <li class ="<?= Yii::$app->controller->id == 'album' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>"><a href="<?=Url::to(['/album/index/'])?>">Photo Album</a></li>
                                <?php } ?>
                            </ul>
                      </li>
                    <?php } ?> 
                  <!-- show only when  view announcement permission enabled -->
                    <?php if (Yii::$app->user->can('7d0b6ab2-ec46-11e6-b48e-000c2990e707') || Yii::$app->user->can('893232ae-ec46-11e6-b48e-000c2990e707') ) { ?>
                    <li class="<?php if(Yii::$app->controller->id == 'news') { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">News<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                         <?php if (Yii::$app->user->can('7d0b6ab2-ec46-11e6-b48e-000c2990e707')) { ?>
                                <li class ="<?= Yii::$app->controller->id == 'news' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>"><a href="<?=Url::to(['/news/index/'])?>">News Listing</a></li>
                              <?php } ?>
                              <?php if (Yii::$app->user->can('893232ae-ec46-11e6-b48e-000c2990e707')) { ?>
                                <li class ="<?= Yii::$app->controller->id == 'event' && Yii::$app->controller->action->id == 'create' ?"active":'' ?>"><a href="<?=Url::to(['/news/create/'])?>">News Registration</a>
                                </li>
                                <?php } ?>
                            </ul>
                    </li>
                    <?php } ?>
                   <!--  show only when Manage Restaurant or Manage Food Orders permission is enabled -->
                    <?php if (Yii::$app->user->can('69b1b6c1-fffc-11e6-b48e-000c2990e707') ||Yii::$app->user->can('fcb852d5-0005-11e7-b48e-000c2990e707') ) { ?>
                     <li class="<?php if(Yii::$app->controller->id == 'restaurant' || Yii::$app->controller->id == 'orders') { echo 'active'; } ?>">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Restaurant<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                        <!-- show only when Manage Restaurant permission enabled -->
                    <?php if (Yii::$app->user->can('69b1b6c1-fffc-11e6-b48e-000c2990e707')) { ?>
                      <li class ="<?= Yii::$app->controller->id == 'restaurant' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>"><a href="<?=Url::to(['/restaurant/index/'])?>">Manage Restaurant</a></li>
                    <?php } ?>
                    <!-- show only when Manage Food order permission enabled -->
                    <?php if (Yii::$app->user->can('fcb852d5-0005-11e7-b48e-000c2990e707')) { ?>
                      <li class ="<?= Yii::$app->controller->id == 'orders' && Yii::$app->controller->action->id == 'index' ?"active":'' ?>"><a href="<?=Url::to(['/orders/index/'])?>">Food Booking</a></li>
                      <?php } ?>
                      </ul>
                    </li>
                    <?php } ?>
                    <!-- show this menu only when Manage Roles and Privileges is enabled -->
                    <?php if (Yii::$app->user->can('a83cbb99-fff4-11e6-b48e-000c2990e707')) { ?> 
                    <li>
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Roles<span class="caret"></span></a>
                        <ul role="menu" class="dropdown-menu">
                          <li><a href="<?=Url::to(['/rbac/roles/','id' => Yii::$app->user->identity->institution->id])?>">Manage Roles</a></li> 
                        </ul>
                    </li>
                    <?php } ?>
                     <!-- show this menu only when Manage BillingPrivileges is enabled -->
                    <?php if (Yii::$app->user->can('145ddf4b-1e84-4027-8026-f3de456c6400')) { ?> 
                     <li>
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Billing<span class="caret"></span></a>
                       <ul role="menu" class="dropdown-menu">
                                <li><a href="<?=Url::to(['/bill-manager/home/'])?>">Manage Bills</a></li>
                                <li><a href="<?=Url::to(['/transaction/'])?>">Transactions</a></li>
                            </ul>
                    </li>
                    <?php } ?>
                    <!-- show this menu only when bevco Privileges is enabled -->
                    <?php if (Yii::$app->user->can('9c22eb89-6f01-49b1-b7c5-10254330fcce') 
                    || Yii::$app->user->can('d560f85e-46c4-4b14-b2b4-9e6d613f4d2b') ) { ?>
                      <li>
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Beverages<span class="caret"></span></a>
                      <ul role="menu" class="dropdown-menu">
                          <?php if (Yii::$app->user->can('9c22eb89-6f01-49b1-b7c5-10254330fcce')) { ?>
                          <li><a href="<?=Url::to(['/beverages'])?>">Manage Beverages</a></li>
                          <?php } ?>
                          <?php if (Yii::$app->user->can('d560f85e-46c4-4b14-b2b4-9e6d613f4d2b')) { ?>
                          <li><a href="<?=Url::to(['/beverages/manage-booking'])?>">Beverage Booking</a></li>
                          <?php } ?>
                      </ul>
                      </li>
                    <?php } ?>
                    <li><a href= "<?=Url::home(true)?>help/index.html" target="_blank">Help</a></li>
                     <?php if (isset(Yii::$app->session['impersonation_user'])): ?>
                                        <li>
                                          <a href="/account/exit-impersonate">
                                                <i class="fa fa-sign-out fa-fw pull-right"></i>
                                                Exit Impersonate : <?= Yii::$app->session['impersonation_user']['email'] ?>
                                            </a>
                                        </li>
                    <?php endif ?>
                  </ul>
                </div><!--/.nav-collaps
            
            </nav> 
    </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="content-div"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary Mtop10" data-dismiss="modal" title="Cancel">Cancel</button>
                <button type="button" class="btn btn-primary Mtop10" data-dismiss="modal" title="OK">OK</button>
            </div>
        </div>
    </div>
</div>
    <!-- modal end --> 

    <!-- Contents -->
    <div class="container">
         <div class="row">
              <?= $content ?> 
         </div>
    </div>
</nav>
</div>
</div>
  <?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>