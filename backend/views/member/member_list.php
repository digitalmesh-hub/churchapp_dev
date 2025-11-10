<?php 
   use yii\grid\GridView;
   use yii\helpers\Html;
   use backend\assets\AppAsset;
   AppAsset::register($this);
   ?>
    <?= GridView::widget([
   'dataProvider' => $dataProvider,
   'showHeader'=> false,
   //'filterModel' => $searchModel,
   'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
   'pager' => [
   'prevPageLabel' => '<span aria-hidden="true">« Previous</span> </a>',
   'nextPageLabel' => '<span aria-hidden="true"> Next »</span>',
   'maxButtonCount' => 10,
   ],
   'tableOptions' => ['class' => 'table table-list-search memberlist',
                    'cellspacing'=> "0" , 'cellpadding'=>"0"],
   'columns' => [
       
                [
                 'attribute' => 'member_pic',
                'label' => false,
                 'format' =>'html',
                        'enableSorting' => false,
                                'value' => function ($data) {
                                $image  = $data->member_pic?$data->member_pic: "/Member/default-user.png";
                                return '<img class="fix-size" src="'.Yii::$app->params['imagePath'].$image.'" alt="" height="80" width="70">';
                                //return ($data->member_pic) ?  $data->member_pic : '';
                        },
                        'contentOptions' => ['style' => 'width:10%'],
                ],
                [
                'attribute' => 'firstName',
                'label' => false,
                'format' =>'raw',
                        'enableSorting' => false,
                                'value' => function ($data) {
                                        $title      =  $data->membertitle0['Description'] ?? '';
                                        $firstName  =  $data->firstName ? ($data->firstName):' ';
                                        $middleName =  $data->middleName ? ($data->middleName):' ';
                                        $lastName   =  $data->lastName ? ($data->lastName) :'';
                                        $displayName = $title.' '. $firstName . ' '.$middleName. ' '. $lastName;
                                        $memberNo = '';
                                        if($data->membertype == 0)
                                        {
                                            $memberNo = $data->memberno ? $data->memberno :'';
                                        }
                                    
                                        return '<span class="capitalize" style="float: left; width=100%;padding-bottom: 5px;">'.ucwords($displayName) .'</span>'
                                        .'<span style="float: left; width: 100%; padding-bottom: 5px;">'.$memberNo.'</span>';
                },
                'contentOptions' => ['style' => 'width:25%']
                ],
                
                
                [
                'contentOptions' => ['style' => 'width:30%', 'class'=>"text-center"],
                'class' => 'yii\grid\ActionColumn',
                        'header' => 'Action',
                        'template'=>'{update}&ensp;{delete}',
                        'buttons' => [  
                            'update' => function ($url, $model) {
                                 return Html::a('Manage',
                                            [$model->membertype?'staff-update':'update', 'id' => $model->memberid], ['class' => 'btn btn-success btn-sm',
                                            'title' => 'Manage'
                                        ]);
                            },
                            'delete' => function ($url, $model) {
                                    return  Html::button('Delete', ['class' => 'btn btn-danger btn-sm btn-member-delete',
                                       'title' => Yii::t('yii', 'Delete'),
                                                    'data-member-id' => $model->memberid]);
                            },
                        ],
                
                ],
                [
                'attribute' => 'firstName',
                'label' => false,
                'format' =>'raw',
                    'enableSorting' => false,
                        'value' => function ($data) {
                                $title = $data->spousetitle0['Description'] ?? '';
                                $firstName  =  $data->spouse_firstName ? $data->spouse_firstName:' ';
                                $middleName =  $data->spouse_middleName ? $data->spouse_middleName:' ';
                                $lastName   =  $data->spouse_lastName ? $data->spouse_lastName :'';
                                $displayName = $title.' '. $firstName . ' '.$middleName. ' '. $lastName;
                                return ucwords($displayName);
                },
                'contentOptions' => ['style' => 'width:25%', 'class' =>"text-right capitalize" ]
                ],
                [
                'attribute' => 'firstName',
                'label' => false,
                'format' =>'raw',
                    'enableSorting' => false,
                        'value' => function ($data) {
                            $image  = $data->spouse_pic?$data->spouse_pic: "/Member/default-user.png";
                                    return '<img class="fix-size" src="'.Yii::$app->params['imagePath'].$image.'" alt="" height="80" width="70">';
                        },
                        'contentOptions' => ['style' => 'width:10%', 'class' =>"text-right " ]
                ],
   ],
   ]); ?>
