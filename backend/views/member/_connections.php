<?php

use yii\helpers\Html;

/* @var $connections array */
/* @var $memberId int */
?>
<table class="table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Membership No.</th>
            <th>Mobile</th>
            <th>Connected Since</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($connections)) { ?>
            <tr>
                <td colspan="5">No connections added yet.</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($connections as $connection) { ?>
                <tr>
                    <td><?= Html::encode($connection['memberName']) ?></td>
                    <td><?= Html::encode($connection['membershipNumber']) ?></td>
                    <td><?= Html::encode($connection['mobile']) ?></td>
                    <td><?= Html::encode($connection['created_at']) ?></td>
                    <td>
                        <?= Html::button('Remove', [
                            'class' => 'btn btn-danger btn-xs remove-member-connection',
                            'data-member-id' => $memberId,
                            'data-connected-member-id' => $connection['memberId'],
                        ]) ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
