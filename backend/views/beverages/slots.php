<?php
use  common\models\extendedmodels\ExtendedBevcoSlots;
use yii\helpers\Html;
?>
<?php if(empty($slots)) {?> 
<?php
  $start_time = strtotimeNew($settings['start_time']); 
  $end_time = strtotimeNew($settings['end_time']);
  $duration = (int)$settings['slot_duration'];
  $no = 1;
  $re = $settings['sales_per_slot'];
?>
<?php while ($start_time < $end_time) {
  $t1 = $start_time;
  $start_time = strtotimeNew('+'.$duration.' '.'minutes', $t1);
  $orderDate = strtotimeNew($order_date);
  $dt = new DateTime('', new DateTimeZone('Asia/Kolkata'));
  $now = $dt->getTimestamp() + $dt->getOffset();
  $rt = $dt->modify('today');
  $tr = $rt->getTimestamp() + $rt->getOffset();
  $datediff = abs(round(($tr - $orderDate) / (60 * 60 * 24)));

  $x = ($datediff > 0) ? strtotimeNew('+'.$datediff.'days', $start_time) : $start_time;
  $expired = ($x < $now) ? true :false;
?>
<div class="col-md-3 col-sm-6 col-xs-12">
  <div class="tile">
    <div class="tile_perimeter">
      <div class="tile-title">
          <h3>Slot Number : <?= $no ?></h3>
      </div>
      <div class="tile_content selection-panel <?= ($expired)? 'tile_disabled' : ''?>" data-row="<?= $no?>">
        <dl>
          <dt><strong>Time : <?= date("g:i a", $t1);?> - <?= date("g:i a", $start_time);?></strong></dt>
        </dl>
          <dl>
            <dt>
              <strong> Active Bookings: 0</strong>
            </dt>
          </dl>
          <dl>
            <dt>
              <strong> Remaining: <?= $re ?></strong>
            </dt>
          </dl>
          <?php if ($expired): ?>
              <dl>
                <dt>
                 <p class="slot-expired-text">Expired</p>
                </dt>
              </dl>
          <?php endif ?>
      </div>
      <a
        class="more-info" 
        title="Slot : <?=$no?>" 
        data-toggle="popover"
        data-trigger="hover"
        data-placement="top"
        data-content="<p>No bookings yet</p>"
      >
      <i class="glyphicon glyphicon-info-sign"></i> More info</a>
    </div>
  </div>
</div>
<?php 
  $no++;
  if(strtotimeNew('+'.$duration.' '.'minutes', $start_time) > $end_time) {
    $end_time = $start_time;
  }
}?>
<?php } else {?>
<?php foreach($slots as $slot) {
  $class = '';           
  $re = $slot->_settings['sales_per_slot'] - $slot->orderCount;
  $pr = round(($re * 100 / $slot->_settings['sales_per_slot']), PHP_ROUND_HALF_UP);
  if($pr <= 0) {
      $class = 'tile-full';
  } else if($pr <= 20) {
      $class = 'tile-almost-full';
  } else if($pr <= 50) {
      $class = 'tile-half-full'; 
  }

  $orderDate = strtotimeNew($order_date.'00:00:00');
  $dt = new DateTime('', new DateTimeZone('Asia/Kolkata'));
  $now = $dt->getTimestamp() + $dt->getOffset();
  $rt = $dt->modify('today');
  $tr = $rt->getTimestamp() + $rt->getOffset();
  $datediff = abs(round(($tr - $orderDate) / (60 * 60 * 24)));
  $x = ($datediff > 0) ? strtotimeNew('+'.$datediff.'days', strtotimeNew($slot->end_time)) : strtotimeNew($slot->end_time);
  $expired = ($x < $now) ? true :false;
  $orders = $slot->orders;
  $locked = ($slot->lock != ExtendedBevcoSlots::LOCK_NONE) ? true : false;
?>
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="tile <?= ($class)? $class : ''?>">
      <div class="tile_perimeter">
          <div class="tile-title">
              <h3>
              <?php if($locked) :?>
                  <i class="glyphicon glyphicon-lock"></i>  
              <?php endif ?>
              Slot Number : <?= $slot->slot_number ?></h3>
              <?php if ($locked): ?>
               <div class="ui-actions">
                <div class="dropdown">
                  <button class="btn btn-default btn-sm dropdown-toggle <?= ($locked) ? '' :'disabled'?>" type="button" id="dropdownMenuButton-<?= $slot->slot_number ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="glyphicon glyphicon-cog"></span>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-<?= $slot->slot_number ?>">
                        <?= Html::a('Unlock', ['#'], ['class' => 'dropdown-item slot-unlock','data' => ['slot-id' => $slot->id]]) ?> 
                  </div>
                </div>
              </div>
               <?php endif ?>
          </div>
          <div class="tile_content selection-panel <?= ($expired)? 'tile_disabled' : ''?>" data-row="<?= $slot->slot_number?>">
            <dl>
              <dt><strong>Time : <?= date("g:i a", strtotimeNew($slot->start_time));?> - <?= date("g:i a", strtotimeNew($slot->end_time));?></strong></dt>
            </dl>
            <dl>
              <dt>
                <strong> Active Bookings: <?= $slot->orderCount ?></strong>
              </dt>
            </dl>
            <dl>
              <dt>
                <strong> Remaining: <?= $re ?></strong>
              </dt>
            </dl>
            <?php if ($expired): ?>
              <dl>
                <dt>
                 <p class="slot-expired-text">Expired</p>
                </dt>
              </dl>
            <?php endif ?>
            <?php if ($pr <= 0): ?>
              <dl>
                <dt>
                 <p class="slot-expired-text">Sold Out</p>
                </dt>
              </dl>
            <?php endif ?>
          </div>
            <a
              class="more-info" 
              title="Slot : <?=$slot->slot_number?>" 
              data-toggle="popover"
              data-trigger="hover"
              data-placement="top"
              data-content="
                <?php if(!empty($orders)) {?>
                    <table class='table table-bordered'>
                      <tbody>
                        <tr>
                          <th>Member</th>
                          <th>Contact</th>
                        </tr>
                    <?php foreach ($orders as $j => $o) { ?>
                      <tr>
                        <td> <?=$o->memberFullName?></td>
                        <td> <?=$o->member->member_mobile1?></td>
                      </tr>
                    <?php } ?>
                      </tbody>
                    </table>
                <?php } else {?>
                  <p>No bookings yet</p>
                <?php } ?>
              "
              >
              <i class="glyphicon glyphicon-info-sign"></i>More info</a>
           </a>
        </div>
    </div>
</div>
<?php }?>
<?php } ?>
