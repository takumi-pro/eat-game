<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');
session_start();
$objs = array();

class Obj{
    protected $name;
    protected $danger;
    protected $fullness;
    protected $img;
    public function __construct($name,$danger,$fullness,$img){
        $this->name = $name;
        $this->danger = $danger;
        $this->fullness = $fullness;
        $this->img = $img;
    }
    public function getName(){
        return $this->name;
    }
    public function getDanger(){
        return $this->danger;
    }
    public function getFullness(){
        return $this->fullness;
    }
    public function getImg(){
        return $this->img;
    }
    public function poison(){
        if(probability($this->danger) == true){
            $_SESSION['gameover'] = true;
        }
    }
}
class Human{
    protected $fullness;
    protected $fitness;
    
    public function __construct($fullness){
        $this->fullness = $fullness;
        //$this->fitness = $fitness;
    }
    public function getFullness(){
        return $this->fullness;
    }
    public function getImg(){
        return $this->img;
    }
    public function getFitness(){
        return $this->fitness;
    }
    public function setFullness($str){
        $this->fullness = $str;
    }
    public function eat($targetobj){
        $this->setFullness($this->getFullness() + $targetobj->getFullness());
    }
}

//インスタンス生成
$objs[] = new Obj('牛丼',2,35,'food1.png');
$objs[] = new Obj('カレー',20,30,'food2.png');
$objs[] = new Obj('寿司',35,20,'food3.png');
$objs[] = new Obj('クレヨン',67,10,'food4.png');
$human = new Human(50,100);

function createObj(){
    global $objs;
    $obj = $objs[mt_rand(0,3)];
    $_SESSION['obj'] = $obj;
}
function createHuman(){
    global $human;
    $_SESSION['human'] = $human;
}
function init(){
    $_SESSION['eat_count'] = 0;
    createObj();
    createHuman();
}
function gameOver(){
    $_SESSION['gameover'] = true;
}
function hangry(){
    $_SESSION['human']->setFullness($_SESSION['human']->getFullness()-10);
}
function probability($num){
    if(rand(1,100) < (int)$num){
        $result = true;
    }else{
        $result = false;
    }
    return $result;
}

//POST送信があった場合
if(!empty($_POST)){
    $eatFlg = (!empty($_POST['eat'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $nothankFlg = (!empty($_POST['no_thank'])) ? true : false;
    //ゲーム開始ボタン
    if($startFlg){
        init();
    }else{
        //食べるボタン
        if($eatFlg){
            $_SESSION['human']->eat($_SESSION['obj']);
            createObj();
            $_SESSION['eat_count'] += 1;

            $_SESSION['obj']->poison();
            //満腹度が0以下になった場合
            if($_SESSION['human']->getFullness() == 0){
                gameOver();
            }
            
            //やめとくボタン
        }elseif($nothankFlg){
            //満腹度が0以下になった場合
            if($_SESSION['human']->getFullness() == 0){
                gameOver();
            }
            createObj();
            hangry();
        }else{
        $_SESSION = array();
        }
    }
    $_POST = array();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div class="wrap">
        <div class="inner">
        <?php if(empty($_SESSION)){ ?>
            <p>食べる？食べない？</p>
            <form action="" method="post">
                
                
                <input type="submit" name="start" value="ゲーム開始">
            </form>
        <?php }elseif(!empty($_SESSION['gameover'])){ ?>
            <p>ゲームオーバー</p>
            <form action="" method="post">
                <input type="submit" name="again" value="やり直す">
            </form>
        <?php }else{ ?>
            <p><?php echo $_SESSION['obj']->getName(); ?></p>
            <div class="img-wrap"><img style="width:100%;height:100%;" src="<?php echo 'img/'.$_SESSION['obj']->getImg(); ?>" alt=""></div>
            
            <div style="margin-top:16px;" class="flex">
                <div><span class="deg">危険度</span><span class="par"><?php echo $_SESSION['obj']->getDanger(); ?>%</span></div>
                <div><span class="deg">満腹度</span><span class="par"><?php echo $_SESSION['obj']->getFullness(); ?>%</span></div>
            </div>
            <form class="flex" action="" method="post">
                <input class="left" type="submit" name="eat" value="食べる">
                <input cla="right" type="submit" name="no_thank" value="やめとく">
            </form>

            <div class="fullness"><span>お腹:</span><span><?php echo $_SESSION['human']->getFullness(); ?></span></div>
            <div class="count"><span>食べ物カウント:</span><span><?php echo $_SESSION['eat_count']; ?></span></div>
            
        <?php } ?>
        </div>
    </div>
</body>
</html>