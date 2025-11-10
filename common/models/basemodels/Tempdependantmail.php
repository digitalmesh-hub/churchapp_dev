<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "tempdependantmail".
 *
 * @property int $id
 * @property int $tempmemberid
 * @property int $dependantid
 * @property int $titleid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 * @property string $weddinganniversary
 * @property int $spousedependantid
 * @property int $ismarried
 * @property int $isapproved
 * @property string $tempimage
 * @property string $tempimagethumbnail
 * @property string $tempimageid
 *
 * @property Dependant $dependant
 * @property Member $tempmember
 * @property Title $title
 */
class Tempdependantmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tempdependantmail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tempmemberid'], 'required'],
            [['tempmemberid', 'dependantid', 'titleid', 'spousedependantid', 'ismarried'], 'integer'],
            [['dob', 'weddinganniversary'], 'safe'],
            [['dependantname'], 'string', 'max' => 50],
            [['relation', 'tempimageid'], 'string', 'max' => 45],
            [['isapproved'], 'string', 'max' => 4],
            [['tempimage', 'tempimagethumbnail'], 'string', 'max' => 500],
            [['dependantid'], 'exist', 'skipOnError' => true, 'targetClass' => Dependant::className(), 'targetAttribute' => ['dependantid' => 'id']],
            [['tempmemberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['tempmemberid' => 'memberid']],
            [['titleid'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['titleid' => 'TitleId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tempmemberid' => 'Tempmemberid',
            'dependantid' => 'Dependantid',
            'titleid' => 'Titleid',
            'dependantname' => 'Dependantname',
            'dob' => 'Dob',
            'relation' => 'Relation',
            'weddinganniversary' => 'Weddinganniversary',
            'spousedependantid' => 'Spousedependantid',
            'ismarried' => 'Ismarried',
            'isapproved' => 'Isapproved',
            'tempimage' => 'Tempimage',
            'tempimagethumbnail' => 'Tempimagethumbnail',
            'tempimageid' => 'Tempimageid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDependant()
    {
        return $this->hasOne(Dependant::className(), ['id' => 'dependantid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempmember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'tempmemberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitle()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'titleid']);
    }
}
