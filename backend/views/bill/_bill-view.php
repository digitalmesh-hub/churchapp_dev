<?php
use yii\helpers\Html; 
?>
<table id="tblBills" class="table table-bordered">
   <thead>
      <tr bgcolor="#80b3ff">
         <th class="text-center">Date</th>
         <th class="text-center">Particulars</th>
         <th class="text-center">Debit</th>
         <th class="text-center">Credit</th>
      </tr>
   </thead>
   <tbody id="billsbody">
      <?php if (!empty($provider)) {
         foreach ($provider as $memberNo => $bills) { 
               $totalDebit = 0;
               $totalCredit = 0;
            ?>
            <tr bgcolor="#c2d6d6">
               <td><span style="font-weight:bold">Membership no</span></td>
               <td colspan="6"><?= Html::encode($memberNo)?></td>
            </tr>
            <tr bgcolor="#e6e6e6">
               <td><span style="font-weight:bold">Name</span></td>
               <td colspan="6"><?= Html::encode($bills[0]['memberName'])?></td>
            </tr>
            <?php foreach ($bills as $bill) { 
                  $d1 = (float)$bill['debit'];
                  $c1 = (float)$bill['credit'];
                  $totalDebit += $d1;
                  $totalCredit += $c1;
               ?>
               <tr>
                  <td class="text-center"><?= Html::encode(date(yii::$app->params['dateFormat']['viewDateFormat'],strtotimeNew($bill['transactiondate']))) ?></td>
                  <td class="text-center"><?= Html::encode($bill['description']) ?></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($bill['debit'])) ?></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($bill['credit'])) ?></td>
               </tr>
            <?php }?>
            <tr bgcolor="#e6ffe6">
                  <td></td>
                  <td class="text-center"><span style="font-weight:bold">Total</span></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($totalDebit)) ?></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($totalCredit)) ?></td>
            </tr>
            <?php if($totalDebit > $totalCredit) { 
               $balance = $totalDebit - $totalCredit;
            ?>
               <tr bgcolor="#b3d9ff">
                  <td></td>
                  <td class="text-center"><span style="font-weight:bold">Closing Balance</span></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($balance)) ?></td>
                  <td></td>
               </tr>
            <?php } else { 
                $balance =  $totalCredit - $totalDebit;
            ?>
               <tr bgcolor=" #b3d9ff">
                  <td></td>
                  <td class="text-center"><span style="font-weight:bold">Closing Balance</span></td>
                  <td></td>
                  <td class="text-center"><?= Html::encode(yii::$app->MoneyFormat->decimalWithComma($balance)) ?></td>
               </tr>
            <?php } ?>
         <?php } } else { ?>
         <tr>
            <td colspan="7" class="text-center"><span style="color: red;">No Records Found</span> </td>
         </tr>
      <?php } ?>
   </tbody>
</table>

