<html>

<head>

    <title>Re-member</title>

    <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />

</head>

<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="background-color: #fff;">

    <table cellspacing="0" border="0" style="background-color: #fff;font-family:Tahoma, Geneva, sans-serif; font-size:13px" cellpadding="0" width="100%">
        <tr>
            <td valign="top" style="margin:0 auto">
                <table cellspacing="0" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin:0 auto" cellpadding="0" width="600">
                    <tr>
                        <td width="188" style="text-align:left;"><img style="width:150px;" src="<?= !empty($logo) ? $logo : $message->embed(Yii::getAlias('@backend'.'/assets/theme/images/main_logo_mdpi.png'));?>" /></td>
                        <td width="281" style="text-align:center;"><strong>Profile Update Request</strong></td>
                        <td width="129" style="text-align:right;"><img src="<?= $institutionLogo ?>" width="120" height="114" />&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table cellspacing="0" border="0" cellpadding="0" width="100%" align="center">
                                <tr>
                                    <td valign="top" style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px" width="600" colspan="2">
                                        <br> Dear
                                        <?= $name ?>,
                                            <br> Greetings from
                                            <?= $institutionname ?>.
                                                <br> We thank you for sending us your updated membership information and are glad to let you know that some of the updates have been approved.
                                                <br/> Your updated membership information is given below for your reference.
                                                <br/>
                                                <br/> The data that was not approved is shown in <span style="color:#C00;"><strong>red</strong></span>. Please contact the Manager for more information.
                                                <br/>
                                                <br/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" style="padding:10px;">
                                        <!-- data table -->

                                        <table style="width:100%;border:1px solid #ddd; margin:0 auto;font-family:Arial, Helvetica, sans-serif; vertical-align:top;">
                                            <tr>
                                                <th colspan="2" style="border-bottom:1px solid #ddd;padding:5px; font-size:14px; background: #755f88; color: #FFF;">Member Details</th>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom:1px solid #ddd; vertical-align:top;">
                                                    <table style="width:100%;border:0px solid #ddd; font-size:13px; line-height: 18px;">
                                                        <tr>
                                                            <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Personal Details</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="width:100px; text-align:center;">
                                                                <img width="184" height="184" style="border:2px solid <?= $approvelMemberDetails['member_pic']['isApproved'] ?'#000;':'#C00' ?>;" src="<?=$approvelMemberDetails['memberImageThumbnail']['value'] ?>" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>First Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['firstName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['firstName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>Middle Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['middleName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['middleName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>Last Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['lastName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['lastName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Nickname/AKA</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['membernickname']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['membernickname']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Email </strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['member_email']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['member_email']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Date of Birth</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['member_dob']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=  $approvelMemberDetails['member_dob']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Occupation</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['occupation']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['occupation']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Home Church</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['homechurch']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['homechurch']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Blood Group</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['memberbloodgroup']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['memberbloodgroup']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Tag Cloud</strong> </td>
                                                            <td style="padding:5px;">Private</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Date of Marriage</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['dom']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['dom']['value'] ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="border-bottom:1px solid #ddd; vertical-align:top;">
                                                    <table style="width:100%;border-left:1px solid #ddd; font-size:13px; line-height: 18px;">
                                                        <tr>
                                                            <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Spouse Details</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="width:100px; text-align:center;">
                                                                <img width="184" height="184" style="border:2px solid <?= $approvelMemberDetails['spouse_pic']['isApproved'] ?'#000;':'#C00' ?>;" src="<?=$approvelMemberDetails['spouseImageThumbnail']['value'] ?>" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>First Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouse_firstName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouse_firstName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>Middle Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouse_middleName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouse_middleName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="40%" style="padding:5px; color:#757575;"><strong>Last Name</strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouse_lastName']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouse_lastName']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Nickname/AKA</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spousenickname']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spousenickname']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Email </strong></td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouse_email']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouse_email']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <?php $phoneApprove = $approvelMemberDetails['spouse_mobile1']['isApproved'] 
                                                    && $approvelMemberDetails['spouse_mobile1_countrycode']['isApproved']
                                        ?>
                                                                <td style="padding:5px; color:#757575;"><strong>Phone</strong> </td>
                                                                <td width="60%" style="padding:5px; color:<?= $phoneApprove?'#000;':'#C00' ?>">
                                                                    <?=$approvelMemberDetails['spouse_mobile1_countrycode']['value'] ." ".$approvelMemberDetails['spouse_mobile1']['value'] ?>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Date of Birth</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouse_dob']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouse_dob']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Occupation</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['spouseoccupation']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['spouseoccupation']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;"><strong>Blood Group</strong> </td>
                                                            <td width="60%" style="padding:5px; color:<?= $approvelMemberDetails['memberbloodgroup']['isApproved']?'#000;':'#C00' ?>">
                                                                <?=$approvelMemberDetails['memberbloodgroup']['value'] ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp;</td>
                                                        </tr>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr style="display: none;">
                                                <td style=" vertical-align:top;">
                                                    <table style="width:100%;border:0px solid #ddd; font-size:13px;  line-height: 18px;">
                                                        <tr>
                                                            <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Location Details</th>
                                                        </tr>
                                                        <tr>
                                                            <?php $locationApprove = $approvelMemberDetails['location']['isApproved'] ;
                                        ?>
                                                                <td style="padding:5px; color:#757575; vertical-align:top;"><strong>Location</strong></td>
                                                                <td style="padding:5px; color:<?= $locationApprove?'#000;':'#C00' ?>">
                                                                  <?= $approvelMemberDetails['location']['value']?>
                                                                        <br>
                                                                 </td>
                                                        </tr></table> </td></tr>
                                                                 
                                            <tr>
                                                <td style=" vertical-align:top;">
                                                    <table style="width:100%;border:0px solid #ddd; font-size:13px;  line-height: 18px;">
                                                        <tr>
                                                            <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Residence Details</th>
                                                        </tr>
                                                        <tr>
                                                            <?php $addressApprove = $approvelMemberDetails['residence_address1']['isApproved'] 
                                                    && $approvelMemberDetails['residence_address2']['isApproved'] 
                                                    && $approvelMemberDetails['residence_district']['isApproved']
                                && $approvelMemberDetails['residence_state']['isApproved']
                                && $approvelMemberDetails['residence_pincode']['isApproved']
                                        ?>
                                                                <td style="padding:5px; color:#757575; vertical-align:top;"><strong>Residence Address</strong></td>
                                                                <td style="padding:5px; color:<?= $addressApprove?'#000;':'#C00' ?>">
                                                                    <?= $approvelMemberDetails['residence_address1']['value']?>
                                                                        <br>
                                                                        <?= $approvelMemberDetails['residence_address2']['value']?>
                                                                            <br>
                                                                            <?= $approvelMemberDetails['residence_district']['value']?>
                                                                                <br>
                                                                                <?= $approvelMemberDetails['residence_state']['value']?>
                                                                                    <br>
                                                                                    <?= $approvelMemberDetails['residence_pincode']['value']?>
                                                                                        <br>

                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <?php $phoneApprove = $approvelMemberDetails['member_residence_Phone1_countrycode']['isApproved'] 
                                                    && $approvelMemberDetails['member_residence_phone1_areacode']['isApproved'] 
                                                    && $approvelMemberDetails['member_residence_Phone1']['isApproved']
                                        ?>
                                                                <td style="padding:5px; color:#757575; vertical-align:top;"><strong>Residence Phone</strong></td>
                                                                <td style="padding:5px; color:<?= $phoneApprove?'#000;':'#C00' ?>">
                                                                    <?= $approvelMemberDetails['member_residence_Phone1_countrycode']['value']."  ". $approvelMemberDetails['member_residence_phone1_areacode']['value']." ".$approvelMemberDetails['member_residence_Phone1']['value']?>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp; </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp; </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp; </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style=" vertical-align:top;">
                                                    <table style="width:100%;border-left:1px solid #ddd; font-size:13px; line-height: 18px;">
                                                        <tr>
                                                            <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Business Details</th>
                                                        </tr>
                                                        <tr>
                                                            <?php $addressApprove = $approvelMemberDetails['business_address1']['isApproved'] 
                                                    && $approvelMemberDetails['business_address2']['isApproved'] 
                                                    && $approvelMemberDetails['business_district']['isApproved']
                                && $approvelMemberDetails['business_state']['isApproved']
                                && $approvelMemberDetails['business_pincode']['isApproved']
                                        ?>
                                                                <td width="40%" style="padding:5px; color:#757575;  vertical-align:top;"><strong>Business Address</strong></td>
                                                                <td width="60%" style="padding:5px; color:#000;  vertical-align:top;">
                                                                    <?= $approvelMemberDetails['business_address1']['value']?>
                                                                        <br>
                                                                        <?= $approvelMemberDetails['business_address2']['value']?>
                                                                            <br>
                                                                            <?= $approvelMemberDetails['business_district']['value']?>
                                                                                <br>
                                                                                <?= $approvelMemberDetails['business_state']['value']?>
                                                                                    <br>
                                                                                    <?= $approvelMemberDetails['business_pincode']['value']?>
                                                                                        <br>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <?php $phoneApprove = $approvelMemberDetails['member_business_phone1_countrycode']['isApproved'] 
                                                    && $approvelMemberDetails['member_business_phone1_areacode']['isApproved'] 
                                                    && $approvelMemberDetails['member_musiness_Phone1']['isApproved']
                                        ?>
                                                                <td style="padding:5px; color:#757575;"><strong>Phone 1</strong> </td>
                                                                <td style="padding:5px; color:<?= $phoneApprove?'#000;':'#C00' ?>">
                                                                    <?= $approvelMemberDetails['member_business_phone1_countrycode']['value']."  ". $approvelMemberDetails['member_business_phone1_areacode']['value']." ".$approvelMemberDetails['member_musiness_Phone1']['value']?>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <?php $phoneApprove = $approvelMemberDetails['member_business_phone3_countrycode']['isApproved'] 
                                                    && $approvelMemberDetails['member_business_phone3_areacode']['isApproved'] 
                                                    && $approvelMemberDetails['member_business_Phone3']['isApproved']
                                        ?>
                                                                <td style="padding:5px; color:#757575;"><strong>Phone 2</strong> </td>
                                                                <td style="padding:5px; color:<?= $phoneApprove?'#000;':'#C00' ?>">
                                                                    <?= $approvelMemberDetails['member_business_phone3_countrycode']['value']."  ". $approvelMemberDetails['member_business_phone3_areacode']['value']." ".$approvelMemberDetails['member_business_Phone3']['value']?>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp; </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:5px; color:#757575;">&nbsp;</td>
                                                            <td style="padding:5px;">&nbsp; </td>
                                                        </tr>

                                                    </table>
                                                </td>
                                            </tr>
                                            <?php if (count($approvelDepentandList) >=1 ){?>
                                                <tr>

                                                    <td style=" vertical-align:top;" colspan="2">

                                                        <table style="width:100%;border-top:1px solid #ddd; font-size:13px; line-height: 18px;">
                                                            <tr>
                                                                <th colspan="2" style="border-bottom:3px solid #ddd;padding:5px; font-size:14px; color: #656565;">Dependent Details </th>
                                                            </tr>

                                                            <?php foreach ($approvelDepentandList as $dependant){
                                                 $dependantId = isset($dependant['DependantId']) ? $dependant['DependantId']:'';
                                                 if ($dependantId){ ?>
                                                                <!-- Row -->
                                                                <tr>
                                                                    <td width="50%" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;">
                                                                        <!-- Dependant -->
                                                                        <table style="width:100%;border:0px solid #ddd; font-size:13px;  line-height: 18px;">
                                                                            <tr>
                                                                                <?php 
                                                                                 $dImage = $approvelDependantDetails['dependantPic_'.$dependantId]['value'];
                                                                                 if($dImage) {
                                                                                    $dImage =  Yii::$app->params['imagePath'].$dImage;
                                                                                } else {
                                                                                    $dImage = Yii::$app->params['imagePath'].'/Member/default-user.png';
                                                                                }
                                                                                ?>
                                                                                    <td colspan="2" style="width:100px; text-align:center;"><img width="184" height="184" src="<?=$dImage?>" /></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding:5px;" width="40%">Dependent Title </td>
                                                                                <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['dependanttitle_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                    <?=$approvelDependantDetails['dependanttitle_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding:5px;">Name</td>
                                                                                <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['dependantname_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                    <?=$approvelDependantDetails['dependantname_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <?php $phoneApprove = $approvelDependantDetails['dependantmobile_'.$dependantId]['isApproved']
                                                                                        && $approvelDependantDetails['dependantmobilecountrycode_'.$dependantId]['isApproved']
                                                                                    ?>
                                                                                <td style="padding:5px;">Mobile</td>
                                                                                <td width="60%" style="padding:5px; color:<?= $phoneApprove ? '#000;' : '#C00' ?>">
                                                                                    <?=$approvelDependantDetails['dependantmobilecountrycode_'.$dependantId]['value'].' '.$approvelDependantDetails['dependantmobile_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding:5px;">Date of Birth</td>
                                                                                <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['dob_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                    <?=date('d-F-Y',strtotimeNew($approvelDependantDetails['dob_'.$dependantId]['value'])) ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding:5px;">Relation</td>
                                                                                <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['relation_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                    <?=$approvelDependantDetails['relation_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding:5px;">Marital Status</td>
                                                                                <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['ismarried_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                    <?=$approvelDependantDetails['ismarried_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <!-- /.Dependant -->
                                                                    </td>

                                                                    <td width="50%" style="border-bottom:1px solid #ddd;">
                                                                        <!-- Dependant -->
                                                                        <?php  if (isset($approvelDependantDetails['spousetitle_'.$dependantId]) &&!empty($approvelDependantDetails['spousetitle_'.$dependantId])){
                                                                            $spouseDetailsAvailable = true;
                                                                        } else {
                                                                            $spouseDetailsAvailable = false;
                                                                        }
                                                                        ?>
                                                                            <table style="width:100%;border:0px solid #ddd; font-size:13px;  line-height: 18px;">
                                                                                <tr>
                                                                                    <?php 
                                                                                 $sImage = isset($approvelDependantDetails['dependantSpousePic_'.$dependantId]['value']) ? $approvelDependantDetails['dependantSpousePic_'.$dependantId]['value'] : null;
                                                                                 if($sImage) {
                                                                                    $sImage =  Yii::$app->params['imagePath'].$sImage;
                                                                                } else {
                                                                                    $sImage = Yii::$app->params['imagePath'].'/Member/default-user.png';
                                                                                }
                                                                                ?>
                                                                                        <td colspan="2" style="width:100px; text-align:center;"><img width="184" height="184" src="<?=$sImage?>" /></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding:5px;" width="40%">Spouse Title </td>
                                                                                    <?php if ($spouseDetailsAvailable) {?>
                                                                                        <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['spousetitle_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                            <?=$approvelDependantDetails['spousetitle_'.$dependantId]['value'] ?>
                                                                                        </td>
                                                                                        <?php }else{?>
                                                                                            <td style="padding:5px;">&nbsp; </td>
                                                                                            <?php }?>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding:5px;">Spouse Name</td>
                                                                                    <?php if ($spouseDetailsAvailable) {?>
                                                                                        <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['spousename_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                            <?=$approvelDependantDetails['spousename_'.$dependantId]['value'] ?>
                                                                                        </td>
                                                                                        <?php }else{?>
                                                                                            <td style="padding:5px;">&nbsp; </td>
                                                                                            <?php }?>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding:5px;">Spouse Mobile</td>
                                                                                    <?php if ($spouseDetailsAvailable) {?>

                                                                                <?php $phoneApprove = $approvelDependantDetails['dependantspousemobile_'.$dependantId]['isApproved']
                                                                                        && $approvelDependantDetails['dependantspousemobilecountrycode_'.$dependantId]['isApproved']
                                                                                    ?>
                                                                                <td width="60%" style="padding:5px; color:<?= $phoneApprove ? '#000;' : '#C00' ?>">
                                                                                    <?=$approvelDependantDetails['dependantspousemobilecountrycode_'.$dependantId]['value'].' '.$approvelDependantDetails['dependantspousemobile_'.$dependantId]['value'] ?>
                                                                                </td>
                                                                                        <?php }else{?>
                                                                                            <td style="padding:5px;">&nbsp; </td>
                                                                                            <?php }?>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding:5px;">Spouse DOB</td>
                                                                                    <?php if ($spouseDetailsAvailable) {?>
                                                                                        <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['spousedob_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                            <?= date('d-F-Y',strtotimeNew($approvelDependantDetails['spousedob_'.$dependantId]['value'])) ?>
                                                                                        </td>
                                                                                        <?php }else{?>
                                                                                            <td style="padding:5px;">&nbsp; </td>
                                                                                            <?php }?>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="padding:5px;">Wedding Anniversary</td>
                                                                                    <?php if ($spouseDetailsAvailable) {?>
                                                                                        <td width="60%" style="padding:5px; color:<?= $approvelDependantDetails['weddinganniversary_'.$dependantId]['isApproved']?'#000;':'#C00' ?>">
                                                                                            <?=date('d-F-Y',strtotimeNew($approvelDependantDetails['weddinganniversary_'.$dependantId]['value'])) ?>
                                                                                        </td>
                                                                                        <?php }else{?>
                                                                                            <td style="padding:5px;">&nbsp; </td>
                                                                                            <?php }?>
                                                                                </tr>

                                                                            </table>
                                                                            <!-- /.Dependant -->
                                                                    </td>

                                                                </tr>
                                                                <!-- /.Row -->

                                                                <?php } }

                                                ?>

                                                        </table>

                                                    </td>

                                                </tr>
                                                <?php }?>
                                        </table>

                                        <!-- /.data table -->
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" style="padding:10px; font-size:12px; line-height:25px;">
                                        <span style="color:#0165AF;">Thanking you</span>
                                        <br/>
                                        <strong style="color:#0165AF;"><?= $institutionname ?></strong>
                                        <br/>
                                        <!--   <span style="color:#666;">Member ID : 1234</span><br/>-->
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>

                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 11px;  color:#808080; line-height:13px" width="600" colspan="2">
                                        The contents of this email and any files transmitted with it are confidential and intended only for the individuals or entities to which they are addressed. It may not be disclosed to, or used by, anyone other than the addressee, nor may it be copied in any way. If you have received this email in error, please notify the sender by return email and then delete the email and any files transmitted with it from your system. Please note that while reasonable effort has been made to ensure this message is free of viruses, opening and using this message is at the risk of the recipient and Re-Member does not accept responsibility for any loss arising from unauthorised access to, or interference with, any internet communications by any third party, or from the transmission of any viruses.
                                        <br/>
                                        <br/>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
