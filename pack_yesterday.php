<?php
        //打包接口
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        date_default_timezone_set('PRC');
        $YMD=date('Ymd',strtotime("-1 day")); 
        $YMDf=date('Y-m-d',strtotime("-1 day")); 
        $con = mysql_connect("com","","s");
        mysql_query("set names 'utf8'");//编码转化
        $database='hbdb';
        $data=array();
        $size_data=array(
            '4' =>array('S'=>array('width'=>879,'height'=>1162),'M'=>array('width'=>907,'height'=>1191),'L'=>array('width'=>935,'height'=>1247),'XL'=>array('width'=>964,'height'=>1276)),
            '5' =>array('S'=>array('width'=>723,'height'=>964),'M'=>array('width'=>765,'height'=>1006),'L'=>array('width'=>794,'height'=>1049),'XL'=>array('width'=>850,'height'=>1134))
         );
        if(!$con)
        {
            die('Could not connect: ' . mysql_error());
        }else{
            $db_selecct=mysql_select_db($database);//选择数据库
            if(!$db_selecct)
            {
                die("could not to the database</br>".mysql_error());    
            }
           $query="SELECT a.id,b.diy_picture_path,b.id as updateid,c.cate_id,d.attri_name FROM hb_orders a,hb_orders_goods b,hb_goods c,hb_goods_attri d WHERE a.id=b.order_id AND a.pay_time >='".$YMD."' AND a.pay_time<'".date('Y-m-d',time())."' AND a.order_state=3 AND a.is_delete=0 AND b.goods_id=c.id AND b.goods_attrisid=d.id AND c.cate_id in(4,5)";
           $result=mysql_query($query);//执行查询
            if(!$result)
            {
                    die("could not to the database</br>".mysql_error());

            }
            $i=0;
            while($result_row=mysql_fetch_row($result))
            {
                $data[$i]['id']=$result_row[0];
                $data[$i]['updateid']=$result_row[2];
                $data[$i]['cate_id']=$result_row[3];
                $data[$i]['attri_name']=$result_row[4];
                $data[$i]['diy_picture_path']=$result_row[1];
                $i++;
            }
        }
        
        $path_pre="/export/app/images.hbdiy.net";
        //$path_pre="/alidata/www/testmobile.hbdiy.com.cn/Public";
        if (!file_exists($path_pre.'/Uploads/Uploads/'.$YMD)) {
            chmod($path_pre.'/Uploads/',0777);
            mkdir($path_pre.'/Uploads/Uploads/');
            if(!mkdir($path_pre.'/Uploads/Uploads/'.$YMD)){
                echo "创建文件目录失败！";
            }
         }

        foreach ($data as $k => $v) {
            if($v['diy_picture_path']){
                if (!file_exists($path_pre.'/Uploads'.$v['diy_picture_path'])) {
                    $size_arr=$size_data[$v['cate_id']][$v['attri_name']];
                    if(!file_exists($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'])){
                        mkdir($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id']);
                        mkdir($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name']);
                         echo "创建文件目录成功！"; 
                    }
                    $im = new Imagick($path_pre.$v['diy_picture_path']);
                    $im->thumbnailImage($size_arr['width'], $size_arr['height'], true); /* 改变大小 */
                    $im->setInterlaceScheme(4);
                    $lspath=basename($v['diy_picture_path']);
                    $lspatharr=explode(".",$lspath);
                    $am = $im->writeImage($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                    if(!$am){
                        echo "图片压缩失败！";
                    }else{
                     $im_get=new \Imagick($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                     $im_sta=$im_get->getImageColorspace();
                     if(2==$im_sta){
                         list($bg_width,$bg_height)=getimagesize($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                         $bg_width -=2;
                         $bg_height -=2;
                         $imm= new \Imagick();
                         $imm->newImage(2,2,new \ImagickPixel('#FFFFFE'));
                         $im_get->setImageColorSpace(1);
                         $dww = new \ImagickDraw();
                         $dww->setGravity(5);
                         $dww->setFillOpacity(0.1);
                         $dww->composite($imm->getImageCompose(), -$bg_width/2, $bg_height/2, 0, 0, $imm);
                         $im_get->drawImage($dww);
                         $im_get->writeImage($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1]);
                     }
                      $im_get->clear();
                      $im_get->destroy();
                    $query_update="UPDATE hb_orders_goods SET diy_print_path='/Uploads/".$YMD."/".$v['cate_id']."/".$v['attri_name']."/".$lspatharr[0]."-".$v['updateid'].".".$lspatharr[1]."' WHERE id=".$v['updateid'];
                    $result=mysql_query($query_update);
                    $filename = $path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1];
                    $file = file_get_contents($filename);
                        //数据块长度为9
                    $len = pack("N", 9);
                    //数据块类型标志为pHYs
                    $sign = pack("A*", "pHYs");
                    //X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）
                    $data = pack("NNC", 72 * 39.37, 72 * 39.37, 0x01);
                    //CRC检验码由数据块符号和数据域计算得到
                    $checksum = pack("N", crc32($sign . $data));
                    $phys = $len . $sign . $data . $checksum;
                    $pos = strpos($file, "pHYs");
                    if ($pos > 0) {
                    //修改pHYs数据块
                    $file = substr_replace($file, $phys, $pos - 4, 21);
                    } else {
                    //IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）
                    $pos = 33;
                    //将pHYs数据块插入到IHDR之后
                    $file = substr_replace($file, $phys, $pos, 0);
                    }
                         $filedata = fopen($path_pre.'/Uploads/Uploads/'.$YMD.'/'.$v['cate_id'].'/'.$v['attri_name'].'/'.$lspatharr[0].'-'.$v['updateid'].'.'.$lspatharr[1],"w");//打开文件准备写入 
                         fwrite($filedata,$file);//写入 
                         fclose($filedata);//关闭
                         echo "图片压缩成功！";
                    }
                    $im->clear();
                    $im->destroy();
                /*if (!copy($path_pre.$v['diy_picture_path'],$path_pre.'/Uploads'.$v['diy_picture_path'])) {
                    echo "复制文件失败！";
                }*/



            }
            }
        }

       mysql_close($con);
        if(is_readable($path_pre.'/Uploads/Uploads/'.$YMD)){  
            $cmd="cd ".$path_pre."/Uploads/;zip -r ".$path_pre."/Uploads/".$YMDf.".zip  ./Uploads/".$YMD;  
            $result=exec($cmd); 
            echo '打包成功';
        }
            $file=$path_pre."/Uploads/".$YMDf.".zip";
            $fp = fopen($file, 'r');
            $file2=$YMDf.".zip";
            // 连接FTP
            $conn_id = ftp_connect("182.92.174.176") or die("Could not connect");
            $login_result = ftp_login($conn_id, "gongchang", "gongchang123456");

            // 上传
            if(ftp_fput($conn_id, $file2, $fp, FTP_BINARY)) {
                echo "Successfully uploaded $file2";
            } else {
                echo "There was a problem while uploading $file2\n";
            }

            // 退出
            ftp_close($conn_id);
            fclose($fp);          
        
?>
