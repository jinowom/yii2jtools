<?php
namespace jinowom\yii2jtools\components\jd_area;


use GuzzleHttp\Client;
use jinowom\yii2jtools\tools\Model;
use yii\base\Component;
use yii\base\StaticInstanceInterface;
use yii\base\StaticInstanceTrait;
use yii\db\ActiveRecord;
use yii\db\Exception;

class JdArea extends Component implements StaticInstanceInterface
{
    use StaticInstanceTrait;

    /**
     * @param int $fid
     * @return array
     */
    public function getArrByFid($fid = 0)
    {
        $client = new Client();
        $resp = $client->request("GET", "http://d.jd.com/area/get", [
            'query' => [
                'fid' => $fid,
            ]
        ]);
        $o = json_decode($resp->getBody()->getContents(), true);
        return $o;
    }

    /**
     * @param string $class_name model必须含有id,fid,name
     * @param int $fid
     * @throws Exception
     */
    public function updateLocalDbModel($class_name, $fid = 0, $vp = 0)
    {
        /**
         * @var ActiveRecord $model
         */
        $model = new $class_name;
        $data = self::getArrByFid($fid);
        if (count($data)>0){
            foreach ($data as $k => $v){
                $m = $model::findOne(['fid' => $fid, 'id' => $v['id']]);
                if (!$m){
                    $m = clone $model;
                    $m->fid = $fid;
                    $m->id = $v['id'];
                    $m->name = $v['name'];
                    if (!$m->save()){
                        throw new Exception(Model::getModelError($m));
                    }
                }else{
                    if ($m->name != $v['name']){
                        $m->name = $v['name'];
                        if (!$m->save()){
                            throw new Exception(Model::getModelError($m));
                        }
                    }
                }
                if ($vp){
                    var_dump($m->toArray());
                }
                self::updateLocalDbModel($class_name, $v['id'], $vp);
            }
        }
    }
}