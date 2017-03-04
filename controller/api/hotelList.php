<?php
/**
 * Created by PhpStorm.
 * User: geek
 * Date: 2017/2/20
 * Time: 上午9:15
 */

header('content-type:text.html;charset=utf-8');
error_reporting(0);
require_once '../../model/PdoMySQL.class.php';
require_once '../../model/config.php';
require_once 'Response.php';


class HotelList
{
    private $tableName = "hotel";
    private $telephone = "";
    private $cityName= "";
    private $subjectId = "";
    private $page = 0;
    private $size = 0;
    private $type = "";



    protected static $_instance = null;

    protected function  __construct()
    {

    }

    protected function  __clone()
    {
        // TODO: Implement __clone() method.
    }


    public function  getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function getHotels()
    {
        self.$this->telephone = $_REQUEST["telephone"];
        self.$this->subjectId = $_REQUEST["subjectId"];
        self.$this->cityName = $_REQUEST["cityName"];
        self.$this->page = $_REQUEST["page"];
        self.$this->size = $_REQUEST["size"];
        self.$this->type = $_REQUEST["type"];

        $mysqlPdo = new PdoMySQL();

        if($this->telephone == ""){
            Response::show(201,"fail","非安全的数据请求","json");
        }
        $userRows = $mysqlPdo->find("user","telephone='$this->telephone'");
        if($userRows[0]["telephone"] != $this->telephone){
            Response::show(201,"fail","非安全的数据请求","json");
        }

        if(!empty($this->cityName) && !empty($this->subjectId)){
            //根据城市、酒店主题、分页字段查询主题酒店列表
            $city = str_replace("市","",$this->cityName);
            $allrows = $mysqlPdo->find($this->tableName,"subject='$this->subjectId' and address like '%$city%'","","","","",[(intval($this->page)-1)*intval($this->size),intval($this->page)*intval($this->size)]);
            Response::show(200,'主题酒店列表获取成功',$allrows,'json');
        }else if(!empty($this->cityName) && empty($this->subjectId) && !isset($this->type)){
            //根据查询城市、分页查询所有酒店列表
            $city = str_replace("市","",$this->cityName);
            $allrows = $mysqlPdo->find($this->tableName,"address like '%$city%'","","","","",[(intval($this->page)-1)*intval($this->size),intval($this->page)*intval($this->size)]);
            Response::show(200,'主题酒店列表获取成功',$allrows,'json');
        }else if(!empty($this->cityName) && !empty($this->type)){
            //查询城市、分页、酒店类型查询酒店列表
            $city = str_replace("市","",$this->cityName);
            $allrows = $mysqlPdo->find($this->tableName,"address like '%$city%' and kindType='$this->type'","","","","",[(intval($this->page)-1)*intval($this->size),intval($this->page)*intval($this->size)]);
            Response::show(200,'特色酒店列表获取成功',$allrows,'json');
        }else if(empty($this->cityName) && !empty($this->type)){
            //查询城市、分页、酒店类型查询酒店列表
            $city = str_replace("市","",$this->cityName);
            $allrows = $mysqlPdo->find($this->tableName,"kindType='$this->type'","","","","",[(intval($this->page)-1)*intval($this->size),intval($this->page)*intval($this->size)]);
            Response::show(200,'特色酒店列表获取成功',$allrows,'json');
        }else{
            //查询所有酒店列表
            $allrows = $mysqlPdo->find($this->tableName);
            Response::show(200,'4',$allrows,'json');
        }

    }
}

$lister = HotelList::getInstance();
$lister->getHotels();
?>