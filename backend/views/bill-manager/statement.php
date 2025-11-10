<!-- Modal -->

      
        
        <p>
        	<table cellpadding="0" cellspacing="0" class="table table-bordered">
            	<tr>
                	<td width="30%"><strong>Member Name</strong></td>
                    <td width="70%"><?php echo $queryResult[0]['firstName'].' '.$queryResult[0]['middleName'].' '.$queryResult[0]['lastName'];?></td>
                </tr>
                <tr>
                	<td><strong>Membership No.</strong></td>
                    <td><?php echo $queryResult[0]['memberno'];?></td>
                </tr>
            </table>
        </p>
       
        <div class="transactionstab">
        	<table cellpadding="0" cellspacing="0" class="table table-bordered accountingtab table-striped">
            	<tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Type</th>
                </tr>
                 <?php 
                 $i =0;
       			 foreach ($queryResult as $row){?>
                <tr>
                    <td><?php $trdDate = $row['transactiondate']; 
                    $date = str_replace('.', '-', $trdDate);
                    echo date('d-m-Y', strtotimeNew($date));?></td>
                    <td><?php echo $row['description'];?></td>
                    <td><?php echo !empty($row['debit']) ? yii::$app->MoneyFormat->decimalWithComma($row['debit']) : '';?></td>
                    <td><?php echo !empty($row['credit']) ? yii::$app->MoneyFormat->decimalWithComma($row['credit']) : '';?></td>
                    <?php if ($row['paymentType'] == "card" ){?>
                    <td><a title="View Details" role="button" data-toggle="collapse" href="#collapseType<?php echo $i;?>" aria-expanded="false" aria-controls="collapseType<?php echo $i;?>">Card</a></td>
                    </tr>
                    <tr class="collapse" id="collapseType<?php echo $i;?>">
                	<td colspan="6">
                    	<table cellpadding="0" cellspacing="0" class="table typetable Mbot0">
                        	<tr>
                            	<th>Card No.</th>
                                <th>Card Issuer</th>
                                
                            </tr>
                            <tr>
                            	<td><?php echo $row['ChequeNo']?></td>
                                <td><?php echo $row['Bank']?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                    <?php }
                    //if the payment type is cheque
                    elseif ($row['paymentType'] == "cheque"){?>
                    <td><a title="View Details" role="button" data-toggle="collapse" href="#collapseType<?php echo $i;?>" aria-expanded="false" aria-controls="collapseType<?php echo $i;?>">Cheque</a></td>
                    </tr>
                    <tr class="collapse" id="collapseType<?php echo $i;?>">
                	<td colspan="6">
                    	<table cellpadding="0" cellspacing="0" class="table typetable Mbot0">
                        	<tr>
                            	<th>Cheque no.</th>
                                <th>Date</th>
                                <th>Branch</th>             
                                <th>Bank</th>
                                
                            </tr>
                            <tr>
                            	<td><?php echo $row['ChequeNo']?></td>
                                <td><?php echo date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($row['Date']));?></td>
                                <td><?php echo $row['Branch']?></td>
                                <td><?php echo $row['Bank']?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php }
                //if the payment type is neft
        elseif ($row['paymentType'] == "neft"){
           ?>
           <td><a title="View Details" role="button" data-toggle="collapse" href="#collapseType<?php echo $i;?>" aria-expanded="false" aria-controls="collapseType<?php echo $i;?>">Neft</a></td>
                    </tr>
                    <tr class="collapse" id="collapseType<?php echo $i;?>">
                	<td colspan="6">
                    	<table cellpadding="0" cellspacing="0" class="table typetable Mbot0">
                        	<tr>
                            	<th>Neft no.</th>
                                <th>Date</th>
                                <th>Branch</th>             
                                <th>Bank</th>
                                
                            </tr>
                            <tr>
                            	<td><?php echo $row['NeftNo']?></td>
                                <td><?php echo date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($row['Date']));?></td>
                                <td><?php echo $row['Branch']?></td>
                                <td><?php echo $row['Bank']?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php }
                 //if the payment type is upi
        elseif ($row['paymentType'] == "upi"){
            ?>
            <td><a title="View Details" role="button" data-toggle="collapse" href="#collapseType<?php echo $i;?>" aria-expanded="true" aria-controls="collapseType<?php echo $i;?>">UPI</a></td>
                     </tr>
                 <?php }
                else {?>
                <td>Cash</td>
                
                <?php }
                $i++;
        }?>
            </table>
           
        </div>
      
      


