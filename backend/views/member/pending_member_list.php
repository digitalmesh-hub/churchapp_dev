<?php 
   use yii\grid\GridView;
   use yii\helpers\Html;
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
   
                                $image  = $data->temp_member_pic?$data->temp_member_pic:"/Member/default-user.png";
                                 
                                return '<img src="'.Yii::$app->params['imagePath'].$image.'" alt="">';
                                
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
                                        //return ($data->firstName) ?  $data->firstName : '';
                                        
                                        $title      =  $data->tempmembertitle0['Description'] ? $data->tempmembertitle0['Description']:'' ;
                                        $firstName  =  $data->temp_firstName ? $data->temp_firstName:' ';
                                        $middleName =  $data->temp_middleName ? $data->temp_middleName:' ';
                                        $lastName   =  $data->temp_lastName ? $data->temp_lastName :'';
                                        $displayName = $title.' '. $firstName . ' '.$middleName. ' '. $lastName;
                                        
                                        $memberNo = $data->temp_memberno ? $data->temp_memberno :'';
                                        
                                    
                                        return '<span class="capitalize" style="float: left; width=100%;padding-bottom: 5px;">'.ucwords($displayName) .'</span>'
                                        .'<span style="float: left; width: 100%; padding-bottom: 5px;">'.$memberNo.'</span>';
                },
                'contentOptions' => ['style' => 'width:25%']
                ],
                
                
                [
                        'contentOptions' => ['style' => 'width:30%',
                                              'class'=>"text-center"],
                'class' => 'yii\grid\ActionColumn',
                        'header' => 'Action',
                        'template'=>'{update}',
                        
                                'buttons' => [
                    'update' => function ($url, $model) {
                    
                         return '<div class="inlinerow datelabel">'.date('l jS F Y ', strtotimeNew($model->temp_createddate)) .'</div>'. Html::a('View Changes',
                                    ['member/member-approvel', 'id' => $model->temp_memberid], ['class' => 'btn btn-primary btn-sm btnmanagepending',
                                    'title' => 'View Changes'
                                ]);
                        },
                                
                        ],
                
                        ],
                
                [
                'attribute' => 'firstName',
                'label' => false,
                'format' =>'raw',
                        'enableSorting' => false,
                                'value' => function ($data) {
                                $title = $data->tempspousetitle0['Description'] ?? '';
                                $firstName  =  $data->temp_spouse_firstName ? $data->temp_spouse_firstName:' ';
                                $middleName =  $data->temp_spouse_middleName ? $data->temp_spouse_middleName:' ';
                                $lastName   =  $data->temp_spouse_lastName ? $data->temp_spouse_lastName :'';
                                $displayName = $title.' '. $firstName . ' '.$middleName. ' '. $lastName;
                                return ucwords($displayName);
                },
                'contentOptions' => ['style' => 'width:25%',
                                       'class' =>"text-right capitalize" ]
                ],
            
                [
                'attribute' => 'firstName',
                'label' => false,
                'format' =>'raw',
                        'enableSorting' => false,
                                'value' => function ($data) {
                                
                                $image  = $data->temp_spouse_pic?$data->temp_spouse_pic:"/Member/default-user.png";
                            
                                    return '<img src="'.Yii::$app->params['imagePath'].$image.'" alt="">';
                                //return ($data->member_pic) ?  $data->member_pic : '';
                        },
                        'contentOptions' => ['style' => 'width:10%',
                                               'class' =>"text-right " ]
                ],
                                
   
    
   ],
   ]); ?>