<?php 

use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

echo Html::hiddenInput('dependant-id', 0,
    [
        'id'=>'dependant-id'
    ]
); 
echo Html::hiddenInput(
            'admin-get-dependant-details-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-dependant-details-Url'],
            [
                'id'=>'admin-get-dependant-details-Url'
            ]
        ); 
?>
<div role="tabpanel" class="tab-pane fade" id="dependants">
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h4 style="margin-top: 0;"><i class="glyphicon glyphicon-search"></i> Search for Dependant to Add to Committee</h4>
            <p style="margin-bottom: 0;">Search for member dependants (children, parents, etc.) and assign them to committee positions.</p>
        </div>
        <div class="inlinerow">
            <div class="col-md-5 col-sm-5">
                <div class="labelbox">
                    <strong style="font-size: 15px;">Search Dependant by Name</strong>
                </div>
                <div class="inlinerow Mtop5">
                   <input type="text" class="form-control dependant-name" 
                          id="dependant-search-name" 
                          placeholder="Type dependant name (minimum 2 characters)" 
                          autocomplete="off"
                          style="font-size: 14px; padding: 10px;">
                </div>
            </div>
            <div class="col-md-3 col-sm-3">
                <div class="labelbox">&nbsp;</div>
                <div class="inlinerow Mtop5">
                <?= Html::button('<i class="glyphicon glyphicon-search"></i> Search Dependant', ['class' => 'btn btn-primary btn-lg add-dependant', 'title' => Yii::t('yii', 'Search'),"id"=>"button-add-dependant"]) ?>
                </div>
            </div>
        </div>
        <div class="inlinerow Mtop20" id="dependantSearchResults" style="display:none;">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading" style="background-color: #3498db; border-color: #3498db;">
                        <h4 style="margin: 5px 0; color: white;">
                            <i class="glyphicon glyphicon-list"></i> <strong>Search Results - Click to Select Dependant</strong>
                        </h4>
                    </div>
                    <div class="panel-body" id="dependantResultsList" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    <div id="CommitteeDependantDetailsDiv"></div>
</div>
