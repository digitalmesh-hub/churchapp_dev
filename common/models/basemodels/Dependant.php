<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "dependant".
 *
 * @property int $id
 * @property int $memberid
 * @property int $titleid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 * @property int $dependantid
 * @property string $weddinganniversary
 * @property int $ismarried
 * @property string $image
 * @property string $thumbnailimage
 * @property string $dependantmobilecountrycode
 * @property string $dependantmobile

 * @property Title $title
 * @property Member $member
 * @property Tempdependant[] $tempdependants
 * @property Tempdependantmail[] $tempdependantmails
 */
class Dependant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dependant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberid'], 'required'],
            [['memberid', 'titleid', 'dependantid', 'ismarried'], 'integer'],
            [['dob', 'weddinganniversary'], 'safe'],
            [['dependantname'], 'string', 'max' => 50],
            [['relation'], 'string', 'max' => 45],
        	[['tempmemberid'], 'string', 'max' => 15],
            [['tempdependantId'], 'string', 'max' => 75],
            [['dependantmobilecountrycode'], 'string', 'max' => 4],
            [['dependantmobile'], 'string', 'max' => 13],
            [['image', 'thumbnailimage'], 'string', 'max' => 500],
            [['titleid'], 'exist', 'skipOnError' => true, 'targetClass' => Title::className(), 'targetAttribute' => ['titleid' => 'TitleId']],
            [['memberid'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['memberid' => 'memberid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberid' => 'Memberid',
            'titleid' => 'Titleid',
            'dependantname' => 'Dependantname',
            'dob' => 'Dob',
            'relation' => 'Relation',
            'dependantid' => 'Dependantid',
            'weddinganniversary' => 'Weddinganniversary',
            'ismarried' => 'Ismarried',
            'image' => 'Image',
            'thumbnailimage' => 'Thumbnailimage',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitle()
    {
        return $this->hasOne(Title::className(), ['TitleId' => 'titleid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['memberid' => 'memberid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempdependants()
    {
        return $this->hasMany(Tempdependant::className(), ['dependantid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTempdependantmails()
    {
        return $this->hasMany(Tempdependantmail::className(), ['dependantid' => 'id']);
    }
}
