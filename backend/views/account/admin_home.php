<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\assets\AppAsset;
use common\models\extendedmodels\ExtendedInstitution;
use backend\components\widgets\FlashResult;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$assetName = AppAsset::register($this);

$this->title = 'Dashboard';
?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">
<!-- Content -->
<div class="col-md-12 col-sm-12 col-xs-12 contentbg">
    <div class="col-md-12 col-sm-12  col-xs-12">
     <div class="Mtop10">
      <?= FlashResult::widget(); ?>
      </div>
            <!-- Dashboard -->
            <div class="inlinerow Mtop50">
                         <!-- show only when manage institution permission enabled -->
                        <?php if (Yii::$app->user->can('05677a55-ed1a-11e6-b48e-000c2990e707'))
                        { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/institution/edit-institution/'])?>">
                                <div dashstyle="1" class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/institution-icon.png"/>
                                </div>
                                <div class="dashtext">Institution</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when view member list enabled -->
                         <?php if (Yii::$app->user->can('fe083df2-ec49-11e6-b48e-000c2990e707'))
                               { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/member/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/member-icon.png"/>
                                </div>
                                <div class="dashtext">Members</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when list event permission enabled -->
                    <?php if (Yii::$app->user->can('b0d171e3-ec48-11e6-b48e-000c2990e707') || Yii::$app->user->can('bdb35068-ec48-11e6-b48e-000c2990e707')) { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/event/index/'])?>">
                                <div class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/events-icon.png"/>
                                </div>
                                <div class="dashtext">Events</div>
                            </a>
                        </div>
                    <?php } ?>
                        <!-- show only when  view announcement permission enabled -->
                    <?php if (Yii::$app->user->can('7d0b6ab2-ec46-11e6-b48e-000c2990e707') || Yii::$app->user->can('893232ae-ec46-11e6-b48e-000c2990e707') ) { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/news/index/'])?>">
                                <div class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/announcement-icon.png"/>
                                </div>
                                <div class="dashtext">News</div>
                            </a>
                        </div>
                    <?php } ?>
                     <!-- show only when manage bills permission enabled -->
                    <?php if (Yii::$app->user->can('a65b8d57-ec46-11e6-b48e-000c2990e707'))
                    { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/bill/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/payments-icon.png"/>
                                </div>
                                <div class="dashtext">Bills/Payments</div>
                            </a>
                        </div>
                    <?php } ?>
                     <!-- show only when manage user profile permission enabled -->
                    <?php if (Yii::$app->user->can('297e0e10-ec46-11e6-b48e-000c2990e707'))
                    { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/account/edit-admin-profile/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/user-icon.png"/>
                                </div>
                                <div class="dashtext">Admin Profile</div>
                            </a>
                        </div>
                    <?php } ?>
                        <!-- show only when manage feedback permission enabled -->
                        <?php if (Yii::$app->user->can('0f74458a-ec49-11e6-b48e-000c2990e707'))
                               { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/feedback/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/feedback-icon.png"/>
                                </div>
                                <div class="dashtext">Feedback</div>
                            </a>
                        </div> 
                        <?php } ?>
                         <!-- show only when manage commitee permission enabled -->
                        <?php if (Yii::$app->user->can('b46fb1de-ec46-11e6-b48e-000c2990e707'))
                               { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/committee/index/'])?>">
                                <div class="dashicon">
                                  <img src="<?php echo $assetName->baseUrl; ?>/theme/images/committee-icon.png"/>
                                </div>
                                <div class="dashtext">Committee</div>
                            </a>
                        </div>
                        <?php } ?>
                         <!-- show only when list conversations permission enabled -->
                        <?php if (Yii::$app->user->can('22643223-ec48-11e6-b48e-000c2990e707'))
                               { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/conversations/index/'])?>">
                                <div class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/conversations-icon.png"/>
                                </div>
                                <div class="dashtext">Conversations</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when manage prayer request permission enabled -->
                        <?php if (Yii::$app->user->can('ca4ac940-ec4a-11e6-b48e-000c2990e707') && Yii::$app->user->identity->institution->institutiontype == 2)
                        { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/prayerrequest/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/prayer-icon.png"/>
                                </div>
                                <div class="dashtext">Prayer Requests</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when manage Album or View Albums  permission enabled -->
                        <?php if (Yii::$app->user->can('74af2974-ec46-11e6-b48e-000c2990e707') || Yii::$app->user->can('437c49cf-ec46-11e6-b48e-000c2990e707'))
                        { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/album/index/'])?>">
                                <div class="dashicon">
                                  <img src="<?php echo $assetName->baseUrl; ?>/theme/images/album-icon.png"/>
                                </div>
                                <div class="dashtext">Photo Album</div>
                            </a>
                        </div>
                        <?php } ?>
                          <!-- show only when manage family units permission enabled -->
                        <?php if (Yii::$app->user->can('04cd913a-ec49-11e6-b48e-000c2990e707') && Yii::$app->user->identity->institution->institutiontype == 2)
                        { ?>
                         <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/familyunit/index/'])?>">
                                <div class="dashicon">
                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/familyunit-icon.png"/>
                                </div>
                                <div class="dashtext">Family Unit</div>
                            </a>
                        </div>
                        <?php } ?>
                         <!-- show only when manage affilated institution permission enabled -->
                        <?php if (Yii::$app->user->can('5a1562b9-ed1e-11e6-b48e-000c2990e707'))
                        { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/affiliatedinstitution/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/affiliates-icon.png"/>
                                </div>
                                <div class="dashtext">Affiliated Institution</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when Manage Food order permission enabled -->
                        <?php if (Yii::$app->user->can('fcb852d5-0005-11e7-b48e-000c2990e707')) { ?>
                         <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/orders/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/food-icon.png"/>
                                </div>
                                <div class="dashtext">Food Booking</div>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- show only when Manage Restaurant permission enabled -->
                        <?php if (Yii::$app->user->can('69b1b6c1-fffc-11e6-b48e-000c2990e707')) { ?>
                         <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/restaurant/index/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/restaurant-icon.png"/>
                                </div>
                                <div class="dashtext">Manage Restaurant</div>
                            </a>
                        </div>
                        <?php } ?>
                          <!-- show this menu only when Manage Roles and Privileges is enabled -->
                        <?php if (Yii::$app->user->can('a83cbb99-fff4-11e6-b48e-000c2990e707')) { ?>
                         <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/rbac/roles/','id' => Yii::$app->user->identity->institution->id])?>">
                                <div class="dashicon">
                                  <img src="<?php echo $assetName->baseUrl; ?>/theme/images/roles-privilege-icon.png"/>
                                </div>
                                <div class="dashtext">Roles</div>
                            </a>
                        </div> 
                        <?php } ?> 
                          <!-- show this menu only when More url is enabled -->
                        <?php if (Yii::$app->user->can('7304da2e-f2ac-11e6-b48e-000c2990e707') && Yii::$app->user->identity->institution->moreenabled) { ?>  
                        <div class="col-md-2 col-sm-3 col-xs-6">
                        <?php $moreurl = Yii::$app->user->identity->institution->moreurl;?>
                        <a href="<?= $moreurl ?>" target="_blank">
                                <div class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/more-icon.png"/>
                                </div>
                                <div class="dashtext">More</div>
                            </a>
                        </div>
                        <?php } ?>
                         <!-- show only when Manage Restaurant permission enabled -->
                        <?php if (Yii::$app->user->can('9c22eb89-6f01-49b1-b7c5-10254330fcce')) { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/beverages/'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/bar.png"/>
                                </div>
                                <div class="dashtext">Manage Beverages</div>
                            </a>
                        </div>
                        <?php } ?>
                        <?php if (Yii::$app->user->can('d560f85e-46c4-4b14-b2b4-9e6d613f4d2b')) { ?>
                            <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/beverages/manage-booking'])?>">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/bar.png"/>
                                </div>
                                <div class="dashtext">Beverage Booking</div>
                            </a>
                            </div>
                        <?php } ?>
                        <?php if (Yii::$app->user->can('0c26fee6-3df8-4cd6-83d9-45a556a75b64'))
                        { ?>
                        <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href="<?=Url::to(['/qurbana/list-qurbana/'])?>">
                                <div dashstyle="1" class="dashicon">
                                <img src="<?php echo $assetName->baseUrl; ?>/theme/images/qurbana-icon.png"/>
                                </div>
                                <div class="dashtext">Qurbana</div>
                            </a>
                        </div>
                        <?php } ?>
                         <div class="col-md-2 col-sm-3 col-xs-6">
                            <a href= "<?=Url::home(true)?>help/index.html" target="_blank">
                                <div class="dashicon">
                                 <img src="<?php echo $assetName->baseUrl; ?>/theme/images/help-icon.png"/>
                                </div>
                                <div class="dashtext">Help</div>
                            </a>
                        </div>    
            </div>
            <!-- /. Dashboard -->  
    </div>
</div>
</div>
