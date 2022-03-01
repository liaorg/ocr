<?php

class Distinguish
{
    private $ImagePath = '';
    private $ImageSize = '';
    private $ImageInfo = '';
    
    public function __construct($Image){
        /*
         *取得图片路径和图片尺寸
         */
        $this->ImagePath = $Image;
        $this->ImageSize = getimagesize($Image);
    }

    /*
     *获取图像标识符，保存到ImageInfo，只能处理bmp,png,jpg图片
     *ImageCreateFromBmp是我自己定义的函数，最后会给出
     */
    function getInfo(){
        $filetype = substr($this->ImagePath,-3);
        if($filetype == 'bmp'){
            $this->ImageInfo = $this->ImageCreateFromBmp($this->ImagePath);
        }elseif($filetype == 'jpg'){
            $this->ImageInfo = imagecreatefromjpeg($this->ImagePath);
        }elseif($filetype == 'png'){
            $this->ImageInfo = imagecreatefrompng($this->ImagePath);
        }
    }

    /*获取图片RGB信息*/
    function getRgb(){
        $rgbArray = array();
        $res = $this->ImageInfo;
        $size = $this->ImageSize;
        $wid = $size['0'];
        $hid = $size['1'];
        for($i=0; $i < $hid; ++$i){
            for($j=0; $j < $wid; ++$j){
                $rgb = imagecolorat($res,$j,$i);
                $rgbArray[$i][$j] = imagecolorsforindex($res, $rgb);
            }
        }
        return $rgbArray;
    }
    /*
     *获取灰度信息
     */
    function getGray(){
        $grayArray = array();
        $size = $this->ImageSize;
        $rgbarray = $this->getRgb();
        $wid = $size['0'];
        $hid = $size['1'];
        for($i=0; $i < $hid; ++$i){
            for($j=0; $j < $wid; ++$j){
                $grayArray[$i][$j] = (299*$rgbarray[$i][$j]['red']+587*$rgbarray[$i][$j]['green']+144*$rgbarray[$i][$j]['blue'])/1000;
            }
        }
        return $grayArray;
    }
    
    /*根据灰度信息打印图片*/
    function printByGray(){
        $size = $this->ImageSize;
        $grayArray = $this->getGray();
        $wid = $size['0'];
        $hid = $size['1'];
        for($k=0;$k<25;$k++){
            echo $k."\n";
            for($i=0; $i < $hid; ++$i){
                for($j=0; $j < $wid; ++$j){
                    if($grayArray[$i][$j] < $k*10){
                        echo '■';
                    }else{
                        echo '□';
                    }
                }
                echo "|\n";
            }
            echo "---------------------------------------------------------------------------------------------------------------\n";
        }

    }
    
    /*
     *根据自定义的规则，获取二值化二维数组
     *@return  图片高*宽的二值数组（0,1）
     */
    function getErzhi(){
        $erzhiArray = array();
        $size = $this->ImageSize;
        $grayArray = $this->getGray();
        $wid = $size['0'];
        $hid = $size['1'];
        for($i=0; $i < $hid; ++$i){
            for($j=0; $j <$wid; ++$j){
                if( $grayArray[$i][$j]    < 90 ){
                    $erzhiArray[$i][$j]=1;
                }else{
                    $erzhiArray[$i][$j]=0;
                }
            }
        }
        return $erzhiArray;
    }
    
    /*
     *二值化图片降噪
     *@param $erzhiArray二值化数组
     */
    function reduceZao($erzhiArray){
        $data = $erzhiArray;
        $gao = count($erzhiArray);
        $chang = count($erzhiArray['0']);

        $jiangzaoErzhiArray = array();

        for($i=0;$i<$gao;$i++){
            for($j=0;$j<$chang;$j++){
                $num = 0;
                if($data[$i][$j] == 1)
                {
                    // 上
                    if(isset($data[$i-1][$j])){
                        $num = $num + $data[$i-1][$j];
                    }
                    // 下
                    if(isset($data[$i+1][$j])){
                        $num = $num + $data[$i+1][$j];
                    }
                    // 左
                    if(isset($data[$i][$j-1])){
                        $num = $num + $data[$i][$j-1];
                    }
                    // 右
                    if(isset($data[$i][$j+1])){
                        $num = $num + $data[$i][$j+1];
                    }
                    // 上左
                    if(isset($data[$i-1][$j-1])){
                        $num = $num + $data[$i-1][$j-1];
                    }
                    // 上右
                    if(isset($data[$i-1][$j+1])){
                        $num = $num + $data[$i-1][$j+1];
                    }
                    // 下左
                    if(isset($data[$i+1][$j-1])){
                        $num = $num + $data[$i+1][$j-1];
                    }
                    // 下右
                    if(isset($data[$i+1][$j+1])){
                        $num = $num + $data[$i+1][$j+1];
                    }
                }

                    if($num < 1){
                        $jiangzaoErzhiArray[$i][$j] = 0;
                    }else{
                        $jiangzaoErzhiArray[$i][$j] = 1;
                    }
            }
        }
        return $jiangzaoErzhiArray;

    }
    /*
     *归一化处理,针对一个个的数字,即去除字符周围的白点
     *@param $singleArray 二值化数组
     */
    function getJinsuo($singleArray){
        $dianCount = 0;
        $rearr = array();

        $gao = count($singleArray);
        $kuan = count($singleArray['0']);

        $dianCount = 0;
        $shangKuang = 0;
        $xiaKuang = 0;
        $zuoKuang = 0;
        $youKuang = 0;
        //从上到下扫描
        for($i=0; $i < $gao; ++$i){
            for($j=0; $j < $kuan; ++$j){
                if( $singleArray[$i][$j] == 1){
                    $dianCount++;
                }
            }
            if($dianCount>1){
                $shangKuang = $i;
                $dianCount = 0;
                break;
            }
        }
        //从下到上扫描
        for($i=$gao-1; $i > -1; $i--){
            for($j=0; $j < $kuan; ++$j){
                if( $singleArray[$i][$j] == 1){
                    $dianCount++;
                }
            }
            if($dianCount>1){
                $xiaKuang = $i;
                $dianCount = 0;
                break;
            }
        }
        //从左到右扫描
        for($i=0; $i < $kuan; ++$i){
            for($j=0; $j < $gao; ++$j){
                if( $singleArray[$j][$i] == 1){
                    $dianCount++;
                }
            }
            if($dianCount>1){
                $zuoKuang = $i;
                $dianCount = 0;
                break;
            }
        }
        //从右到左扫描
        for($i=$kuan-1; $i > -1; --$i){
            for($j=0; $j < $gao; ++$j){
                if( $singleArray[$j][$i] == 1){
                    $dianCount++;
                }
            }
            if($dianCount>1){
                $youKuang = $i;
                $dianCount = 0;
                break;
            }
        }
        for($i=0;$i<$xiaKuang-$shangKuang+1;$i++){
            for($j=0;$j<$youKuang-$zuoKuang+1;$j++){
                $rearr[$i][$j] = $singleArray[$shangKuang+$i][$zuoKuang+$j];
            }
        }
        return $rearr;
    }
    /*
     *切割成三维数组，每个小数字在一个数组里面
     *只适用四个数字一起的数组
     *@param 经过归一化处理的二值化数组
     */
    function cutSmall($erzhiArray){
        $doubleArray = array();
        $jieZouyou = array();

        $gao = count($erzhiArray);
        $kuan = count($erzhiArray['0']);

        $jie = 0;
        $s = 0;
        $jieZouyou[$s] = 0;
        $s++;
        //从左到右扫描

        for($i=0; $i < $kuan;){
            for($j=0; $j < $gao; ++$j){
                $jie = $jie + $erzhiArray[$j][$i];
            }
            //如果有一列全部是白，设置$jieZouyou,并且跳过中间空白部分
            if($jie == 0){
                $jieZouyou[$s] = $i+1;
                do{
                    $n = ++$i;
                    $qian = 0;
                    $hou = 0;
                    for($m=0; $m < $gao; ++$m){
                        $qian = $qian + $erzhiArray[$m][$n];
                        $hou = $hou + $erzhiArray[$m][$n+1];
                    }
                    $jieZouyou[$s+1] = $n+1;
                }
                //当有两列同时全部为白，说明有间隙，循环，知道间隙没有了
                while($qian == 0 && $hou == 0);
                $s+=2;
                $i++;
            }else{
                $i++;
            }

            $jie = 0;
        }
        $jieZouyou[] = $kuan;
        //极端节点数量，(应该是字符个数)*2
        $jieZouyouCount = count($jieZouyou);

        for($k=0;$k<$jieZouyouCount/2;$k++){
            for($i=0; $i < $gao; $i++){
                for($j=0; $j < $jieZouyou[$k*2+1]-$jieZouyou[$k*2]-1; ++$j){
                    $doubleArray[$k][$i][$j] = $erzhiArray[$i][$j+$jieZouyou[$k*2]];
                }
            }

        }
        return $doubleArray;
    }
    /*
     *定义求线性回归A和B的函数
     *@param $zuobiaoArray坐标的三维数组
     */
    function getHuigui($zuobiaoArray){
        $y8 = 0;
        $x8 = 0;
        $x2 = 0;
        $xy = 0;
        $geshu = count($zuobiaoArray);
        for($i=0;$i<$geshu;$i++){
            $y8 = $y8+$zuobiaoArray[$i]['y'];
            $x8 = $x8+$zuobiaoArray[$i]['x'];
            $xy = $xy+$zuobiaoArray[$i]['y']*$zuobiaoArray[$i]['x'];
            $x2 = $x2 + $zuobiaoArray[$i]['x']*$zuobiaoArray[$i]['x'];;
        }
        $y8 = $y8/$geshu;
        $x8 = $x8/$geshu;

        $b = ($xy-$geshu*$y8*$x8)/($x2-$geshu*$x8*$x8);
        $a = $y8-$b*$x8;
        $re['a'] = $a;
        $re['b'] = $b;
        return $re;
        //y = b * x + a
    }
    /*
     *定义转化坐标的函数
     *@param $x x坐标即$i
     *@param $y y坐标，即j
     *@param $b 线性回归方程的b参数
     */
    function getNewZuobiao($x,$y,$b){
        if($x == 0){
            if($y>0){
                $xianJiao = M_PI/2;
            }elseif($y<0){
                $xianJiao = -M_PI/2;
            }else{
                $p['x'] = 0;
                $p['y'] = 0;
                return $p;
            }
        }else{
            $xianJiao = atan($y/$x);
        }
        $jiao =$xianJiao-atan($b);
        $chang = sqrt($x*$x+$y*$y);
        $p['x'] = $chang*cos($jiao);
        $p['y'] = $chang*sin($jiao);
        return $p;
    }
    /*
     *对【单个】数字的二值化二维数组进行倾斜调整
     *@param  $singleArray  高*宽的二值数组（0,1）
     */
    function singleSlopeAdjust($singleErzhiArray){
        $slopeArray = array();
        $gao = count($singleErzhiArray);
        $chang = count($singleErzhiArray['0']);

        //初始化$slopeArray
        for($i=0;$i<$gao*4;$i++){
            for($j=0;$j<$chang*4;$j++){
                $slopeArray[$i][$j] = 0;
            }
        }

        //初始化中心坐标(是数组的下标)
        $centerXfoalt = ($gao-1)/2;
        $centerYfoalt = ($chang-1)/2;
        $centerX = ceil($centerXfoalt);
        $centerY = ceil($centerYfoalt);

        //初始化图片倾斜诶角度
        /*斜率的计算！！！！！，回归方程*/
        //从上到下扫描，计算中点，求得一串坐标（$i,$ava）
        for($i=0;$i<$gao;$i++){
            $Num = 0;
            $Amount = 0;
            for($j=0;$j<$chang;$j++){
                if($singleErzhiArray[$i][$j] == 1){
                    $Num = $Num+$j;
                    $Amount++;
                }
            }
            if($Amount == 0){
                $Ava[$i] = $chang/2;
            }else{
                $Ava[$i] = $Num/$Amount;
            }
        }


        //计算线性回归方程的b与a
        $zuo = array();
        for($j=0;$j<count($Ava);$j++){
            $zuo[$j]['x'] = $j;
            $zuo[$j]['y'] = $Ava[$j];
        }
        $res = $this->getHuigui($zuo);
        $zuoB = $res['b'];


        for($i=0;$i<$gao;$i++){
            for($j=0;$j<$chang;$j++){
                if($singleErzhiArray[$i][$j] == 1){
                    $splodeZuobiao = $this->getNewZuobiao($i,$j,$zuoB);
                    $splodeX = $splodeZuobiao['x'];
                    $splodeY = $splodeZuobiao['y'];
                    $slopeArray[$splodeX+$gao][$splodeY+$chang] = 1;
                }
            }
        }

        //将预处理的数组空白清理
        $slopeArray = $this->getJinsuo($slopeArray);
        return $slopeArray;
    }
    /*
     *进行匹配
     *@param  $Image  图片路径
     */
    public function run($Image){
        $data = array('','','','');
        $result="";
        $bilu = '';
        $maxarr = '';

        //提取特征
        $this->prepare($Image);
        $yuanshi = $this->getErzhi();
        $yijijiangzao = $this->reduceZao($yuanshi);
        $small = $this->cutSmall($yijijiangzao);
        for($k=0;$k<4;$k++){
            $tianchong = $this->tianChong($small[$k]);
            $tiaozhenjiaodu = $this->singleSlopeAdjust($tianchong);
            $tongyidaxiao = $this->tongyiDaxiao($tiaozhenjiaodu);
            for($i=0;$i<20;$i++){
                for($j=0;$j<20;$j++){
                    $data[$k] .= $tongyidaxiao[$i][$j];
                }
            }
        }

        // 进行关键字匹配
        foreach($data as $numKey => $numString)
        {
            $max = 0;
            $num = 0;
            foreach($this->Keys as $value => $key)
            {
                similar_text($value, $numString,$percent);
                if($percent > $max)
                {
                    $max = $percent;
                    $num = $key;
                    $zim = $value;
                }
                if($max>95){
                    break;
                }
            }
            $result .=$num;
            $maxarr[] = $max;
        }
        // 查找最佳匹配数字
        $re = $maxarr;
        $re[] = $result;
        return $re;
        //return $result.'|max|一:'.$maxarr['0'].'|二:'.$maxarr['1'].'|三:'.$maxarr['2'].'|四:'.$maxarr['3'];
    }
    
}